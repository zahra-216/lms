<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Course;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    // ==========================
    // MAIN INDEX PAGE
    // ==========================
    public function index()
    {
        $levels = Level::with('course')->get();
        $courses = Course::all();

        return view('admin.levels.index', compact('levels','courses'));
    }

    // ==========================
    // STORE NEW LEVEL
    // ==========================
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required',
            'name' => 'required'
        ]);

        Level::create($request->all());

        return back()->with('success','Level Added Successfully');
    }

    // ==========================
    // EDIT LEVEL
    // ==========================
    public function edit($id)
    {
        $editLevel = Level::findOrFail($id);
        $levels = Level::with('course')->get();
        $courses = Course::all();

        return view('admin.levels.index', compact('editLevel','levels','courses'));
    }

    // ==========================
    // UPDATE LEVEL
    // ==========================
    public function update(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'required',
            'name' => 'required'
        ]);

        $level = Level::findOrFail($id);
        $level->update($request->all());

        return redirect()->route('admin.levels.index')->with('success','Updated Successfully');
    }

    // ==========================
    // DELETE LEVEL
    // ==========================
    public function destroy($id)
    {
        Level::destroy($id);

        return back()->with('success','Deleted Successfully');
    }

    // ==========================
    // AJAX: GET LEVELS BY COURSE
    // ==========================
    public function getByCourse($courseId)
    {
        // Return all levels of selected course
        return Level::where('course_id', $courseId)
            ->select('id','name')
            ->get();
    }
}