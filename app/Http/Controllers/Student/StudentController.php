<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\Mark;

class StudentController extends Controller
{
   public function grades()
{
    // ✅ USE SESSION (NOT AUTH GUARD)
    $studentId = session('student_id');

    if (!$studentId) {
        return redirect('/login'); // safety
    }

    $student = \App\Models\Student::find($studentId);

    $subjects = Subject::with(['assignments.marks' => function ($q) use ($studentId) {
        $q->where('student_id', $studentId);
    }])->get();

    return view('student.grades', compact('subjects', 'student'));
}

   public function subjectGrades($id)
{
    $studentId = session('student_id');

    if (!$studentId) {
        return redirect('/login');
    }

    $student = \App\Models\Student::find($studentId);

    $subject = Subject::with(['assignments.marks' => function ($q) use ($studentId) {
        $q->where('student_id', $studentId);
    }])->findOrFail($id);

    return view('student.subject-grades', compact('subject', 'student'));
}
}