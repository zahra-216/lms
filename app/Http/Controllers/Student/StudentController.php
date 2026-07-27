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
        $currentSemester = \App\Models\Semester::find($student->semester_id);

        if (!$currentSemester) {
            $semesterGroups = collect();
            return view('student.grades', compact('semesterGroups', 'student'));
        }

        // extract the number from "Semester 3" -> 3
        $currentNumber = (int) filter_var($currentSemester->name, FILTER_SANITIZE_NUMBER_INT);

        $semesters = \App\Models\Semester::where('course_id', $student->course_id)
            ->where('level_id', $student->level_id)
            ->get()
            ->filter(function ($sem) use ($currentNumber) {
                $num = (int) filter_var($sem->name, FILTER_SANITIZE_NUMBER_INT);
                return $num <= $currentNumber;
            })
            ->sortByDesc(function ($sem) {
                return (int) filter_var($sem->name, FILTER_SANITIZE_NUMBER_INT);
            });

        $subjects = Subject::with(['subjectMarks' => function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        }])
        ->whereIn('semester_id', $semesters->pluck('id'))
        ->get()
        ->groupBy('semester_id');

        // build an ordered collection: semester object + its subjects
        $semesterGroups = $semesters->map(function ($sem) use ($subjects) {
            return [
                'semester' => $sem,
                'subjects' => $subjects->get($sem->id, collect()),
            ];
        });

        return view('student.grades', compact('semesterGroups', 'student'));
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