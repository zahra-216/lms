<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Assignment;
use Illuminate\Http\Request;

class LecturerAssignmentController extends Controller
{
    public function create(Subject $subject)
    {
        return view('lecturer.assignments.create', compact('subject'));
    }

    public function store(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'total_points' => 'nullable|numeric|min:0',
            'allow_late' => 'nullable|boolean',
            'late_penalty' => 'nullable|numeric|min:0|max:100',
            'submission_type' => 'nullable|string',
            'is_published' => 'nullable|boolean',
            'assignment_file' => 'nullable|file|max:10240',
        ]);

        $validated['subject_id'] = $subject->id;
        $validated['allow_late'] = $request->boolean('allow_late');
        $validated['is_published'] = $request->boolean('is_published', true);

        if ($request->hasFile('assignment_file')) {
            $validated['file_path'] = $request->file('assignment_file')->store('assignments', 'public');
        }

        unset($validated['assignment_file']);

        Assignment::create($validated);

        return redirect()
            ->route('lecturer.subject.assignments', $subject->id)
            ->with('success', 'Assignment created successfully');
    }
}