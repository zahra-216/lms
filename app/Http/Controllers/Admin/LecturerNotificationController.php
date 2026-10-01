<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Models\Lecturer;
use App\Notifications\LecturerNotification;

class LecturerNotificationController extends Controller
{
    public function create()
    {
        $lecturers = Lecturer::orderBy('name')->get();
        return view('admin.notifications.create', compact('lecturers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'send_to' => 'required|in:all,selected',
            'lecturer_ids' => 'required_if:send_to,selected|array',
            'lecturer_ids.*' => 'exists:lecturers,id',
        ], [
            'lecturer_ids.required_if' => 'Please select at least one lecturer.',
        ]);

        $lecturers = $request->send_to === 'all'
            ? Lecturer::all()
            : Lecturer::whereIn('id', $request->lecturer_ids)->get();

        Notification::send($lecturers, new LecturerNotification($request->title, $request->message));

        return redirect()->route('admin.notifications.create')
            ->with('success', 'Notification sent to ' . $lecturers->count() . ' lecturer(s).');
    }
}