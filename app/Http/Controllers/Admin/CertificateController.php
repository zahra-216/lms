<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $students = collect();

        if ($search) {
            $students = Student::withCount('certificates')
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('registration_no', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->get();
        }

        return view('admin.certificates.index', compact('students', 'search'));
    }

    public function studentCertificates(Student $student)
    {
        $certificates = $student->certificates()->latest()->get();
        return view('admin.certificates.student', compact('student', 'certificates'));
    }

    public function create(Student $student)
    {
        return view('admin.certificates.create', compact('student'));
    }

    public function store(Request $request, Student $student)
    {
        $validated = $request->validate([
            'certificate_number' => 'required|string|max:50|unique:certificates,certificate_number',
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'course' => 'required|string|max:255',
            'course_start' => 'required|date',
            'course_end' => 'required|date|after_or_equal:course_start',
            'award_status' => 'required|in:Distinction,Merit,Pass',
            'photo' => 'required|image|max:2048',
        ]);

        $validated['student_id'] = $student->id;

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('certificates', 'public');
        }

        Certificate::create($validated);

        return redirect()->route('admin.certificates.student', $student->id)
            ->with('success', 'Certificate added successfully.');
    }

    public function edit(Certificate $certificate)
    {
        $student = $certificate->student;
        return view('admin.certificates.edit', compact('certificate', 'student'));
    }

    public function update(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'certificate_number' => 'required|string|max:50|unique:certificates,certificate_number,' . $certificate->id,
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'course' => 'required|string|max:255',
            'course_start' => 'required|date',
            'course_end' => 'required|date|after_or_equal:course_start',
            'award_status' => 'required|in:Distinction,Merit,Pass',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($certificate->photo) {
                Storage::disk('public')->delete($certificate->photo);
            }
            $validated['photo'] = $request->file('photo')->store('certificates', 'public');
        }

        $certificate->update($validated);

        return redirect()->route('admin.certificates.student', $certificate->student_id)
            ->with('success', 'Certificate updated successfully.');
    }

    public function destroy(Certificate $certificate)
    {
        if ($certificate->photo) {
            Storage::disk('public')->delete($certificate->photo);
        }
        $studentId = $certificate->student_id;
        $certificate->delete();

        return redirect()->route('admin.certificates.student', $studentId)
            ->with('success', 'Certificate deleted successfully.');
    }
}