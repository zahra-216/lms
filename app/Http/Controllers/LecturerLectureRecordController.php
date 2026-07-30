<?php

namespace App\Http\Controllers;

use App\Models\LectureRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LecturerLectureRecordController extends Controller
{
    // My Lecture Records — assigned to me, or unclaimed (lecturer_id null)
    public function index()
    {
        $lecturerId = Auth::guard('lecturer')->id();

        $records = LectureRecord::where(function ($q) use ($lecturerId) {
                $q->where('lecturer_id', $lecturerId)
                  ->orWhereNull('lecturer_id');
            })
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return view('lecturer.lecture-records.index', compact('records'));
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

        $record->update([
            'content_covered' => $request->content_covered,
            'lecturer_id' => $record->lecturer_id ?? Auth::guard('lecturer')->id(),
        ]);

        return redirect()->route('lecturer.lecture-records.index')
            ->with('success', 'Content added successfully.');
    }

    public function pdf()
    {
        $lecturerId = Auth::guard('lecturer')->id();

        $records = LectureRecord::where('lecturer_id', $lecturerId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $pdf = \PDF::loadView('lecturer.lecture-records.pdf', compact('records'));

        return request()->query('download')
            ? $pdf->download('my-lecture-records.pdf')
            : $pdf->stream('my-lecture-records.pdf');
    }
}