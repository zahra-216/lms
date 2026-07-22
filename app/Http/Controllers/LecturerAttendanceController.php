<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;

class LecturerAttendanceController extends Controller
{
    public function show($id, Request $request)
    {
        $subject = Subject::findOrFail($id);

        $date = $request->query('date', now()->toDateString());

        $students = Student::where('course_id', $subject->course_id)
            ->where('level_id', $subject->level_id)
            ->get();

        return view('lecturer.subject.attendance', compact('subject', 'students', 'date'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'status' => 'required|array',
            'status.*' => 'nullable|in:present,absent',
        ]);

        foreach ($request->status as $student_id => $status) {
            try {
                \App\Models\Attendance::updateOrCreate(
                    [
                        'student_id' => $student_id,
                        'subject_id' => $id,
                        'date' => $request->date,
                    ],
                    [
                        'status' => $status ?: null,
                        'marked_by' => auth()->guard('lecturer')->id(),
                    ]
                );
            } catch (\Exception $e) {
                \Log::error("Failed to save attendance for student {$student_id}: " . $e->getMessage());
            }
        }

        return redirect()->route('lecturer.subject.attendance', $id)
            ->with('success', 'Attendance saved successfully');
    }
}