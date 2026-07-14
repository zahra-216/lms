<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StudentSubjectPortalController extends Controller
{
    private function currentStudent()
    {
        $id = Session::get('student_id');
        return $id ? Student::find($id) : null;
    }

    public function show($id)
    {
        $student = $this->currentStudent();
        if (!$student) return redirect()->route('login');

        $subject = Subject::findOrFail($id);

        return view('student.subject.show', compact('subject', 'student'));
    }

    public function notes($id)
    {
        $student = $this->currentStudent();
        if (!$student) return redirect()->route('login');

        $subject = Subject::with('notes')->findOrFail($id);

        return view('student.subject.notes', compact('subject', 'student'));
    }

    public function videos($id)
    {
        $student = $this->currentStudent();
        if (!$student) return redirect()->route('login');

        $subject = Subject::with('videos')->findOrFail($id);

        return view('student.subject.videos', compact('subject', 'student'));
    }

    public function assignments($id)
    {
        $student = $this->currentStudent();
        if (!$student) return redirect()->route('login');

        $subject = Subject::with(['assignments' => function ($q) use ($student) {
            $q->with(['submissions' => function ($sq) use ($student) {
                $sq->where('student_id', $student->id);
            }]);
        }])->findOrFail($id);

        return view('student.subject.assignments', compact('subject', 'student'));
    }

    public function grades($id)
    {
        $student = $this->currentStudent();
        if (!$student) return redirect()->route('login');

        $subject = Subject::with(['assignments.marks' => function ($q) use ($student) {
            $q->where('student_id', $student->id);
        }])->findOrFail($id);

        return view('student.subject.grades', compact('subject', 'student'));
    }
}