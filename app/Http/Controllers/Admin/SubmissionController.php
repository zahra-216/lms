<?php

namespace App\Http\Controllers\Admin;

use App\Models\Assignment;
use App\Models\Submission;
use App\Services\NotificationService;
use App\Events\SubmissionReceived;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    use AuthorizesRequests;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // List submissions for an assignment
    public function index(Request $request, Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $query = $assignment->submissions()->with(['user','gradedBy']);

        if ($request->query('user_id')) $query->where('user_id',$request->query('user_id'));

        if ($request->query('graded') !== null) {
            if ($request->boolean('graded')) {
                $query->whereNotNull('grade');
            } else {
                $query->whereNull('grade');
            }
        }

        if ($request->query('late') !== null) $query->where('is_late',$request->boolean('late'));

        return response()->json($query->orderBy('submitted_at','desc')->paginate(20));
    }

    // Submit assignment
    public function store(Request $request, Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $user = $request->user();

        // Check enrollment (ensure Subject model has users() relationship)
        if (!$assignment->subject->users()->where('user_id',$user->id)->exists()) {
            return response()->json(['message'=>'You are not enrolled'], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'content' => ['nullable','string'],
            'file_path' => ['nullable','string','max:255'],
            'file_name' => ['nullable','string','max:255'],
            'file_size' => ['nullable','integer','min:0'],
        ]);

        // Check late submission
        $isLate = now()->gt($assignment->due_date);
        if ($isLate && !$assignment->allow_late) {
            return response()->json(['message'=>'Late submissions not allowed'], Response::HTTP_BAD_REQUEST);
        }

        // Prevent overwrite if graded
        $existing = Submission::where('assignment_id',$assignment->id)
            ->where('user_id',$user->id)
            ->first();

        if ($existing && $existing->graded_at) {
            return response()->json(['message'=>'Cannot resubmit after grading'], Response::HTTP_FORBIDDEN);
        }

        $submission = $existing 
            ? tap($existing)->update(array_merge($validated,['is_late'=>$isLate,'submitted_at'=>now()]))
            : $assignment->submissions()->create(array_merge($validated,['user_id'=>$user->id,'is_late'=>$isLate,'submitted_at'=>now()]));

        // Notifications
        $this->notificationService->notifySubmissionReceived($submission);

        // Broadcast event
        event(new SubmissionReceived($submission));

        return response()->json($submission->load(['user','assignment']), Response::HTTP_CREATED);
    }

    // Grade a submission
    public function grade(Request $request, Submission $submission)
    {
        $this->authorize('update', $submission->assignment);

        $validated = $request->validate([
            'grade' => ['required','numeric','min:0','max:100'],
            'feedback' => ['nullable','string'],
        ]);

        $finalGrade = $validated['grade'];
        if ($submission->is_late) {
            $finalGrade = max(0, $finalGrade - $submission->assignment->late_penalty);
        }

        $submission->update([
            'grade' => $finalGrade,
            'feedback' => $validated['feedback'] ?? null,
            'graded_at' => now(),
            'graded_by' => $request->user()->id,
        ]);

        return response()->json([
            'submission'=>$submission->fresh()->load(['user','gradedBy']),
            'message'=>'Submission graded'
        ]);
    }
}