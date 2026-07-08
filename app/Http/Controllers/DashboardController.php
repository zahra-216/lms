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

        $onlineUsers = Student::where('course_id', $student->course_id)
            ->where('level_id', $student->level_id)
            ->where('id', '!=', $student->id)
            ->where('last_seen_at', '>=', Carbon::now()->subMinutes(5))
            ->get();

        return view('dashboard', compact(
            'student',
            'course',
            'level',
            'semesters',
            'onlineUsers'
        ));
    }

    // 👥 AJAX ONLINE USERS ONLY
    public function onlineUsers()
    {
        if (!Session::has('student_id')) {
            return response()->json([]);
        }

        $studentId = Session::get('student_id');

        $student = Student::find($studentId);

        if (!$student) {
            return response()->json([]);
        }

        return Student::where('course_id', $student->course_id)
            ->where('level_id', $student->level_id)
            ->where('id', '!=', $student->id)
            ->where('last_seen_at', '>=', Carbon::now()->subMinutes(5))
            ->get(['id', 'name', 'registration_no']);
    }

    // 📚 MY COURSES
    public function myCourses()
    {
        if (!Session::has('student_id')) {
            return redirect()->route('login');
        }

        $student = Student::with(['course', 'level'])
            ->find(Session::get('student_id'));

        if (!$student) {
            return redirect()->route('login');
        }

        $semesters = Semester::where('level_id', $student->level_id)->get();

        return view('student.my-courses', compact('student', 'semesters'));
    }
}