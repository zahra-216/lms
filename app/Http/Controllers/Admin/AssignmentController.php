<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Level;
use App\Models\Subject;
use App\Events\AssignmentCreated;
use App\Notifications\AssignmentSubmitted;
use App\Notifications\AssignmentSubmissionConfirmed;

class AssignmentController extends Controller
{
    // LIST
    public function index()
    {
        $assignments = Assignment::with('subject')->paginate(20);
        return view('admin.assignments.index', compact('assignments'));
    }

    // SUBMIT
    public function submit(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required',
            'file' => 'required|file'
        ]);

        $studentId = session('student_id');

        if (!$studentId) {
            return back()->with('error', 'Login required');
        }

        $exists = AssignmentSubmission::where([
            'assignment_id' => $request->assignment_id,
            'student_id' => $studentId
        ])->first();

        if ($exists) {
            return back()->with('error', 'Already submitted');
        }

        $filePath = $request->file('file')->store('submissions', 'public');

        AssignmentSubmission::create([
            'assignment_id' => $request->assignment_id,
            'student_id' => $studentId,
            'file' => $filePath,
            'submitted_at' => now()
        ]);

        $student = Student::find($studentId);
        $assignment = Assignment::find($request->assignment_id);

        // Admin gets in-app notification only (no email)
        foreach (Admin::all() as $admin) {
            try {
                $admin->notify(new AssignmentSubmitted($student, $assignment));
            } catch (\Exception $e) {
                \Log::error('Failed to notify admin #' . $admin->id . ' (' . $admin->email . '): ' . $e->getMessage());
            }
        }

        // Student gets email confirmation
        try {
            $student->notify(new \App\Notifications\AssignmentSubmissionConfirmed($assignment));
        } catch (\Exception $e) {
            \Log::error('Failed to email student #' . $student->id . ' (' . $student->email . '): ' . $e->getMessage());
        }

        return back()->with('success', 'Submitted!');
    }

    public function create(\App\Models\Subject $subject)
    {
        return view('admin.subjects.assignments-create', compact('subject'));
    }

    public function store(Request $request, \App\Models\Subject $subject)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'total_points' => 'nullable|numeric|min:0',
            'submission_type' => 'nullable|string',
            'allow_late' => 'nullable|boolean',
            'late_penalty' => 'nullable|numeric|min:0|max:100',
            'is_published' => 'nullable|boolean',
            'assignment_file' => 'nullable|file|max:10240',
        ]);

        $data['subject_id'] = $subject->id;
        $data['allow_late'] = $request->boolean('allow_late');
        $data['is_published'] = $request->boolean('is_published', true);

        if ($request->hasFile('assignment_file')) {
            $data['file_path'] = $request->file('assignment_file')->store('assignments', 'public');
        }
        unset($data['assignment_file']);

        Assignment::create($data);

        return redirect()->route('admin.subjects.assignments.index', $subject->id)
            ->with('success', 'Assignment created!');
    }

    // SUBJECT-SCOPED LIST (mirrors lecturer view)
    public function subjectIndex(\App\Models\Subject $subject)
    {
        $subject->load('assignments.submissions.student');
        return view('admin.subjects.assignments', compact('subject'));
    }

    public function edit(\App\Models\Subject $subject, Assignment $assignment)
    {
        return view('admin.assignments.edit', compact('subject', 'assignment'));
    }

    public function update(Request $request, \App\Models\Subject $subject, Assignment $assignment)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'total_points' => 'nullable|numeric|min:0',
            'submission_type' => 'nullable|string',
            'allow_late' => 'nullable|boolean',
            'late_penalty' => 'nullable|numeric|min:0|max:100',
            'is_published' => 'nullable|boolean',
            'assignment_file' => 'nullable|file|max:10240',
        ]);

        $data['allow_late'] = $request->boolean('allow_late');
        $data['is_published'] = $request->boolean('is_published', true);

        if ($request->hasFile('assignment_file')) {
            $data['file_path'] = $request->file('assignment_file')->store('assignments', 'public');
        } else {
            unset($data['file_path']);
        }
        unset($data['assignment_file']);

        $assignment->update($data);

        return redirect()->route('admin.subjects.assignments.index', $subject->id)
            ->with('success', 'Assignment updated!');
    }

    public function destroy(\App\Models\Subject $subject, Assignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('admin.subjects.assignments.index', $subject->id)
            ->with('success', 'Assignment deleted!');
    }

    // SUBMISSIONS
    public function submissions($id)
    {
        $assignment = Assignment::with('submissions.student')
            ->findOrFail($id);

        return view('admin.assignments.submissions', compact('assignment'));
    }
}