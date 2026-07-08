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