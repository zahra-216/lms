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
        // 🔥 check login
        if (!session()->has('student_id')) {
            return redirect()->route('login');
        }

        $student = Student::find(session('student_id'));

        // 🔥 prevent null error
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

        // delete old photo
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        // upload new
        $path = $request->file('photo')->store('students', 'public');

        $student->update([
            'photo' => $path
        ]);

        return back()->with('success', 'Profile photo updated!');
    }
}