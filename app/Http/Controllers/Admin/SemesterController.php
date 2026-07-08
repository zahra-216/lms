<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\Level;
use App\Models\Course;
use App\Models\Subject; // 🔥 ADD THIS
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    // ==========================
    // INDEX
    // ==========================
    public function index()
    {
        $semesters = Semester::with(['level', 'course'])->get();
        $levels = Level::all();
        $courses = Course::all();

        return view('admin.semesters.index', compact('semesters', 'levels', 'courses'));
    }

    // ==========================
    // STORE
    // ==========================
    public function store(Request $request)
    {
        $request->validate([
            'level_id' => 'required|exists:levels,id',
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
        ]);

        Semester::create([
            'level_id' => $request->level_id,
            'course_id' => $request->course_id,
            'name' => $request->name,
        ]);

        return back()->with('success', 'Semester Added Successfully!');
    }

    // ==========================
    // EDIT
    // ==========================
    public function edit($id)
    {
        $editSemester = Semester::findOrFail($id);
        $semesters = Semester::with(['level', 'course'])->get();
        $levels = Level::all();
        $courses = Course::all();

        return view('admin.semesters.index', compact('editSemester','semesters','levels','courses'));
    }

    // ==========================
    // UPDATE
    // ==========================
    public function update(Request $request, $id)
    {
        $request->validate([
            'level_id' => 'required|exists:levels,id',
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
        ]);

        $semester = Semester::findOrFail($id);

        $semester->update([
            'level_id' => $request->level_id,
            'course_id' => $request->course_id,
            'name' => $request->name,
        ]);

        return redirect()->route('admin.semesters.index')
            ->with('success','Semester Updated Successfully!');
    }

    // ==========================
    // DELETE
    // ==========================
    public function destroy($id)
    {
        Semester::destroy($id);

        return back()->with('success','Semester Deleted Successfully!');
    }

    // ==================================================
    // 🔥 AJAX: GET SEMESTERS BY LEVEL (IMPORTANT FIX)
    // ==================================================
    public function getByLevel($levelId)
    {
        return Semester::where('level_id', $levelId)
            ->select('id','name')
            ->get();
    }
  

// ==========================
// 🔥 STUDENT: GET SUBJECTS
// ==========================
public function getSubjects($id)
{
    $semester = Semester::findOrFail($id);

    $subjects = Subject::where('semester_id', $id)->get();

    return response()->json([
        'semester' => $semester->name,
        'subjects' => $subjects
    ]);
}
}