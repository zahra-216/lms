<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class LecturerSubjectController extends Controller
{
    // The 4-card landing page for a clicked module
    public function show($id)
    {
        $subject = Subject::findOrFail($id);
        return view('lecturer.subject.show', compact('subject'));
    }

    public function notes($id)
    {
        $subject = Subject::with('notes')->findOrFail($id);
        return view('lecturer.subject.notes', compact('subject'));
    }

    // 🔧 Placeholder until a LectureVideo model/table exists
    public function videos($id)
    {
        $subject = Subject::findOrFail($id);
        return view('lecturer.subject.videos', compact('subject'));
    }

    public function assignments($id)
    {
        $subject = Subject::with(['assignments.submissions.student'])->findOrFail($id);
        return view('lecturer.subject.assignments', compact('subject'));
    }

    public function grades($id)
    {
        $subject = Subject::with(['assignments.marks.student', 'assignments.submissions'])->findOrFail($id);
        return view('lecturer.subject.grades', compact('subject'));
    }
}