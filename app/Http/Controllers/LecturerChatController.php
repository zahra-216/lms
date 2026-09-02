<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LecturerChatController extends Controller
{
    public function index()
    {
        $lecturer = Auth::guard('lecturer')->user();

        $studentIds = ChatMessage::where('lecturer_id', $lecturer->id)
            ->distinct()
            ->pluck('student_id');

        $students = Student::whereIn('id', $studentIds)->get()->map(function ($student) use ($lecturer) {
            $student->last_message = ChatMessage::where('lecturer_id', $lecturer->id)
                ->where('student_id', $student->id)
                ->latest()
                ->first();

            $student->unread_count = ChatMessage::where('lecturer_id', $lecturer->id)
                ->where('student_id', $student->id)
                ->where('sender_type', 'student')
                ->where('is_read', false)
                ->count();

            return $student;
        })->sortByDesc(function ($student) {
            return optional($student->last_message)->created_at;
        })->values();

        return view('lecturer.chat.index', compact('students'));
    }

    public function show(Student $student)
    {
        $lecturer = Auth::guard('lecturer')->user();

        $messages = ChatMessage::where('lecturer_id', $lecturer->id)
            ->where('student_id', $student->id)
            ->orderBy('created_at')
            ->get();

        ChatMessage::where('lecturer_id', $lecturer->id)
            ->where('student_id', $student->id)
            ->where('sender_type', 'student')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('lecturer.chat.show', compact('student', 'messages', 'lecturer'));
    }

    public function store(Request $request, Student $student)
    {
        $lecturer = Auth::guard('lecturer')->user();

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = ChatMessage::create([
            'student_id' => $student->id,
            'lecturer_id' => $lecturer->id,
            'sender_type' => 'lecturer',
            'message' => $validated['message'],
        ]);

        return response()->json([
            'id' => $message->id,
            'sender_type' => 'lecturer',
            'message' => $message->message,
            'time' => $message->created_at->format('H:i'),
        ]);
    }

    public function poll(Request $request, Student $student)
    {
        $lecturer = Auth::guard('lecturer')->user();
        $after = (int) $request->query('after', 0);

        $messages = ChatMessage::where('lecturer_id', $lecturer->id)
            ->where('student_id', $student->id)
            ->where('id', '>', $after)
            ->orderBy('created_at')
            ->get();

        ChatMessage::where('lecturer_id', $lecturer->id)
            ->where('student_id', $student->id)
            ->where('sender_type', 'student')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages->map(function ($m) {
            return [
                'id' => $m->id,
                'sender_type' => $m->sender_type,
                'message' => $m->message,
                'time' => $m->created_at->format('H:i'),
            ];
        }));
    }
}