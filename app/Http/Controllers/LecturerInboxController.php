<?php

namespace App\Http\Controllers;

class LecturerInboxController extends Controller
{
    public function index()
    {
        $lecturer = auth('lecturer')->user();
        $notifications = $lecturer->notifications()->latest()->paginate(15);

        return view('lecturer.notifications.index', compact('lecturer', 'notifications'));
    }
}