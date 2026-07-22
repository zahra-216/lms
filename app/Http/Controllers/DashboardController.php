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

        $semesters = Semester::where('level_id', $student->level_id)->get();

        return view('dashboard', compact(
            'student',
            'course',
            'level',
            'semesters',
        ));
    }

}