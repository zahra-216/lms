<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Faculty;
use App\Models\Course;
use App\Models\Level;
use App\Models\Student;

class FrontendController extends Controller
{
    public function home()
    {
        $faculties = Faculty::all();
        return view('frontend.home', compact('faculties'));
    }

    public function facultyCourses($id)
    {
        $faculty = Faculty::with(['courses.levels.semesters.subjects'])->findOrFail($id);
        return view('faculty.courses', compact('faculty'));
    }

    public function courseLevels($id)
    {
        $course = Course::with('levels')->findOrFail($id);
        return view('frontend.levels', compact('course'));
    }

    // ================= LOGIN PAGE =================
public function loginPage(Request $request)
{
    if ($request->filled('course_id')) {
        Session::put('selected_course', $request->course_id);
    }

    if ($request->filled('level_id')) {
        Session::put('selected_level', $request->level_id);
    }

    return view('auth.login');
}

    // Note: student registration is disabled here. Students are created by admin.
    

    // ================= LOGIN =================

    public function login(Request $request)
    {
        $request->validate([
            'registration_no' => 'required',
            'password' => 'required',
        ]);

        // Lookup only by registration number (admin-provisioned students)
        $student = Student::where('registration_no', $request->registration_no)->first();

        if (! $student) {
            return back()->withErrors([
                'registration_no' => 'Registration Number not found.'
            ]);
        }

        // Passwords are stored hashed. Verify with Hash::check against the stored hash.
        if (! Hash::check($request->password, $student->password)) {
            return back()->withErrors([
                'password' => 'Incorrect Password.'
            ]);
        }

        // Selected Course & Level
      // Selected Course & Level (Optional)
$courseId = Session::get('selected_course');
$levelId  = Session::get('selected_level');

// Check only if Course & Level are selected
if ($courseId && $levelId) {

    if ($student->course_id != $courseId) {
        return back()->withErrors([
            'registration_no' => 'You are not enrolled in this course.'
        ]);
    }

    if ($student->level_id != $levelId) {
        return back()->withErrors([
            'registration_no' => 'You are not enrolled in this level.'
        ]);
    }
}

        // Update only online time
        $student->update([
            'last_seen_at' => now()
        ]);

        // Login Session
        Session::put('student_id', $student->id);
        Session::put('student_name', $student->name);
        Session::put('course_id', $student->course_id);
        Session::put('level_id', $student->level_id);

        return redirect()->route('dashboard');
    }
}