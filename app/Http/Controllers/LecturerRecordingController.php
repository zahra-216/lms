<?php

namespace App\Http\Controllers;

use App\Models\Recording;
use App\Models\Subject;
use Illuminate\Http\Request;

class LecturerRecordingController extends Controller
{
    public function index(Subject $subject)
    {
        $recordings = Recording::where('subject_id', $subject->id)->latest()->get();
        return view('lecturer.subject.recordings.index', compact('subject', 'recordings'));
    }

    public function create(Subject $subject)
    {
        return view('lecturer.subject.recordings.create', compact('subject'));
    }

    public function store(Request $request, Subject $subject)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'youtube_video_id' => 'required|string|max:255',
        ]);

        Recording::create([
            'subject_id' => $subject->id,
            'title' => $request->title,
            'youtube_video_id' => $this->extractVideoId($request->youtube_video_id),
        ]);

        return redirect()->route('lecturer.subject.recordings', $subject->id)
            ->with('success', 'Recording added successfully!');
    }

    public function edit(Subject $subject, Recording $recording)
    {
        return view('lecturer.subject.recordings.edit', compact('subject', 'recording'));
    }

    public function update(Request $request, Subject $subject, Recording $recording)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'youtube_video_id' => 'required|string|max:255',
        ]);

        $recording->update([
            'title' => $request->title,
            'youtube_video_id' => $this->extractVideoId($request->youtube_video_id),
        ]);

        return redirect()->route('lecturer.subject.recordings', $subject->id)
            ->with('success', 'Recording updated successfully!');
    }

    public function destroy(Subject $subject, Recording $recording)
    {
        $recording->delete();
        return redirect()->route('lecturer.subject.recordings', $subject->id)
            ->with('success', 'Recording deleted!');
    }

    private function extractVideoId($input)
    {
        $input = trim($input);

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $input, $matches)) {
            return $matches[1];
        }

        return $input;
    }
}