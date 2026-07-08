<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Note;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    public function index(Subject $subject)
    {
        $notes = $subject->notes()->latest()->paginate(10);
        return view('admin.notes.index', compact('subject','notes'));
    }

    public function create(Subject $subject)
    {
        return view('admin.notes.create', compact('subject'));
    }

    public function store(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|string',
            'file_path' => 'nullable|file',
            'url' => 'nullable|string'
        ]);

        if($request->hasFile('file_path')){
            $validated['file_path'] = $request->file('file_path')
                ->store('notes','public');
        }

        $validated['subject_id'] = $subject->id;

        Note::create($validated);

        return redirect()
            ->route('admin.subjects.notes.index',$subject->id)
            ->with('success','Note Added Successfully');
    }

    public function edit(Subject $subject, Note $note)
    {
        return view('admin.notes.edit', compact('subject','note'));
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
        'is_published' => 'required|boolean'
    ]);

    if ($request->hasFile('file_path')) {

        if ($note->file_path) {
            Storage::disk('public')->delete($note->file_path);
        }

        $validated['file_path'] =
            $request->file('file_path')->store('notes','public');
    } else {
        $validated['file_path'] = $note->file_path;
    }

    $note->update($validated);

    return redirect()
        ->route('subjects.notes.index',$subject->id)
        ->with('success','Note Updated Successfully');
}

    public function destroy(Subject $subject, Note $note)
    {
        if($note->file_path){
            Storage::disk('public')->delete($note->file_path);
        }

        $note->delete();

        return back()->with('success','Note Deleted Successfully');
    }

    public function download(Note $note)
    {
        return Storage::disk('public')
            ->download($note->file_path);
    }
}