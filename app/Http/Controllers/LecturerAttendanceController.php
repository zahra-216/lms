<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LecturerAttendanceController extends Controller
{
    public function show($id, Request $request)
    {
        $subject = Subject::findOrFail($id);
        $date = $request->query('date', now()->toDateString());

        $students = Student::where('course_id', $subject->course_id)
            ->where('level_id', $subject->level_id)
            ->get();

        $alreadyMarked = Attendance::where('subject_id', $id)
            ->where('date', $date)
            ->exists();

        return view('lecturer.subject.attendance', compact('subject', 'students', 'date', 'alreadyMarked'));
    }

    public function history($id)
    {
        $subject = Subject::findOrFail($id);

        $dates = Attendance::where('subject_id', $id)
            ->select('date')
            ->distinct()
            ->orderByDesc('date')
            ->pluck('date');

        return view('lecturer.subject.attendance-history', compact('subject', 'dates'));
    }

    public function historyPdf(Request $request, $id, $date)
    {
        $subject = Subject::findOrFail($id);

        $students = Student::where('course_id', $subject->course_id)
            ->where('level_id', $subject->level_id)
            ->get();

        $records = Attendance::where('subject_id', $id)
            ->where('date', $date)
            ->pluck('status', 'student_id');

        $pdf = \PDF::loadView('lecturer.subject.attendance-pdf', compact('subject', 'students', 'records', 'date'));

        $filename = "attendance-{$subject->code}-{$date}.pdf";

        return $request->query('download')
            ? $pdf->download($filename)
            : $pdf->stream($filename);
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