<?php

namespace App\Http\Controllers;

use App\Models\TimetableRow;

class LecturerTimetableController extends Controller
{
    /** Every class where the signed-in lecturer was chosen, in week order. */
    public function index()
    {
        $rows = TimetableRow::with(['subject', 'timetable.course', 'timetable.level', 'timetable.semester'])
            ->where('lecturer_id', auth('lecturer')->id())
            ->get()
            ->sortBy(fn ($r) => [array_search($r->day, \App\Models\Timetable::DAYS), $r->start_time])
            ->values();

        return view('lecturer.timetable', compact('rows'));
    }
}
