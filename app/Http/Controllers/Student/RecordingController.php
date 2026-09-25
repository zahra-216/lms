<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Recording;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class RecordingController extends Controller
{
    private function currentStudent()
    {
        return Student::find(session('student_id'));
    }

    // List recordings within one subject
    public function subject($subjectId)
    {
        $student = $this->currentStudent();
        if (!$student) {
            return redirect()->route('login')->with('error', 'Please log in again');
        }

        $subject = Subject::where('id', $subjectId)
            ->where('course_id', $student->course_id)
            ->where('level_id', $student->level_id)
            ->where('semester_id', $student->semester_id)
            ->firstOrFail();

        $recordings = Recording::where('subject_id', $subject->id)->latest()->get();

        return view('student.recordings.subject', compact('subject', 'recordings'));
    }

    // Show + play one recording
    public function show($id)
    {
        $student = $this->currentStudent();
        if (!$student) {
            return redirect()->route('login')->with('error', 'Please log in again');
        }

        if ($student->status !== 'active') {
            abort(403, 'Your access to lecture recordings has been disabled.');
        }

        $recording = Recording::where('id', $id)->firstOrFail();
        $subject = $recording->subject;

        // Make sure this recording belongs to the student's own course/level/semester
        if (
            $subject->course_id !== $student->course_id ||
            $subject->level_id !== $student->level_id ||
            $subject->semester_id !== $student->semester_id
        ) {
            abort(403, 'You do not have access to this recording.');
        }

        return view('student.recordings.show', compact('recording', 'subject'));
    }
}