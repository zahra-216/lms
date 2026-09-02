<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Step 1: list students who have at least one chat, with search
    public function index(Request $request)
    {
        $search = $request->query('search');

        $studentIds = ChatMessage::distinct()->pluck('student_id');

        $query = Student::whereIn('id', $studentIds);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('registration_no', 'like', "%{$search}%");
            });
        }

        $students = $query->get()->map(function ($student) {
            $student->thread_count = ChatMessage::where('student_id', $student->id)
                ->distinct('lecturer_id')
                ->count('lecturer_id');

            $student->unread_count = ChatMessage::where('student_id', $student->id)
                ->where('is_read', false)
                ->count();

            $student->last_message = ChatMessage::where('student_id', $student->id)
                ->latest()
                ->first();

            return $student;
        })->sortByDesc(function ($s) {
            return optional($s->last_message)->created_at;
        })->values();

        return view('admin.chats.index', compact('students', 'search'));
    }

    // Step 2: lecturers this student has chatted with
    public function studentThreads(Student $student)
    {
        $lecturerIds = ChatMessage::where('student_id', $student->id)
            ->distinct()
            ->pluck('lecturer_id');

        $lecturers = Lecturer::whereIn('id', $lecturerIds)->get()->map(function ($lecturer) use ($student) {
            $lecturer->last_message = ChatMessage::where('student_id', $student->id)
                ->where('lecturer_id', $lecturer->id)
                ->latest()
                ->first();

            $lecturer->unread_count = ChatMessage::where('student_id', $student->id)
                ->where('lecturer_id', $lecturer->id)
                ->where('is_read', false)
                ->count();

            $lecturer->msg_count = ChatMessage::where('student_id', $student->id)
                ->where('lecturer_id', $lecturer->id)
                ->count();

            return $lecturer;
        })->sortByDesc(function ($l) {
            return optional($l->last_message)->created_at;
        })->values();

        return view('admin.chats.student', compact('student', 'lecturers'));
    }

    // Step 3: the actual thread (unchanged)
    public function show(Student $student, Lecturer $lecturer)
    {
        $messages = ChatMessage::where('student_id', $student->id)
            ->where('lecturer_id', $lecturer->id)
            ->orderBy('created_at')
            ->get();

        return view('admin.chats.show', compact('student', 'lecturer', 'messages'));
    }
}