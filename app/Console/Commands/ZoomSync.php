<?php

namespace App\Console\Commands;

use App\Models\ZoomMeeting;
use App\Services\ZoomAttendance;
use Illuminate\Console\Command;
use Throwable;

class ZoomSync extends Command
{
    protected $signature = 'zoom:sync';

    protected $description = 'Fetch attendance from Zoom for classes that have finished (safety net for the Zoom webhook).';

    public function handle(ZoomAttendance $attendance): int
    {
        // 1. A class that should be over long ago but Zoom never told us: treat it as ended.
        ZoomMeeting::where('status', 'scheduled')
            ->get()
            ->filter(fn ($m) => $m->start_time->copy()->addMinutes($m->duration_minutes + 60)->isPast())
            ->each(fn ($m) => $m->update([
                'status' => 'ended',
                'ended_at' => $m->start_time->copy()->addMinutes($m->duration_minutes),
                'attempts' => 0,
                'next_try_at' => null,
            ]));

        // 2. Every finished class that is waiting for its attendance.
        $due = ZoomMeeting::where('status', 'ended')
            ->where(fn ($q) => $q->whereNull('next_try_at')->orWhere('next_try_at', '<=', now()))
            ->get();

        foreach ($due as $meeting) {
            try {
                $result = $attendance->run($meeting);
            } catch (Throwable $e) {
                $result = 'error: '.$e->getMessage();
            }

            $this->line("Class {$meeting->id} ({$meeting->topic}): {$result}");
        }

        return self::SUCCESS;
    }
}
