<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Level;
use App\Models\Semester;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function show(Subject $subject)
    {
        return view('admin.subject.show', compact('subject'));
    }

    public function grades(Subject $subject)
    {
        $subject->load(['assignments.marks.student', 'assignments.submissions']);

        $students = \App\Models\Student::where('course_id', $subject->course_id)
            ->where('level_id', $subject->level_id)
            ->get();

        $subjectMarks = \App\Models\SubjectMark::where('subject_id', $subject->id)
            ->get()
            ->keyBy('student_id');

        return view('admin.subject.grades', compact('subject', 'students', 'subjectMarks'));
    }

    public function updateMarks(Request $request, Subject $subject)
    {
        $rule = ['nullable', 'regex:/^(?:[Aa][Bb]|\d{1,3}(\.\d{1,2})?)$/'];

        $request->validate([
            'marks' => 'required|array',
            'marks.*.assignment_marks' => $rule,
            'marks.*.practical_marks' => $rule,
            'marks.*.mid_marks' => $rule,
            'marks.*.final_exam_marks' => $rule,
            'marks.*.final_marks' => $rule,
        ]);

        foreach ($request->marks as $student_id => $data) {
            $finalMarks = $data['final_marks'] ?? null;
            $finalGrade = is_numeric($finalMarks)
                ? $this->gradeFor($finalMarks)
                : (strtoupper((string) $finalMarks) === 'AB' ? 'AB' : null);

            \App\Models\SubjectMark::updateOrCreate(
                ['student_id' => $student_id, 'subject_id' => $subject->id],
                [
                    'assignment_marks' => $data['assignment_marks'] ?? null,
                    'practical_marks'  => $data['practical_marks'] ?? null,
                    'mid_marks'        => $data['mid_marks'] ?? null,
                    'final_exam_marks' => $data['final_exam_marks'] ?? null,
                    'final_marks'      => $finalMarks,
                    'final_grade'      => $finalGrade,
                ]
            );
        }

        return redirect()->route('admin.subjects.grades', $subject->id)
            ->with('success', 'Marks saved successfully');
    }

    private function gradeFor($marks)
    {
        return match(true) {
            $marks >= 85 => 'A+', $marks >= 75 => 'A', $marks >= 70 => 'A-',
            $marks >= 65 => 'B+', $marks >= 60 => 'B', $marks >= 55 => 'B-',
            $marks >= 50 => 'C+', $marks >= 45 => 'C', $marks >= 40 => 'C-',
            $marks >= 35 => 'D+', $marks >= 30 => 'D', default => 'F',
        };
    }

    public function index()
    {
        $subjects = Subject::with(['course','level','semester'])->paginate(10);
        $courses = Course::all();
        $levels = Level::all();
        $semesters = Semester::all();

        return view('admin.subjects.index', compact('subjects','courses','levels','semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id'=>'required',
            'level_id'=>'required',
            'semester_id'=>'required',
            'code'=>'required',
            'name'=>'required',
        ]);

        Subject::create($request->all());

        return redirect()->back()->with('success','Subject added successfully!');
    }

    public function edit(Subject $subject)
    {
        $subjects = Subject::with(['course','level','semester'])->paginate(10);
        $courses = Course::all();
        $levels = Level::where('course_id',$subject->course_id)->get();
        $semesters = Semester::where('level_id',$subject->level_id)->get();

        return view('admin.subjects.index', [
            'editSubject'=>$subject,
            'subjects'=>$subjects,
            'courses'=>$courses,
            'levels'=>$levels,
            'semesters'=>$semesters,
        ]);
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'course_id'=>'required',
            'level_id'=>'required',
            'semester_id'=>'required',
            'code'=>'required',
            'name'=>'required',
        ]);

        $subject->update($request->all());
        return redirect()->route('admin.subjects.index')->with('success','Subject updated successfully!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->back()->with('success','Subject deleted successfully!');
    }

    // AJAX methods
    public function getLevels($courseId)
    {
        $levels = Level::where('course_id',$courseId)->get();
        return response()->json($levels);
    }

    public function getSemesters($levelId)
    {
        $semesters = Semester::where('level_id',$levelId)->get();
        return response()->json($semesters);
    }
}