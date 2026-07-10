<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\LectureVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LecturerVideoController extends Controller
{
    public function create(Subject $subject)
    {
        return view('lecturer.videos.create', compact('subject'));
    }

    public function store(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:file,link',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,mkv|max:102400',
            'video_url' => 'nullable|string|max:500',
        ]);

        if ($validated['type'] === 'file') {
            if (!$request->hasFile('video_file')) {
                return back()->withErrors(['video_file' => 'Please upload a video file.'])->withInput();
            }
            $validated['video_path'] = $request->file('video_file')->store('videos', 'public');
            $validated['video_url'] = null;
        } else {
            if (!$request->filled('video_url')) {
                return back()->withErrors(['video_url' => 'Please provide a video URL.'])->withInput();
            }
            $validated['video_path'] = null;
        }

        unset($validated['video_file']);
        $validated['subject_id'] = $subject->id;

        LectureVideo::create($validated);

        return redirect()
            ->route('lecturer.subject.videos', $subject->id)
            ->with('success', 'Video added successfully');
    }

    public function edit(Subject $subject, LectureVideo $video)
    {
        return view('lecturer.videos.edit', compact('subject', 'video'));
    }

    public function update(Request $request, Subject $subject, LectureVideo $video)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:file,link',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,mkv|max:102400',
            'video_url' => 'nullable|string|max:500',
            'is_published' => 'required|boolean',
        ]);

        if ($validated['type'] === 'file') {
            if ($request->hasFile('video_file')) {
                if ($video->video_path) {
                    Storage::disk('public')->delete($video->video_path);
                }
                $validated['video_path'] = $request->file('video_file')->store('videos', 'public');
            } else {
                $validated['video_path'] = $video->video_path;
            }
            $validated['video_url'] = null;
        } else {
            if ($video->video_path) {
                Storage::disk('public')->delete($video->video_path);
            }
            $validated['video_path'] = null;
        }

        unset($validated['video_file']);
        $video->update($validated);

        return redirect()
            ->route('lecturer.subject.videos', $subject->id)
            ->with('success', 'Video updated successfully');
    }

    public function destroy(Subject $subject, LectureVideo $video)
    {
        if ($video->video_path) {
            Storage::disk('public')->delete($video->video_path);
        }

        $video->delete();

        return back()->with('success', 'Video deleted successfully');
    }
}