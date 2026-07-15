<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Mark;
use App\Models\Course;
use App\Models\Level;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('admin.students.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'registration_no' => strtoupper($request->registration_no),
        ]);
        $request->validate([
            'registration_no' => [
                'required',
                'unique:students,registration_no',
                'regex:/^TTMC\/ML\/[A-Za-z]{2,3}\/[A-Za-z]{2,4}(-O)?\/\d{2}\/\d{2,4}$/',
            ],
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'branch' => 'required|string|max:255',
            'course_id' => 'required|integer',
            'level_id' => 'required|integer',
            'password' => 'required|string|min:15',

            // 🔥 NEW: optional photo
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'registration_no.regex' => 'Registration No must match format: TTMC/ML/XX/CSE-O or CSE/YY/NN (e.g. TTMC/ML/UG/CSE-O/25/03)',
        ]);
        $data = [
            'registration_no' => $request->registration_no,
            'name' => $request->name,
            'email' => $request->email,
            'branch' => $request->branch,
            'course_id' => $request->course_id,
            'level_id' => $request->level_id,
            'password' => Hash::make($request->password),
        ];

        // 🔥 NEW: handle optional photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students', 'public');
        }

        Student::create($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student added successfully!');
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $courses = Course::all();
        $levels = Level::where('course_id', $student->course_id)->get();
        return view('admin.students.edit', compact('student', 'courses', 'levels'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->merge([
            'registration_no' => strtoupper($request->registration_no),
        ]);

        $request->validate([
            'registration_no' => [
                'required',
                'unique:students,registration_no,' . $student->id,
                'regex:/^TTMC\/ML\/[A-Za-z]{2,3}\/[A-Za-z]{2,4}(-O)?\/\d{2}\/\d{2,4}$/',
            ],
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'branch' => 'required|string|max:255',
            'course_id' => 'required|integer',
            'level_id' => 'required|integer',

            // 🔥 password removed from validation entirely — not editable here
        ], [
            'registration_no.regex' => 'Registration No must match format: TTMC/ML/XX/CSE-O or CSE/YY/NN (e.g. TTMC/ML/UG/CSE-O/25/03)',
        ]);

        $student->update([
            'registration_no' => $request->registration_no,
            'name' => $request->name,
            'email' => $request->email,
            'branch' => $request->branch,
            'course_id' => $request->course_id,
            'level_id' => $request->level_id,

            // 🔥 password intentionally NOT touched here
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

        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        $path = $request->file('photo')->store('students', 'public');

        $student->update([
            'photo' => $path
        ]);

        return back()->with('success', 'Profile photo updated successfully!');
    }
}