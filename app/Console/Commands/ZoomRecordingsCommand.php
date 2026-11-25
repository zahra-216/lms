<?php

namespace App\Console\Commands;

use App\Models\ZoomMeeting;
use App\Models\ZoomRecording;
use App\Services\ZoomRecordings;
use App\Services\ZoomRecordingTransfer;
use Illuminate\Console\Command;
use Throwable;

/**
 * Class recordings, from the Zoom cloud to YouTube. Run every minute by the server's cron job (schedule:run).
 *   1. Safety net: for classes that ended, ask Zoom for the recording now and then until it shows up
 *      (Zoom's "recording completed" message may be missed, and it can take up to an hour to prepare the video).
 *   2. Move waiting recordings to YouTube, one at a time (a big video takes a while).
 *   3. Tidy up: remove leftover video files from the server.
 */
class ZoomRecordingsCommand extends Command
{
    protected $signature = 'zoom:recordings {--limit=1 : How many recordings to move in this run} {--no-check : Do not ask Zoom, only move what is waiting}';

    protected $description = 'Find class recordings in Zoom and move them to YouTube and the module pages.';

    public function handle(ZoomRecordings $recordings, ZoomRecordingTransfer $transfer): int
    {
        if (! $this->option('no-check')) {
            $this->askZoom($recordings);
        }

        for ($i = 0; $i < max(1, (int) $this->option('limit')); $i++) {
            $rec = $transfer->claimNext();

            if (! $rec) {
                break;
            }

            $result = $transfer->process($rec);
            $this->line("Recording {$rec->id} (class {$rec->zoom_meeting_id}, part {$rec->part}): {$result}");
        }

        $this->tidy();

        return self::SUCCESS;
    }

    private function askZoom(ZoomRecordings $recordings): void
    {
        $due = ZoomMeeting::query()
            ->where('start_time', '>', now()->subDays(7))
            ->where('recordings_checks', '<', 14)
            ->whereDoesntHave('recordings')
            ->where(fn ($q) => $q->whereNull('recordings_checked_at')->orWhere('recordings_checked_at', '<=', now()->subMinutes(15)))
            ->get()
            ->filter(fn ($m) => $m->start_time->copy()->addMinutes($m->duration_minutes + 15)->isPast());

        foreach ($due as $meeting) {
            try {
                $found = $recordings->check($meeting);
                $this->line("Class {$meeting->id} ({$meeting->topic}): ".($found === null ? 'no recording yet' : "{$found} part(s) queued"));
            } catch (Throwable $e) {
                $this->warn("Class {$meeting->id}: {$e->getMessage()}");
            }
        }
    }

    private function tidy(): void
    {
        $folder = ZoomRecordingTransfer::folder();

        if (! is_dir($folder)) {
            return;
        }

        foreach (glob($folder.'/*.mp4') ?: [] as $file) {
            $id = (int) basename($file, '.mp4');
            $busy = ZoomRecording::where('id', $id)->whereIn('status', ['working', 'waiting'])->exists();

            // a file nobody needs, or one that has sat there for days
            if (! $busy || filemtime($file) < now()->subDays(3)->getTimestamp()) {
                @unlink($file);
            }
        }
    }
}
