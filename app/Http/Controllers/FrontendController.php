<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
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

        // Slow down password guessing: 5 tries, then a 2 minute wait (per registration number and address).
        $key = 'student-login:'.Str::lower((string) $request->registration_no).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'registration_no' => 'Too many attempts. Please try again in '.RateLimiter::availableIn($key).' seconds.',
            ])->onlyInput('registration_no');
        }

        $student = Student::where('registration_no', $request->registration_no)->first();

        // One message for both cases, so the form never reveals which registration numbers exist.
        if (! $student || ! Hash::check($request->password, $student->password)) {
            RateLimiter::hit($key, 120);

            return back()->withErrors([
                'registration_no' => 'Invalid registration number or password.',
            ])->onlyInput('registration_no');
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

        // Login Session (new session id after sign-in prevents session fixation)
        RateLimiter::clear($key);
        $request->session()->regenerate();
        Session::put('student_id', $student->id);
        Session::put('student_name', $student->name);
        Session::put('course_id', $student->course_id);
        Session::put('level_id', $student->level_id);

        return redirect()->route('dashboard');
    }
}