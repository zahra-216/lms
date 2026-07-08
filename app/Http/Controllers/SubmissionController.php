<?php

namespace App\Http\Controllers;

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

    public function index(Request $request, Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $query = $assignment->submissions()->with(['user', 'gradedBy']);

        // Filter by user if provided
        if ($request->query('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        // Filter by graded status
        if ($request->query('graded') !== null) {
            if ($request->boolean('graded')) {
                $query->whereNotNull('grade');
            } else {
                $query->whereNull('grade');
            }
        }

        // Filter by late submissions
        if ($request->query('late') !== null) {
            $query->where('is_late', $request->boolean('late'));
        }

        $submissions = $query->orderBy('submitted_at', 'desc')->paginate(20);

        return response()->json($submissions);
    }

    public function store(Request $request, Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $user = $request->user();

        // Check if user is enrolled in the course
        if (!$assignment->course->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'You are not enrolled in this course'
            ], Response::HTTP_FORBIDDEN);
        }

        // Check if submission already exists
        $existingSubmission = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingSubmission && $existingSubmission->graded_at) {
            return response()->json([
                'message' => 'Cannot submit after grading'
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'content' => ['nullable', 'string'],
            'file_path' => ['nullable', 'string', 'max:255'],
            'file_name' => ['nullable', 'string', 'max:255'],
            'file_size' => ['nullable', 'integer', 'min:0'],
        ]);

        // Check if assignment allows the submission type
        if ($assignment->submission_type === 'file' && !$validated['file_path']) {
            return response()->json([
                'message' => 'File submission is required for this assignment'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($assignment->submission_type === 'text' && !$validated['content']) {
            return response()->json([
                'message' => 'Text content is required for this assignment'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Check if assignment allows late submissions
        $isLate = now()->gt($assignment->due_date);
        if ($isLate && !$assignment->allow_late) {
            return response()->json([
                'message' => 'Late submissions are not allowed for this assignment'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Calculate grade penalty for late submission
        $grade = null;
        if ($isLate && $assignment->late_penalty > 0) {
            $grade = max(0, 100 - $assignment->late_penalty);
        }

        $submissionData = array_merge($validated, [
            'user_id' => $user->id,
            'is_late' => $isLate,
            'submitted_at' => now(),
        ]);

        if ($existingSubmission) {
            $existingSubmission->update($submissionData);
            $submission = $existingSubmission;
        } else {
            $submission = $assignment->submissions()->create($submissionData);
        }

        // Send notifications
        $this->notificationService->notifySubmissionReceived($submission);
        
        // Dispatch real-time event
        event(new SubmissionReceived($submission));

        return response()->json($submission->load(['user', 'assignment']), Response::HTTP_CREATED);
    }

    public function show(Submission $submission)
    {
        $user = request()->user();

        // Users can view their own submissions, or instructors can view all
        if ($user->id !== $submission->user_id && 
            $user->id !== $submission->assignment->course->instructor_id && 
            $user->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized to view this submission'
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json($submission->load(['user', 'assignment.course', 'gradedBy']));
    }

    public function update(Request $request, Submission $submission)
    {
        $user = $request->user();

        // Only allow updates if not graded and user owns the submission
        if ($submission->graded_at) {
            return response()->json([
                'message' => 'Cannot update graded submission'
            ], Response::HTTP_FORBIDDEN);
        }

        if ($user->id !== $submission->user_id) {
            return response()->json([
                'message' => 'Unauthorized to update this submission'
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'content' => ['nullable', 'string'],
            'file_path' => ['nullable', 'string', 'max:255'],
            'file_name' => ['nullable', 'string', 'max:255'],
            'file_size' => ['nullable', 'integer', 'min:0'],
        ]);

        $submission->update($validated);

        return response()->json($submission->fresh()->load(['user', 'assignment']));
    }

    public function destroy(Submission $submission)
    {
        $user = request()->user();

        // Only allow deletion if not graded and user owns the submission
        if ($submission->graded_at) {
            return response()->json([
                'message' => 'Cannot delete graded submission'
            ], Response::HTTP_FORBIDDEN);
        }

        if ($user->id !== $submission->user_id) {
            return response()->json([
                'message' => 'Unauthorized to delete this submission'
            ], Response::HTTP_FORBIDDEN);
        }

        // Delete associated file if exists
        if ($submission->file_path) {
            Storage::disk('public')->delete($submission->file_path);
        }

        $submission->delete();

        return response()->noContent();
    }

    public function grade(Request $request, Submission $submission)
    {
        $this->authorize('update', $submission->assignment);

        $validated = $request->validate([
            'grade' => ['required', 'numeric', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string'],
        ]);

        // Apply late penalty if applicable
        $finalGrade = $validated['grade'];
        if ($submission->is_late && $submission->assignment->late_penalty > 0) {
            $finalGrade = max(0, $validated['grade'] - $submission->assignment->late_penalty);
        }

        $submission->update([
            'grade' => $finalGrade,
            'feedback' => $validated['feedback'],
            'graded_at' => now(),
            'graded_by' => $request->user()->id,
        ]);

        // Send notification to student
        $this->notificationService->notifySubmissionGraded($submission);

        return response()->json([
            'submission' => $submission->fresh()->load(['user', 'gradedBy']),
            'message' => 'Submission graded successfully'
        ]);
    }

    public function bulkGrade(Request $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $validated = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.submission_id' => ['required', 'integer', 'exists:submissions,id'],
            'grades.*.grade' => ['required', 'numeric', 'min:0', 'max:100'],
            'grades.*.feedback' => ['nullable', 'string'],
        ]);

        $gradedCount = 0;

        foreach ($validated['grades'] as $gradeData) {
            $submission = $assignment->submissions()->find($gradeData['submission_id']);
            
            if ($submission) {
                // Apply late penalty if applicable
                $finalGrade = $gradeData['grade'];
                if ($submission->is_late && $assignment->late_penalty > 0) {
                    $finalGrade = max(0, $gradeData['grade'] - $assignment->late_penalty);
                }

                $submission->update([
                    'grade' => $finalGrade,
                    'feedback' => $gradeData['feedback'] ?? null,
                    'graded_at' => now(),
                    'graded_by' => $request->user()->id,
                ]);

                $gradedCount++;
            }
        }

        return response()->json([
            'message' => "Successfully graded {$gradedCount} submissions"
        ]);
    }

    public function exportGrades(Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $submissions = $assignment->submissions()
            ->with(['user'])
            ->orderBy('user.name')
            ->get();

        $grades = $submissions->map(function ($submission) {
            return [
                'student_name' => $submission->user->name,
                'student_email' => $submission->user->email,
                'submitted_at' => $submission->submitted_at?->toISOString(),
                'is_late' => $submission->is_late,
                'grade' => $submission->grade,
                'feedback' => $submission->feedback,
                'graded_at' => $submission->graded_at?->toISOString(),
            ];
        });

        return response()->json([
            'assignment_id' => $assignment->id,
            'assignment_title' => $assignment->title,
            'due_date' => $assignment->due_date->toISOString(),
            'total_points' => $assignment->total_points,
            'submissions_count' => $submissions->count(),
            'graded_count' => $submissions->whereNotNull('grade')->count(),
            'grades' => $grades,
            'exported_at' => now()->toISOString(),
        ]);
    }
}