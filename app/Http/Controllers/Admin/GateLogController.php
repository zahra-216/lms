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

        $logs = GateLog::where('user_type', 'student')
            ->whereDate('scanned_at', $date)
            ->orderBy('scanned_at')
            ->get();

        $students = Student::whereIn('id', $logs->pluck('user_id')->unique())->get()->keyBy('id');

        $grouped = $logs->groupBy('user_id')->map(function ($userLogs) use ($students) {
            $first = $userLogs->first();
            $student = $students->get($first->user_id);

            return (object) [
                'user_type' => 'student',
                'user_id' => $first->user_id,
                'name' => $student->name ?? 'Unknown Student',
                'ref' => $student->registration_no ?? '',
                'check_in' => $userLogs->firstWhere('scan_type', 'in')?->scanned_at,
                'check_out' => $userLogs->where('scan_type', 'out')->last()?->scanned_at,
            ];
        })->sortBy('name')->values();

        return view('admin.gate-log.index', compact('grouped', 'date'));
    }

    public function destroy(Request $request, $userType, $userId)
    {
        $date = $request->query('date', now()->toDateString());

        GateLog::where('user_type', $userType)
            ->where('user_id', $userId)
            ->whereDate('scanned_at', $date)
            ->delete();

        return $this->returnTo(route('admin.gate-log.index', ['date' => $date]))
            ->with('success', 'Record deleted.');
    }
}