<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GateLog;
use App\Models\Student;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        return view('admin.scan.index');
    }

    private function student(Request $request): ?Student
    {
        $token = trim((string) $request->input('token'));

        return $token === '' ? null : Student::with('course')->where('qr_token', $token)->first();
    }

    private function todayCount(Student $student): int
    {
        return GateLog::where('user_type', 'student')
            ->where('user_id', $student->id)
            ->whereDate('scanned_at', now()->toDateString())
            ->count();
    }

    private function details(Student $student, string $next): array
    {
        return [
            'name' => $student->name,
            'ref' => $student->registration_no,
            'course' => $student->course->name ?? '-',
            'next' => $next,
            'date' => now()->format('d M Y'),
            'time' => now()->format('h:i A'),
        ];
    }

    // Step 1: QR scanned, show who it is
    public function verify(Request $request)
    {
        $student = $this->student($request);

        if (!$student) {
            return response()->json(['message' => 'QR code not recognized.'], 404);
        }

        $count = $this->todayCount($student);

        if ($count >= 2) {
            return response()->json(['message' => $student->name . ' has already checked in and out today.'], 422);
        }

        return response()->json($this->details($student, $count === 0 ? 'in' : 'out'));
    }

    // Step 2: Check In / Check Out clicked
    public function mark(Request $request)
    {
        $student = $this->student($request);

        if (!$student) {
            return response()->json(['message' => 'QR code not recognized.'], 404);
        }

        $count = $this->todayCount($student);

        if ($count >= 2) {
            return response()->json(['message' => 'Already checked in and out today.'], 422);
        }

        $recent = GateLog::where('user_type', 'student')
            ->where('user_id', $student->id)
            ->where('scanned_at', '>=', now()->subSeconds(10))
            ->exists();

        if ($recent) {
            return response()->json(['message' => 'Already recorded, please wait.'], 429);
        }

        $type = $count === 0 ? 'in' : 'out';

        GateLog::create([
            'user_type' => 'student',
            'user_id' => $student->id,
            'scan_type' => $type,
            'scanned_at' => now(),
        ]);

        return response()->json($this->details($student, $type) + ['scan_type' => $type]);
    }
}