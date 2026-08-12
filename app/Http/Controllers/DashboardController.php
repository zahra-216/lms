<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Models\Course;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Assignment;
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

        $pendingAssignments = Assignment::with('subject')
            ->where('is_published', true)
            ->whereHas('subject', function ($q) use ($student) {
                $q->where('course_id', $student->course_id)
                ->where('level_id', $student->level_id)
                ->where('semester_id', $student->semester_id);
            })
            ->whereDoesntHave('submissions', function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            })
            ->orderBy('due_date')
            ->get();

        $upcomingClasses = \App\Models\Timetable::with('subject')
            ->whereHas('subject', function ($q) use ($student) {
                $q->where('course_id', $student->course_id)
                ->where('level_id', $student->level_id)
                ->where('semester_id', $student->semester_id);
            })
            ->get()
            ->sortBy(function ($t) {
                $today = now()->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
                $classDay = \App\Models\Timetable::DAY_ORDER[$t->day] ?? 8;
                $diff = $classDay - $today;
                if ($diff < 0) $diff += 7;
                return $diff * 1440 + (int) str_replace(':', '', substr($t->start_time, 0, 5));
            })
            ->values();

        return view('dashboard', compact(
            'student',
            'course',
            'level',
            'semesters',
            'pendingAssignments',
            'upcomingClasses'
        ));
    }

}