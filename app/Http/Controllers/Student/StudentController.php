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
        $studentId = session('student_id');

        if (!$studentId) {
            return redirect('/login');
        }

        $student = \App\Models\Student::find($studentId);

        $subjects = Subject::with(['subjectMarks' => function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        }])
        ->where('semester_id', $student->semester_id)
        ->get();

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

    public function myPayments()
    {
        $studentId = session('student_id');

        if (!$studentId) {
            return redirect('/login');
        }

        $student = \App\Models\Student::find($studentId);
        $plan = \App\Models\PaymentPlan::where('student_id', $studentId)->first();
        $payments = \App\Models\StudentPayment::where('student_id', $studentId)->latest('date')->get();
        $totalPaid = $payments->sum('amount');

        return view('student.my-payments', compact('student', 'plan', 'payments', 'totalPaid'));
    }
}