<?php

namespace App\Http\Controllers;

use App\Models\GateDevice;
use App\Models\GateLog;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GateScanController extends Controller
{
    // Shows the camera scanner page — tablet visits this once and bookmarks it
    public function scanner($deviceKey)
    {
        $device = GateDevice::where('device_key', $deviceKey)
            ->where('is_active', true)
            ->first();

        if (!$device) {
            abort(403, 'Invalid or inactive device.');
        }

        return view('gate-scanner', compact('device', 'deviceKey'));
    }

    // Called by AJAX every time the camera reads a QR code
    public function scan(Request $request, $deviceKey)
    {
        $device = GateDevice::where('device_key', $deviceKey)
            ->where('is_active', true)
            ->first();

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not authorized.'], 403);
        }

        $device->update(['last_used_at' => now()]);

        $token = trim($request->input('token'));

        $student = Student::where('qr_token', $token)->first();
        $lecturer = $student ? null : Lecturer::where('qr_token', $token)->first();

        if (!$student && !$lecturer) {
            return response()->json(['success' => false, 'message' => 'Card not recognized.'], 404);
        }

        $userType = $student ? 'student' : 'lecturer';
        $userId = $student ? $student->id : $lecturer->id;
        $name = $student ? $student->name : $lecturer->name;

        // Debounce — ignore a second tap of the same card within 10 seconds
        $recent = GateLog::where('user_type', $userType)
            ->where('user_id', $userId)
            ->where('scanned_at', '>=', now()->subSeconds(10))
            ->exists();

        if ($recent) {
            return response()->json(['success' => false, 'message' => 'Already scanned, please wait.'], 429);
        }

        // Decide in vs out based on today's last scan
        $lastToday = GateLog::where('user_type', $userType)
            ->where('user_id', $userId)
            ->whereDate('scanned_at', now()->toDateString())
            ->orderByDesc('scanned_at')
            ->first();

        $scanType = ($lastToday && $lastToday->scan_type === 'in') ? 'out' : 'in';

        GateLog::create([
            'user_type' => $userType,
            'user_id' => $userId,
            'scan_type' => $scanType,
            'scanned_at' => now(),
            'gate_device_id' => $device->id,
        ]);

        return response()->json([
            'success' => true,
            'name' => $name,
            'type' => $userType,
            'scan_type' => $scanType,
            'time' => now()->format('h:i A'),
        ]);
    }
}