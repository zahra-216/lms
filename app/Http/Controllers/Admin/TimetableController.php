<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Lecturer;
use App\Models\Timetable;
use App\Notifications\TimetableAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    public function index(Subject $subject)
    {
        $entries = Timetable::where('subject_id', $subject->id)
            ->orderBy('start_time')
            ->get()
            ->groupBy('group_id')
            ->map(function ($rows) {
                return $rows->sortBy(fn($r) => Timetable::DAY_ORDER[$r->day] ?? 99)->values();
            });

        return view('admin.subjects.timetables.index', compact('subject', 'entries'));
    }

    public function create(Subject $subject)
    {
        $lecturers = Lecturer::orderBy('name')->get();
        return view('admin.subjects.timetables.create', [
            'subject' => $subject,
            'lecturers' => $lecturers,
            'editGroup' => null,
            'existingRows' => collect(),
        ]);
    }

    public function store(Request $request, Subject $subject)
    {
        $data = $this->validateRequest($request);

        $groupId = (string) Str::uuid();

        DB::transaction(function () use ($data, $subject, $groupId) {
            foreach ($data['days'] as $day) {
                Timetable::create([
                    'group_id' => $groupId,
                    'subject_id' => $subject->id,
                    'lecturer_id' => $data['lecturer_id'],
                    'day' => $day,
                    'start_time' => $data['start_time'][$day],
                    'end_time' => $data['end_time'][$day],
                    'content_covered' => $data['content_covered'],
                ]);
            }
        });

        $lecturer = Lecturer::find($data['lecturer_id']);
        $lecturer->notify(new TimetableAssigned($subject, 'assigned'));

        return redirect()->route('admin.subjects.timetables.index', $subject->id)
            ->with('success', 'Timetable added successfully');
    }

    public function edit(Subject $subject, $groupId)
    {
        $existingRows = Timetable::where('subject_id', $subject->id)
            ->where('group_id', $groupId)
            ->get();

        if ($existingRows->isEmpty()) {
            abort(404);
        }

        $lecturers = Lecturer::orderBy('name')->get();

        return view('admin.subjects.timetables.create', [
            'subject' => $subject,
            'lecturers' => $lecturers,
            'editGroup' => $groupId,
            'existingRows' => $existingRows,
        ]);
    }

    public function update(Request $request, Subject $subject, $groupId)
    {
        $existing = Timetable::where('subject_id', $subject->id)->where('group_id', $groupId)->first();
        if (!$existing) {
            abort(404);
        }

        $data = $this->validateRequest($request);

        DB::transaction(function () use ($data, $subject, $groupId) {
            Timetable::where('subject_id', $subject->id)->where('group_id', $groupId)->delete();

            foreach ($data['days'] as $day) {
                Timetable::create([
                    'group_id' => $groupId,
                    'subject_id' => $subject->id,
                    'lecturer_id' => $data['lecturer_id'],
                    'day' => $day,
                    'start_time' => $data['start_time'][$day],
                    'end_time' => $data['end_time'][$day],
                    'content_covered' => $data['content_covered'],
                ]);
            }
        });

        $lecturer = Lecturer::find($data['lecturer_id']);
        $lecturer->notify(new TimetableAssigned($subject, 'updated'));

        return redirect()->route('admin.subjects.timetables.index', $subject->id)
            ->with('success', 'Timetable updated successfully');
    }

    public function destroy(Subject $subject, $groupId)
    {
        Timetable::where('subject_id', $subject->id)->where('group_id', $groupId)->delete();

        return redirect()->route('admin.subjects.timetables.index', $subject->id)
            ->with('success', 'Timetable deleted successfully');
    }

    private function validateRequest(Request $request): array
    {
        $validated = $request->validate([
            'lecturer_id' => 'required|exists:lecturers,id',
            'days' => 'required|array|min:1',
            'days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|array',
            'end_time' => 'required|array',
            'content_covered' => 'nullable|string',
        ]);

        foreach ($validated['days'] as $day) {
            $request->validate([
                "start_time.$day" => 'required|date_format:H:i',
                "end_time.$day" => 'required|date_format:H:i|after:start_time.' . $day,
            ]);
        }

        return $validated;
    }
}