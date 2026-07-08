<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mark;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;

class MarkController extends Controller
{
    // CREATE PAGE
    public function create($assignment_id)
    {
        $assignment = Assignment::with('subject')->findOrFail($assignment_id);

        $submissions = AssignmentSubmission::with('student')
            ->where('assignment_id', $assignment_id)
            ->get();

        return view('admin.marks.create', compact('submissions','assignment'));
    }

    // STORE MARKS
    public function store(Request $request)
    {
        foreach ($request->marks as $student_id => $mark) {

            Mark::updateOrCreate(
                [
                    'student_id' => $student_id,
                    'assignment_id' => $request->assignment_id
                ],
                [
                    'marks' => $mark,
                    'grade' => $this->grade($mark)
                ]
            );
        }

        return redirect()->route('admin.marks.index')
            ->with('success','Marks saved successfully');
    }

    // INDEX PAGE
    public function index()
    {
        $marks = Mark::with(['student','assignment.subject'])
            ->latest()
            ->get();

        return view('admin.marks.index', compact('marks'));
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