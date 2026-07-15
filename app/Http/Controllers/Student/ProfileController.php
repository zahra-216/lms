<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // SHOW PROFILE
    public function index()
    {
        if (!session()->has('student_id')) {
            return redirect()->route('login');
        }

        $student = Student::find(session('student_id'));

        if (!$student) {
            return redirect()->route('login')->with('error', 'Student not found');
        }

        return view('student.profile', compact('student'));
    }

    // UPDATE PHOTO
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $student = Student::find(session('student_id'));

        if (!$student) {
            return back()->with('error', 'Student not found');
        }

        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $path = $request->file('photo')->store('students', 'public');

        $student->update([
            'photo' => $path
        ]);

        return back()->with('success', 'Profile photo updated!');
    }

    // EDIT PROFILE FORM
    public function edit()
    {
        if (!session()->has('student_id')) {
            return redirect()->route('login');
        }

        $student = Student::find(session('student_id'));

        if (!$student) {
            return redirect()->route('login');
        }

        return view('student.profile-edit', compact('student'));
    }

    // UPDATE PROFILE
    public function update(Request $request)
    {
        $student = Student::find(session('student_id'));

        if (!$student) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $student->update($validated);

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }
}