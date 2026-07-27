<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Models\Course;
use App\Models\Semester;
use App\Models\Student;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        if (!Session::has('student_id')) {
            return redirect()->route('login');
        }

        $studentId = Session::get('student_id');

        $student = Student::with(['course', 'level'])->find($studentId);

        if (!$student) {
            return redirect()->route('login');
        }

        $course = $student->course;
        $level  = $student->level;

        $currentSemester = Semester::find($student->semester_id);
        $currentNumber = $currentSemester
            ? (int) filter_var($currentSemester->name, FILTER_SANITIZE_NUMBER_INT)
            : null;

        $semesters = Semester::where('course_id', $student->course_id)
            ->where('level_id', $student->level_id)
            ->get()
            ->filter(function ($sem) use ($currentNumber) {
                if (!$currentNumber) return true;
                $num = (int) filter_var($sem->name, FILTER_SANITIZE_NUMBER_INT);
                return $num <= $currentNumber;
            })
            ->values();

        return view('dashboard', compact(
            'student',
            'course',
            'level',
            'semesters',
        ));
    }

}