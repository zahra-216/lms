<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Course;
use App\Models\Level;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\LectureRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LecturerLectureRecordController extends Controller
{
    // Faculty -> Course -> Level -> Semester -> Subject picker
    public function select()
    {
        $faculties = Faculty::all();
        return view('lecturer.lecture-records.select', compact('faculties'));
    }

    public function getCourses($facultyId)
    {
        return response()->json(
            Course::where('faculty_id', $facultyId)->get(['id', 'name'])
        );
    }

    public function getLevels($courseId)
    {
        return response()->json(
            Level::where('course_id', $courseId)->get(['id', 'name'])
        );
    }

    public function getSemesters($levelId)
    {
        return response()->json(
            Semester::where('level_id', $levelId)->get(['id', 'name'])
        );
    }

    public function getSubjects($semesterId)
    {
        return response()->json(
            Subject::where('semester_id', $semesterId)->get(['id', 'name', 'code'])
        );
    }

    // List (acts as history) for a subject
    public function index($id)
    {
        $subject = Subject::findOrFail($id);

        $records = LectureRecord::where('subject_id', $id)
            ->with('lecturer')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return view('lecturer.lecture-records.index', compact('subject', 'records'));
    }

    // Lecturer-initiated creation — content only, no date/time
    public function create($id)
    {
        $subject = Subject::findOrFail($id);
        return view('lecturer.lecture-records.create', compact('subject'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'content_covered' => 'required|string',
        ]);

        LectureRecord::create([
            'subject_id' => $id,
            'lecturer_id' => Auth::guard('lecturer')->id(),
            'content_covered' => $request->content_covered,
            'created_by' => 'lecturer',
        ]);

        return redirect()->route('lecturer.subject.lecture-records', $id)
            ->with('success', 'Lecture record created. Awaiting date/time from admin.');
    }

    // Admin-initiated record: lecturer fills content, date/start/end shown read-only
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

        return redirect()->route('lecturer.subject.lecture-records', $record->subject_id)
            ->with('success', 'Content added successfully.');
    }

    public function pdf($id)
    {
        $subject = Subject::findOrFail($id);

        $records = LectureRecord::where('subject_id', $id)
            ->with('lecturer')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $pdf = \PDF::loadView('lecturer.lecture-records.pdf', compact('subject', 'records'));

        $filename = "lecture-records-{$subject->code}.pdf";

        return request()->query('download')
            ? $pdf->download($filename)
            : $pdf->stream($filename);
    }
}