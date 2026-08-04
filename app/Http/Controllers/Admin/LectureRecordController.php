<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Lecturer;
use App\Models\LectureRecord;
use Illuminate\Http\Request;

class LectureRecordController extends Controller
{
    public function index()
    {
        $records = LectureRecord::with(['subject', 'lecturer'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $grouped = $records->groupBy(function ($r) {
            return implode('|', [
                $r->date,
                $r->start_time,
                $r->end_time,
                $r->lecturer_id,
                trim($r->content_covered ?? ''),
                trim($r->remarks ?? ''),
            ]);
        })->values();

        return view('admin.lecture-records.index', compact('grouped'));
    }

    public function destroy($ids)
    {
        $idArray = array_filter(explode(',', $ids));
        $count = LectureRecord::whereIn('id', $idArray)->delete();

        return redirect()->route('admin.lecture-records.index')
            ->with('success', "{$count} lecture record(s) deleted.");
    }

    public function create()
    {
        $faculties = \App\Models\Faculty::orderBy('name')->get(['id', 'name']);
        $lecturers = Lecturer::orderBy('name')->get(['id', 'name', 'username']);

        return view('admin.lecture-records.create', compact('faculties', 'lecturers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
            'lecturer_id' => 'nullable|exists:lecturers,id',
            'content_covered' => 'nullable|string',
            'date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ], [
            'end_time.after' => 'End time must be after start time.',
            'subject_ids.required' => 'Select at least one module.',
        ]);

        foreach ($data['subject_ids'] as $subjectId) {
            LectureRecord::create([
                'subject_id' => $subjectId,
                'lecturer_id' => $data['lecturer_id'] ?? null,
                'content_covered' => $data['content_covered'] ?? null,
                'date' => $data['date'] ?? null,
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'created_by' => 'admin',
            ]);
        }

        $count = count($data['subject_ids']);

        return redirect()->route('admin.lecture-records.index')
            ->with('success', "{$count} lecture record(s) created successfully.");
    }

    public function edit($ids)
    {
        $idArray = array_filter(explode(',', $ids));
        $record = LectureRecord::whereIn('id', $idArray)->firstOrFail();
        $lecturers = Lecturer::orderBy('name')->get(['id', 'name', 'username']);

        return view('admin.lecture-records.edit', compact('record', 'lecturers'))
            ->with('idsCsv', $ids);
    }

    public function update(Request $request, $ids)
    {
        $data = $this->validateData($request);
        $idArray = array_filter(explode(',', $ids));

        LectureRecord::whereIn('id', $idArray)->update([
            'lecturer_id' => $data['lecturer_id'] ?? null,
            'content_covered' => $data['content_covered'] ?? null,
            'date' => $data['date'] ?? null,
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            'remarks' => $data['remarks'] ?? null,
        ]);

        return redirect()->route('admin.lecture-records.index')
            ->with('success', count($idArray) . ' lecture record(s) updated successfully.');
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'lecturer_id' => 'nullable|exists:lecturers,id',
            'content_covered' => 'nullable|string',
            'remarks' => 'nullable|string',
            'date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ], [
            'end_time.after' => 'End time must be after start time.',
        ]);
    }

    private function cleanIds(Request $request): array
    {
        return collect($request->query('ids', []))
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public function getCourses(Request $request)
    {
        $facultyIds = $this->cleanIds($request);
        $grouped = \App\Models\Course::whereIn('faculty_id', $facultyIds)
            ->get(['id', 'name'])
            ->groupBy('name')
            ->map(fn($g, $name) => ['name' => $name, 'ids' => $g->pluck('id')->values()])
            ->values();
        return response()->json($grouped);
    }

    public function getLevels(Request $request)
    {
        $courseIds = $this->cleanIds($request);
        $grouped = \App\Models\Level::whereIn('course_id', $courseIds)
            ->get(['id', 'name'])
            ->groupBy('name')
            ->map(fn($g, $name) => ['name' => $name, 'ids' => $g->pluck('id')->values()])
            ->values();
        return response()->json($grouped);
    }

    public function getSemesters(Request $request)
    {
        $levelIds = $this->cleanIds($request);

        $levelNames = \App\Models\Level::whereIn('id', $levelIds)
            ->pluck('name')->map(fn($n) => strtolower(trim($n)));

        $maxSemester = 2;
        if ($levelNames->contains('degree')) $maxSemester = 6;
        elseif ($levelNames->contains('hnd')) $maxSemester = 4;

        $grouped = \App\Models\Semester::whereIn('level_id', $levelIds)
            ->get(['id', 'name'])
            ->groupBy('name')
            ->map(fn($g, $name) => ['name' => $name, 'ids' => $g->pluck('id')->values()])
            ->filter(function ($item) use ($maxSemester) {
                preg_match('/(\d+)/', $item['name'], $m);
                return (isset($m[1]) ? (int)$m[1] : 999) <= $maxSemester;
            })
            ->values();

        return response()->json($grouped);
    }

    public function getSubjects(Request $request)
    {
        $semesterIds = $this->cleanIds($request);
        return response()->json(
            Subject::whereIn('semester_id', $semesterIds)->get(['id', 'code', 'name'])
        );
    }

    public function reportsIndex()
    {
        $reports = \App\Models\LectureRecordReport::with('lecturer')
            ->orderByDesc('generated_at')
            ->get();

        return view('admin.lecture-records.reports.index', compact('reports'));
    }

    public function reportsCreate()
    {
        $lecturers = Lecturer::orderBy('name')->get(['id', 'name', 'username']);
        return view('admin.lecture-records.reports.create', compact('lecturers'));
    }

    public function reportsStore(Request $request)
    {
        $data = $request->validate([
            'lecturer_id' => 'required|exists:lecturers,id',
            'month' => 'required|date_format:Y-m|before_or_equal:' . date('Y-m'),
        ]);

        $lecturer = Lecturer::findOrFail($data['lecturer_id']);
        $start = \Carbon\Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $records = LectureRecord::with('subject')
            ->where('lecturer_id', $lecturer->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->get();

        // Merge rows that share identical content_covered text, combining module codes
        $grouped = $records
            ->groupBy(fn($r) => trim($r->content_covered ?? '—'))
            ->map(function ($group, $content) {
                return [
                    'content' => $content,
                    'modules' => $group->pluck('subject')->filter()
                        ->map(fn($s) => $s->code . ' - ' . $s->name)
                        ->unique()->values(),
                    'records' => $group->values(),
                ];
            })
            ->values();

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.lecture-records.reports.pdf', [
            'lecturer' => $lecturer,
            'month' => $start,
            'grouped' => $grouped,
        ]);

        $filename = 'lecture-report-' . \Illuminate\Support\Str::slug($lecturer->name) . '-' . $start->format('Y-m') . '-' . time() . '.pdf';
        $path = 'lecture-reports/' . $filename;
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $pdf->output());

        \App\Models\LectureRecordReport::create([
            'lecturer_id' => $lecturer->id,
            'month' => $start->toDateString(),
            'file_path' => $path,
            'generated_at' => now(),
        ]);

        return redirect()->route('admin.lecture-records.reports.index')
            ->with('success', 'Report generated successfully.');
    }

    public function reportsDownload(\App\Models\LectureRecordReport $report)
    {
        return \Illuminate\Support\Facades\Storage::disk('public')->download(
            $report->file_path,
            basename($report->file_path)
        );
    }

    public function reportsDestroy(\App\Models\LectureRecordReport $report)
    {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($report->file_path);
        $report->delete();

        return redirect()->route('admin.lecture-records.reports.index')
            ->with('success', 'Report deleted.');
    }

}