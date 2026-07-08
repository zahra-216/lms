<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;


class EnrollmentController extends Controller
{
    // 📋 INDEX + FILTER
    public function index(Request $request)
    {
        $query = Enrollment::with(['student','course']);

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('enrolled_at', [
                $request->from_date,
                $request->to_date
            ]);
        }

        $enrollments = $query->latest()->get();

        $students = Student::all();
        $courses = Course::all();

        return view('admin.enrollments.index', compact('enrollments','students','courses'));
    }

    // 📄 PDF EXPORT (FINAL FIX)
    public function exportPdf(Request $request)
{
    $query = Enrollment::with(['student','course']);

    if ($request->student_id) {
        $query->where('student_id', $request->student_id);
    }

    if ($request->course_id) {
        $query->where('course_id', $request->course_id);
    }

    if ($request->status) {
        $query->where('status', $request->status);
    }

    if ($request->from_date && $request->to_date) {
        $query->whereBetween('enrolled_at', [
            $request->from_date,
            $request->to_date
        ]);
    }

    $enrollments = $query->get();

    // 🔥 SAFE DOMPDF (NO FACADE ISSUE)
    $pdf = app('dompdf.wrapper');

    $pdf->loadView('admin.enrollments.pdf', compact('enrollments'));

    return $pdf->download('enrollments-report.pdf');
}

    // 💾 STORE
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'course_id' => 'required',
        ]);

        $exists = Enrollment::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Already enrolled!');
        }

        Enrollment::create($request->all());

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment Added!');
    }

    // 🗑 DELETE
    public function destroy($id)
    {
        Enrollment::findOrFail($id)->delete();

        return back()->with('success', 'Deleted Successfully!');
    }

    // ✏ EDIT
    public function edit($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $students = Student::all();
        $courses = Course::all();

        return view('admin.enrollments.edit', compact('enrollment','students','courses'));
    }

    // 🔄 UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'student_id' => 'required',
            'course_id' => 'required',
            'status' => 'required',
        ]);

        $enrollment = Enrollment::findOrFail($id);

        $enrollment->update([
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'status' => $request->status,
            'enrolled_at' => $request->enrolled_at,
        ]);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment Updated Successfully!');
    }
}