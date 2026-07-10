<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LecturerNoteController extends Controller
{
    public function create(Subject $subject)
    {
        return view('lecturer.notes.create', compact('subject'));
    }

    public function store(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'file_path' => 'nullable|file',
            'url' => 'nullable|string',
        ]);

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('notes', 'public');
        }

        $validated['subject_id'] = $subject->id;

        Note::create($validated);

        return redirect()
            ->route('lecturer.subject.notes', $subject->id)
            ->with('success', 'Note added successfully');
    }

    public function edit(Subject $subject, Note $note)
    {
        return view('lecturer.notes.edit', compact('subject', 'note'));
    }

    public function update(Request $request, Subject $subject, Note $note)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'file_path' => 'nullable|file',
            'url' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_published' => 'required|boolean',
        ]);

        if ($request->hasFile('file_path')) {
            if ($note->file_path) {
                Storage::disk('public')->delete($note->file_path);
            }
            $validated['file_path'] = $request->file('file_path')->store('notes', 'public');
        } else {
            $validated['file_path'] = $note->file_path;
        }

        $note->update($validated);

        return redirect()
            ->route('lecturer.subject.notes', $subject->id)
            ->with('success', 'Note updated successfully');
    }

    public function destroy(Subject $subject, Note $note)
    {
        if ($note->file_path) {
            Storage::disk('public')->delete($note->file_path);
        }

        $note->delete();

        return back()->with('success', 'Note deleted successfully');
    }

    public function download(Note $note)
    {
        return Storage::disk('public')->download($note->file_path);
    }
}