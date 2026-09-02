<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    private function currentStudent()
    {
        return Student::find(session('student_id'));
    }

    public function index()
    {
        $student = $this->currentStudent();
        if (!$student) {
            return redirect()->route('login')->with('error', 'Please log in again');
        }

        $lecturers = Lecturer::all()->map(function ($lecturer) use ($student) {
            $lecturer->last_message = ChatMessage::where('student_id', $student->id)
                ->where('lecturer_id', $lecturer->id)
                ->latest()
                ->first();

            $lecturer->unread_count = ChatMessage::where('student_id', $student->id)
                ->where('lecturer_id', $lecturer->id)
                ->where('sender_type', 'lecturer')
                ->where('is_read', false)
                ->count();

            return $lecturer;
        })->sortByDesc(function ($lecturer) {
            return optional($lecturer->last_message)->created_at;
        })->values();

        return view('student.chat.index', compact('lecturers'));
    }

    public function show(Lecturer $lecturer)
    {
        $student = $this->currentStudent();
        if (!$student) {
            return redirect()->route('login')->with('error', 'Please log in again');
        }

        $messages = ChatMessage::where('student_id', $student->id)
            ->where('lecturer_id', $lecturer->id)
            ->orderBy('created_at')
            ->get();

        ChatMessage::where('student_id', $student->id)
            ->where('lecturer_id', $lecturer->id)
            ->where('sender_type', 'lecturer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('student.chat.show', compact('lecturer', 'messages', 'student'));
    }

    public function store(Request $request, Lecturer $lecturer)
    {
        $student = $this->currentStudent();
        if (!$student) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = ChatMessage::create([
            'student_id' => $student->id,
            'lecturer_id' => $lecturer->id,
            'sender_type' => 'student',
            'message' => $validated['message'],
        ]);

        return response()->json([
            'id' => $message->id,
            'sender_type' => 'student',
            'message' => $message->message,
            'time' => $message->created_at->format('H:i'),
        ]);
    }

    public function poll(Request $request, Lecturer $lecturer)
    {
        $student = $this->currentStudent();
        if (!$student) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $after = (int) $request->query('after', 0);

        $messages = ChatMessage::where('student_id', $student->id)
            ->where('lecturer_id', $lecturer->id)
            ->where('id', '>', $after)
            ->orderBy('created_at')
            ->get();

        ChatMessage::where('student_id', $student->id)
            ->where('lecturer_id', $lecturer->id)
            ->where('sender_type', 'lecturer')
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