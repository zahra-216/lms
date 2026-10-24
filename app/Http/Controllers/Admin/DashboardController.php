<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\Subject;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            ['Students', Student::count(), 'people', route('admin.students.index')],
            ['Lecturers', Lecturer::count(), 'person-workspace', route('admin.lecturers.index')],
            ['Faculties', Faculty::count(), 'building', route('admin.faculties.index')],
            ['Courses', Course::count(), 'mortarboard', route('admin.courses.index')],
            ['Subjects', Subject::count(), 'book', route('admin.subjects.index')],
            ['Marked present today', Attendance::whereDate('date', today())->where('status', 'present')->count(), 'calendar-check', route('admin.attendance.index')],
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentStudents' => Student::with('course')->latest()->take(6)->get(),
            'newSubmissions' => AssignmentSubmission::where('created_at', '>=', now()->subDays(7))->count(),
        ]);
    }
}
