<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Subject; // <-- add this

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalFaculty = Faculty::count();
        $totalCourses = Course::count();
        $totalEnrollments = Enrollment::count();
        $pendingEnrollments = Enrollment::where('status','pending')->count();

        $recentActivities = [
            'New student registered',
            'New course added',
            'Faculty assigned'
        ];

        $notifications = [
            'Pending enrollment approvals',
            'Assignment deadlines',
            'System alerts'
        ];

        // ✅ Add this line to get subjects
        $subjects = Subject::all();

        return view('admin.dashboard', compact(
            'totalStudents', 
            'totalFaculty', 
            'totalCourses', 
            'totalEnrollments',
            'pendingEnrollments',
            'recentActivities',
            'notifications',
            'subjects' // <-- pass it to the view
        ));
    }
}