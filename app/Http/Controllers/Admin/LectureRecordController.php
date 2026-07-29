<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Lecturer;
use App\Models\LectureRecord;
use Illuminate\Http\Request;

class LectureRecordController extends Controller
{
    // Faculty -> Course -> Level -> Semester accordion (same pattern as AttendanceController@index)
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

        return view('admin.lecture-records.index', compact('grouped'));
    }

    // List of records for a subject
    public function show($id)
    {
        $subject = Subject::with(['course.faculty', 'level'])->findOrFail($id);

        $records = LectureRecord::where('subject_id', $id)
            ->with('lecturer')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return view('admin.lecture-records.show', compact('subject', 'records'));
    }

    public function create($id)
    {
        $subject = Subject::findOrFail($id);
        $lecturers = Lecturer::orderBy('name')->get(['id', 'name', 'username']);

        return view('admin.lecture-records.create', compact('subject', 'lecturers'));
    }

    public function store(Request $request, $id)
    {
        $data = $this->validateData($request);

        LectureRecord::create([
            'subject_id' => $id,
            'lecturer_id' => $data['lecturer_id'] ?? null,
            'content_covered' => $data['content_covered'] ?? null,
            'date' => $data['date'] ?? null,
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            'created_by' => 'admin',
        ]);

        return redirect()->route('admin.lecture-records.show', $id)
            ->with('success', 'Lecture record created successfully.');
    }

    public function edit(LectureRecord $record)
    {
        $lecturers = Lecturer::orderBy('name')->get(['id', 'name', 'username']);
        return view('admin.lecture-records.edit', compact('record', 'lecturers'));
    }

    public function update(Request $request, LectureRecord $record)
    {
        $data = $this->validateData($request);

        $record->update([
            'lecturer_id' => $data['lecturer_id'] ?? null,
            'content_covered' => $data['content_covered'] ?? null,
            'date' => $data['date'] ?? null,
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
        ]);

        return redirect()->route('admin.lecture-records.show', $record->subject_id)
            ->with('success', 'Lecture record updated successfully.');
    }

    public function pdf($id)
    {
        $subject = Subject::findOrFail($id);

        $records = LectureRecord::where('subject_id', $id)
            ->with('lecturer')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.lecture-records.pdf', compact('subject', 'records'));

        return $pdf->download('lecture-records-' . $subject->code . '.pdf');
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'lecturer_id' => 'nullable|exists:lecturers,id',
            'content_covered' => 'nullable|string',
            'date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ], [
            'end_time.after' => 'End time must be after start time.',
        ]);
    }
}