<?php

namespace App\Http\Controllers;

use App\Models\LectureRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LecturerLectureRecordController extends Controller
{
    public function index()
    {
        return view('lecturer.lecture-records.index');
    }

    // AJAX: records for a given month, grouped so multi-module entries collapse into one row
    public function byMonth(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m|before_or_equal:' . date('Y-m'),
        ]);

        $lecturerId = Auth::guard('lecturer')->id();
        $start = \Carbon\Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $records = LectureRecord::where(function ($q) use ($lecturerId) {
                $q->where('lecturer_id', $lecturerId)
                  ->orWhereNull('lecturer_id');
            })
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $grouped = $records->groupBy(function ($r) {
                return implode('|', [
                    $r->date, $r->start_time, $r->end_time, $r->lecturer_id,
                    trim($r->content_covered ?? ''), trim($r->remarks ?? ''),
                ]);
            })
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'id' => $first->id, 
                    'date' => $first->date ? \Carbon\Carbon::parse($first->date)->format('d M Y') : '—',
                    'start' => $first->start_time ? \Carbon\Carbon::parse($first->start_time)->format('h:i A') : '—',
                    'end' => $first->end_time ? \Carbon\Carbon::parse($first->end_time)->format('h:i A') : '—',
                    'duration' => $first->duration ?? '—',
                    'content' => $first->content_covered ?? '—',
                    'remarks' => $first->remarks ?? '—',
                    'status' => ($first->content_covered && $first->date) ? 'Complete' : 'Pending',
                ];
            })
            ->values();

        return response()->json($grouped);
    }

    public function create()
    {
        return view('lecturer.lecture-records.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'content_covered' => 'required|string',
        ]);

        LectureRecord::create([
            'lecturer_id' => Auth::guard('lecturer')->id(),
            'content_covered' => $request->content_covered,
            'created_by' => 'lecturer',
        ]);

        return redirect()->route('lecturer.lecture-records.index')
            ->with('success', 'Lecture record created successfully.');
    }

    public function addContentForm(LectureRecord $record)
    {
        return view('lecturer.lecture-records.add-content', compact('record'));
    }

    public function addContentStore(Request $request, LectureRecord $record)
    {
        $request->validate([
            'content_covered' => 'required|string',
        ]);

        $lecturerId = Auth::guard('lecturer')->id();

        // Find every sibling row in the same "session" group as $record
        // (same date/start/end/lecturer_id/remarks, still pending content)
        $query = LectureRecord::where('date', $record->date)
            ->where('start_time', $record->start_time)
            ->where('end_time', $record->end_time)
            ->where(function ($q) use ($record) {
                $record->remarks
                    ? $q->where('remarks', $record->remarks)
                    : $q->whereNull('remarks');
            })
            ->where(function ($q) use ($record) {
                $record->content_covered
                    ? $q->where('content_covered', $record->content_covered)
                    : $q->whereNull('content_covered');
            });

        if ($record->lecturer_id) {
            $query->where('lecturer_id', $record->lecturer_id);
        } else {
            $query->whereNull('lecturer_id');
        }

        $query->update([
            'content_covered' => $request->content_covered,
            'lecturer_id' => $lecturerId,
        ]);

        return redirect()->route('lecturer.lecture-records.index')
            ->with('success', 'Content added successfully.');
    }

    public function pdf(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m|before_or_equal:' . date('Y-m'),
        ]);

        $lecturerId = Auth::guard('lecturer')->id();
        $start = \Carbon\Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $records = LectureRecord::where(function ($q) use ($lecturerId) {
                $q->where('lecturer_id', $lecturerId)
                  ->orWhereNull('lecturer_id');
            })
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $grouped = $records->groupBy(function ($r) {
                return implode('|', [
                    $r->date, $r->start_time, $r->end_time, $r->lecturer_id,
                    trim($r->content_covered ?? ''), trim($r->remarks ?? ''),
                ]);
            })
            ->values();

        $pdf = \PDF::loadView('lecturer.lecture-records.pdf', [
            'grouped' => $grouped,
            'month' => $start,
        ]);

        return $pdf->download('my-lecture-records-' . $start->format('Y-m') . '.pdf');
    }
}