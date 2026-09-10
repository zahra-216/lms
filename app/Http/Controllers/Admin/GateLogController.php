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
            ->orderBy('scanned_at')
            ->get();

        $studentIds = $logs->where('user_type', 'student')->pluck('user_id')->unique();
        $lecturerIds = $logs->where('user_type', 'lecturer')->pluck('user_id')->unique();

        $students = Student::whereIn('id', $studentIds)->get()->keyBy('id');
        $lecturers = Lecturer::whereIn('id', $lecturerIds)->get()->keyBy('id');

        // Group by user, then work out first "in" and last "out" for that day
        $grouped = $logs->groupBy(fn($log) => $log->user_type . '-' . $log->user_id)
            ->map(function ($userLogs) use ($students, $lecturers) {
                $first = $userLogs->first();

                if ($first->user_type === 'student') {
                    $person = $students->get($first->user_id);
                    $name = $person->name ?? 'Unknown Student';
                    $ref = $person->registration_no ?? '';
                } else {
                    $person = $lecturers->get($first->user_id);
                    $name = $person->name ?? 'Unknown Lecturer';
                    $ref = $person->username ?? '';
                }

                $checkIn = $userLogs->firstWhere('scan_type', 'in');
                $checkOut = $userLogs->where('scan_type', 'out')->last();

                return (object) [
                    'user_type' => $first->user_type,
                    'user_id' => $first->user_id,
                    'name' => $name,
                    'ref' => $ref,
                    'check_in' => $checkIn?->scanned_at,
                    'check_out' => $checkOut?->scanned_at,
                    'device' => $first->device->name ?? '—',
                ];
            })
            ->sortBy('name')
            ->values();

        return view('admin.gate-log.index', compact('grouped', 'date'));
    }

    public function destroy(Request $request, $userType, $userId)
    {
        $date = $request->query('date', now()->toDateString());

        GateLog::where('user_type', $userType)
            ->where('user_id', $userId)
            ->whereDate('scanned_at', $date)
            ->delete();

        return redirect()->route('admin.gate-log.index', ['date' => $date])
            ->with('success', 'Record deleted.');
    }
}