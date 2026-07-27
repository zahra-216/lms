<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;

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

    public function show($id, Request $request)
    {
        $subject = Subject::with(['course.faculty', 'level'])->findOrFail($id);

        $date = $request->query('date', now()->toDateString());

        $students = Student::where('course_id', $subject->course_id)
            ->where('level_id', $subject->level_id)
            ->get();

        $attendance = Attendance::where('subject_id', $id)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        // Summary
        $totalClasses = Attendance::where('subject_id', $id)
            ->distinct('date')
            ->count('date');

        $summary = $students->map(function ($student) use ($id, $totalClasses) {
            $presentCount = Attendance::where('subject_id', $id)
                ->where('student_id', $student->id)
                ->where('status', 'present')
                ->count();

            $percentage = $totalClasses > 0 ? round(($presentCount / $totalClasses) * 100, 1) : 0;

            return (object)[
                'student' => $student,
                'present_count' => $presentCount,
                'total_classes' => $totalClasses,
                'percentage' => $percentage,
            ];
        });

        return view('admin.attendance.show', compact('subject', 'students', 'attendance', 'date', 'summary', 'totalClasses'));
    }

    public function exportPdf($id, Request $request)
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

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.attendance.pdf', compact('subject', 'students', 'attendance', 'date'));

        return $pdf->download('attendance-' . $subject->code . '-' . $date . '.pdf');
    }

    public function exportSummaryPdf($id)
    {
        $subject = Subject::findOrFail($id);

        $students = Student::where('course_id', $subject->course_id)
            ->where('level_id', $subject->level_id)
            ->get();

        $totalClasses = Attendance::where('subject_id', $id)->distinct('date')->count('date');

        $summary = $students->map(function ($student) use ($id, $totalClasses) {
            $presentCount = Attendance::where('subject_id', $id)
                ->where('student_id', $student->id)
                ->where('status', 'present')
                ->count();

            $percentage = $totalClasses > 0 ? round(($presentCount / $totalClasses) * 100, 1) : 0;

            return (object)[
                'student' => $student,
                'present_count' => $presentCount,
                'total_classes' => $totalClasses,
                'percentage' => $percentage,
            ];
        });

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.attendance.summary-pdf', compact('subject', 'summary', 'totalClasses'));

        return $pdf->download('attendance-summary-' . $subject->code . '.pdf');
    }
}