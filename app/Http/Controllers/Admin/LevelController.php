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

    // ==========================
    // UPDATE LEVEL
    // ==========================

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