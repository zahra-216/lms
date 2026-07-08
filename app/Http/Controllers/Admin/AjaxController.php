<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Note;

class AjaxController extends Controller
{
    public function getSubjects(Request $request)
    {
        return Subject::where('course_id', $request->course_id)
            ->where('level_id', $request->level_id)
            ->get();
    }

    public function getNotes($subject_id)
    {
        return Note::where('subject_id', $subject_id)->get();
    }
}