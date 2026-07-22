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
    /* LOAD SUBJECTS */
    public function getSubjects($id)
    {
        $semester = Semester::findOrFail($id);

        $subjects = Subject::where('semester_id', $id)->get();

        return response()->json([
            'semester' => $semester->name,
            'subjects' => $subjects
        ]);
    }

    /* VERIFY SUBJECT CODE */
    public function verifySubject(Request $request)
    {
        $subject = Subject::find($request->subject_id);

        if(!$subject){
            return response()->json(['status' => false, 'message' => 'Subject not found']);
        }

        if($subject->code != $request->code){
            return response()->json(['status' => false, 'message' => 'Wrong subject code']);
        }

        \App\Models\SubjectUnlock::firstOrCreate(
            ['student_id' => session('student_id'), 'subject_id' => $subject->id],
            ['unlocked_at' => now()]
        );

        return response()->json(['status' => true, 'subject_id' => $subject->id]);
    }

    /* NOTES PAGE - FIXED */
 public function notes($id)
{
    $subject = Subject::findOrFail($id);
    $notes = Note::where('subject_id', $id)->get();

    // ✅ ADD THIS
    $assignments = Assignment::where('subject_id', $id)->get();

    $student = Student::with(['course','level'])
        ->find(session('student_id'));

    if (!$student) {
        return redirect('/login');
    }

    return view('student.notes', [
        'subject' => $subject,
        'notes' => $notes,
        'assignments' => $assignments, // ✅ ADD THIS
        'student' => $student,
    ]);
}

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
public function assignments($id)
{
    $subject = Subject::findOrFail($id);

    $assignments = Assignment::where('subject_id', $id)->get();

    // 🔥 IMPORTANT: get logged-in student
    $student = Student::find(session('student_id'));

    return view('student.assignments', compact(
        'subject',
        'assignments',
        'student'
    ));
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