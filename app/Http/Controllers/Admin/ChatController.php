<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Lecturer;
use App\Models\Student;

class ChatController extends Controller
{
    public function index()
    {
        $pairs = ChatMessage::select('student_id', 'lecturer_id')
            ->distinct()
            ->get()
            ->map(function ($pair) {
                $student = Student::find($pair->student_id);
                $lecturer = Lecturer::find($pair->lecturer_id);

                $lastMessage = ChatMessage::where('student_id', $pair->student_id)
                    ->where('lecturer_id', $pair->lecturer_id)
                    ->latest()
                    ->first();

                $count = ChatMessage::where('student_id', $pair->student_id)
                    ->where('lecturer_id', $pair->lecturer_id)
                    ->count();

                return (object) [
                    'student' => $student,
                    'lecturer' => $lecturer,
                    'last_message' => $lastMessage,
                    'count' => $count,
                ];
            })
            ->filter(fn ($p) => $p->student && $p->lecturer)
            ->sortByDesc(fn ($p) => optional($p->last_message)->created_at)
            ->values();

        return view('admin.chats.index', compact('pairs'));
    }

    public function show(Student $student, Lecturer $lecturer)
    {
        $messages = ChatMessage::where('student_id', $student->id)
            ->where('lecturer_id', $lecturer->id)
            ->orderBy('created_at')
            ->get();

        return view('admin.chats.show', compact('student', 'lecturer', 'messages'));
    }
}