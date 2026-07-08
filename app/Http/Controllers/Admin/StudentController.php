<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Mark;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'registration_no' => 'required|unique:students,registration_no',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'branch' => 'required|string|max:255',

            // 🔥 NEW FIELDS
            'course_id' => 'required|integer',
            'level_id' => 'required|integer',

            'password' => 'required|string|min:4',
        ]);

        Student::create([
            'registration_no' => $request->registration_no,
            'name' => $request->name,
            'email' => $request->email,
            'branch' => $request->branch,

            // 🔥 NEW FIELDS
            'course_id' => $request->course_id,
            'level_id' => $request->level_id,

            // 🔥 secure password
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student added successfully!');
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'registration_no' => 'required|unique:students,registration_no,' . $student->id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'branch' => 'required|string|max:255',

            // 🔥 NEW FIELDS
            'course_id' => 'required|integer',
            'level_id' => 'required|integer',

            'password' => 'required|string|min:4',
        ]);

        $student->update([
            'registration_no' => $request->registration_no,
            'name' => $request->name,
            'email' => $request->email,
            'branch' => $request->branch,

            // 🔥 NEW FIELDS
            'course_id' => $request->course_id,
            'level_id' => $request->level_id,

            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully!');
    }

    public function destroy($id)
    {
        Student::findOrFail($id)->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted!');
    }
    public function notes($id)
{
    $subject = Subject::findOrFail($id);

    $notes = Note::where('subject_id', $id)->get();

    return view('student.notes', compact('subject', 'notes'));
}




public function profile()
{
    $student = auth()->guard('student')->user();

    if (!$student) {
        return redirect()->route('login');
    }

    return view('student.profile', compact('student'));
}




public function updatePhoto(Request $request)
{
    $request->validate([
        'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $studentId = session('student_id');

    if (!$studentId) {
        return redirect()->route('login');
    }

    $student = Student::find($studentId);

    if (!$student) {
        return redirect()->route('login');
    }

    // delete old photo
    if ($student->photo && Storage::disk('public')->exists($student->photo)) {
        Storage::disk('public')->delete($student->photo);
    }

    // upload new photo
    $path = $request->file('photo')->store('students', 'public');

    $student->update([
        'photo' => $path
    ]);

    return back()->with('success', 'Profile photo updated successfully!');
}





}