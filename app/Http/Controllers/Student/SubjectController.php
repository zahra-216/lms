<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Note;
use App\Models\Semester;
use App\Models\Student; // 🔥 MUST ADD
use App\Models\Assignment;
use App\Models\AssignmentSubmission;


class SubjectController extends Controller
{

    /* DOWNLOAD */
    public function download($id)
    {
        $note = Note::findOrFail($id);

        $filePath = storage_path('app/public/' . $note->file_path);

        if(!file_exists($filePath)){
            abort(404, 'File not found');
        }

        return response()->download($filePath);
    }
 public function profile()
{
    $studentId = session('student_id');

    if(!$studentId){
        return redirect('/login'); // safety
    }

    $student = Student::find($studentId);

    if(!$student){
        return redirect('/login'); // safety
    }

    return view('student.profile', [
        'student' => $student,
          'studentName' => $student->name ?? 'Student',
            'registrationNo' => $student->registration_no ?? 'N/A',
    ]);
}

public function downloadAssignment($id)
{
    $assignment = Assignment::findOrFail($id);

    $filePath = storage_path('app/public/' . $assignment->file_path);

    if(!file_exists($filePath)){
        abort(404, 'File not found');
    }

    return response()->download($filePath);
}



public function grades()
{
    $studentId = auth()->guard('student')->id();

    $marks = Mark::with(['assignment.subject'])
        ->where('student_id', $studentId)
        ->get();

    return view('student.grades', compact('marks'));
}



}