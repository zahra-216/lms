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

    public function videos($id)
    {
        $subject = Subject::with('videos')->findOrFail($id);
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

        $students = \App\Models\Student::where('course_id', $subject->course_id)
            ->where('level_id', $subject->level_id)
            ->get();

        $subjectMarks = \App\Models\SubjectMark::where('subject_id', $id)
            ->get()
            ->keyBy('student_id');

        return view('lecturer.subject.grades', compact('subject', 'students', 'subjectMarks'));
    }

    public function updateMarks(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'marks' => 'required|array',
            'marks.*.assignment_marks' => 'nullable|numeric|min:0|max:100',
            'marks.*.mid_marks' => 'nullable|numeric|min:0|max:100',
            'marks.*.final_exam_marks' => 'nullable|numeric|min:0|max:100',
            'marks.*.final_marks' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($request->marks as $student_id => $data) {

            $finalMarks = $data['final_marks'] ?? null;
            $finalGrade = ($finalMarks !== null && $finalMarks !== '')
                ? $this->grade($finalMarks)
                : null;

            try {
                \App\Models\SubjectMark::updateOrCreate(
                    ['student_id' => $student_id, 'subject_id' => $id],
                    [
                        'assignment_marks'  => $data['assignment_marks'] ?? null,
                        'mid_marks'         => $data['mid_marks'] ?? null,
                        'final_exam_marks'  => $data['final_exam_marks'] ?? null,
                        'final_marks'       => $finalMarks,
                        'final_grade'       => $finalGrade,
                    ]
                );
            } catch (\Exception $e) {
                \Log::error("Failed to save subject marks for student {$student_id}: " . $e->getMessage());
            }
        }

        return redirect()->route('lecturer.subject.grades', $id)
            ->with('success', 'Marks saved successfully');
    }

    private function grade($marks)
    {
        return match(true) {
            $marks >= 80 => 'A',
            $marks >= 60 => 'B',
            $marks >= 40 => 'C',
            default => 'F',
        };
    }
}