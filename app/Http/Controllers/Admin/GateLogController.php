<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GateLog;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GateLogController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $logs = GateLog::with('device')
            ->whereDate('scanned_at', $date)
            ->orderByDesc('scanned_at')
            ->get();

        // Resolve names in bulk instead of one query per row
        $studentIds = $logs->where('user_type', 'student')->pluck('user_id')->unique();
        $lecturerIds = $logs->where('user_type', 'lecturer')->pluck('user_id')->unique();

        $students = Student::whereIn('id', $studentIds)->get()->keyBy('id');
        $lecturers = Lecturer::whereIn('id', $lecturerIds)->get()->keyBy('id');

        $logs = $logs->map(function ($log) use ($students, $lecturers) {
            if ($log->user_type === 'student') {
                $person = $students->get($log->user_id);
                $log->person_name = $person->name ?? 'Unknown Student';
                $log->person_ref = $person->registration_no ?? '';
            } else {
                $person = $lecturers->get($log->user_id);
                $log->person_name = $person->name ?? 'Unknown Lecturer';
                $log->person_ref = $person->username ?? '';
            }
            return $log;
        });

        return view('admin.gate-log.index', compact('logs', 'date'));
    }
}