<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $subjects = Subject::with(['course.faculty', 'level', 'semester'])->get();

        $grouped = $subjects
            ->groupBy(function ($s) { return optional(optional($s->course)->faculty)->name ?? 'Unassigned Faculty'; })
            ->map(function ($facultySubjects) {
                return $facultySubjects->groupBy(function ($s) { return optional($s->course)->name ?? 'Unassigned Course'; })
                    ->map(function ($courseSubjects) {
                        return $courseSubjects->groupBy(function ($s) { return optional($s->level)->name ?? 'Unassigned Level'; })
                            ->map(function ($levelSubjects) {
                                return $levelSubjects->groupBy(function ($s) { return optional($s->semester)->name ?? 'Unassigned Semester'; });
                            });
                    });
            });

        return view('admin.attendance.index', compact('grouped'));
    }

    public function show($id)
    {
        $subject = Subject::with(['course.faculty', 'level'])->findOrFail($id);

        return view('admin.attendance.show', compact('subject'));
    }

    public function mark($id, Request $request)
    {
        $subject = Subject::findOrFail($id);
        $date = $request->query('date', now()->toDateString());

        $students = Student::where('course_id', $subject->course_id)
            ->where('level_id', $subject->level_id)
            ->get();

        $attendance = Attendance::where('subject_id', $id)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        $alreadyMarked = $attendance->isNotEmpty();

        return view('admin.attendance.mark', compact('subject', 'students', 'date', 'alreadyMarked', 'attendance'));
    }

    public function markStore(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'status' => 'required|array',
            'status.*' => 'nullable|in:present,absent',
        ]);

        foreach ($request->status as $student_id => $status) {
            try {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $student_id,
                        'subject_id' => $id,
                        'date' => $request->date,
                    ],
                    [
                        'status' => $status ?: null,
                        'marked_by' => auth()->guard('admin')->id(),
                    ]
                );
            } catch (\Exception $e) {
                \Log::error("Failed to save attendance for student {$student_id}: " . $e->getMessage());
            }
        }

        return redirect()->route('admin.attendance.mark', ['id' => $id, 'date' => $request->date])
            ->with('success', 'Attendance saved successfully');
    }

    public function history($id)
    {
        $subject = Subject::findOrFail($id);

        $months = Attendance::where('subject_id', $id)
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as ym")
            ->distinct()
            ->orderByDesc('ym')
            ->pluck('ym');

        return view('admin.attendance.history', compact('subject', 'months'));
    }

    public function monthlyPdf(Request $request, $id, $month)
    {
        $subject = Subject::findOrFail($id);

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $students = Student::where('course_id', $subject->course_id)
            ->where('level_id', $subject->level_id)
            ->orderBy('name')
            ->get();

        $dates = Attendance::where('subject_id', $id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->select('date')
            ->distinct()
            ->orderBy('date')
            ->pluck('date');

        $records = Attendance::where('subject_id', $id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy('date')
            ->map(fn($rows) => $rows->keyBy('student_id'));

        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('a4', 'landscape');
        $pdf->loadView('admin.attendance.monthly-pdf', compact('subject', 'students', 'dates', 'records', 'start'));

        $filename = 'attendance-' . $subject->code . '-' . $start->format('Y-m') . '.pdf';

        return $request->query('download')
            ? $pdf->download($filename)
            : $pdf->stream($filename);
    }
}