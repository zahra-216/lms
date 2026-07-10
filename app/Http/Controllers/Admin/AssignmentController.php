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

class AssignmentController extends Controller
{
    // LIST
    public function index()
    {
        $assignments = Assignment::with('subject')->paginate(20);
        return view('admin.assignments.index', compact('assignments'));
    }

    // CREATE
    public function create()
    {
        $courses = Course::all();
        $levels = Level::all();
        $subjects = Subject::all();

        return view('admin.assignments.create', compact('courses', 'levels', 'subjects'));
    }

    // STORE
    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
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
        }

        unset($data['assignment_file']);

        $assignment = Assignment::create($data);

        $studentIds = Student::whereHas('subjects', function ($q) use ($data) {
            $q->where('subjects.id', $data['subject_id']);
        })->pluck('id')->toArray();

        event(new AssignmentCreated($assignment, $studentIds));

        return redirect()->route('admin.assignments.index')->with('success', 'Assignment created!');
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

        foreach (Admin::all() as $admin) {
            $admin->notify(new AssignmentSubmitted($student, $assignment));
        }

        return back()->with('success', 'Submitted!');
    }

    // SUBMISSIONS
    public function submissions($id)
    {
        $assignment = Assignment::with('submissions.student')
            ->findOrFail($id);

        return view('admin.assignments.submissions', compact('assignment'));
    }
}