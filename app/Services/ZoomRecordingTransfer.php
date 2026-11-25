<?php

namespace App\Services;

use App\Models\Recording;
use App\Models\ZoomRecording;
use App\Support\YoutubeSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Moves one class recording from the Zoom cloud to the campus YouTube channel (Unlisted), then
 *  - makes a Recording for every module of the class, so students see it on the module's Recordings page,
 *  - adds it to the playlist of each programme (course + level).
 * The video file is only on the server while it is being moved, and is deleted afterwards.
 * Safe to run again: every step remembers what it has done, so a failed run carries on where it stopped.
 */
class ZoomRecordingTransfer
{
    public const MAX_ATTEMPTS = 6;

    public function __construct(
        private ZoomService $zoom,
        private YoutubeService $youtube,
        private YoutubePlaylistResolver $playlists,
    ) {
    }

    public static function folder(): string
    {
        return storage_path('app/zoom-recordings');
    }

    public static function pathFor(ZoomRecording $rec): string
    {
        return self::folder().'/'.$rec->id.'.mp4';
    }

    /** Take the next waiting recording, if no other one is being moved. */
    public function claimNext(): ?ZoomRecording
    {
        // something stuck for hours (the server stopped in the middle) goes back in the queue
        ZoomRecording::where('status', 'working')->where('started_at', '<', now()->subHours(4))
            ->update(['status' => 'waiting', 'step' => null]);

        if (ZoomRecording::where('status', 'working')->exists()) {
            return null;
        }

        $next = ZoomRecording::where('status', 'waiting')
            ->where(fn ($q) => $q->whereNull('next_try_at')->orWhere('next_try_at', '<=', now()))
            ->orderBy('id')->first();

        if (! $next) {
            return null;
        }

        $claimed = ZoomRecording::where('id', $next->id)->where('status', 'waiting')
            ->update(['status' => 'working', 'started_at' => now()]);

        return $claimed ? $next->fresh() : null;
    }

    /** Do the whole job for one recording. Returns done | waiting | failed. */
    public function process(ZoomRecording $rec): string
    {
        $path = self::pathFor($rec);
        $meeting = $rec->meeting()->with('subjects.course', 'subjects.level')->first();

        try {
            if (! $meeting) {
                throw new RuntimeException('The class of this recording was deleted.');
            }

            if (! $rec->youtube_video_id) {
                if (! YoutubeSettings::isConnected()) {
                    return $this->wait($rec, 'YouTube is not connected. Connect it on the YouTube Connection page.', 10, false);
                }

                $this->upload($rec, $meeting, $path);
            }

            if (! $rec->lms_done) {
                $this->makeModuleRecordings($rec, $meeting);
            }

            if ($rec->playlists === null) {
                $this->addToPlaylists($rec, $meeting);
            }

            $rec->update(['status' => 'done', 'step' => null, 'finished_at' => now(), 'next_try_at' => null, 'last_error' => null]);
            @unlink($path);

            return 'done';
        } catch (Throwable $e) {
            return $this->failedAttempt($rec, $e, $path);
        }
    }

    private function upload(ZoomRecording $rec, $meeting, string $path): void
    {
        @mkdir(self::folder(), 0775, true);

        $complete = is_file($path) && ($rec->file_size === 0 || filesize($path) === (int) $rec->file_size);

        if (! $complete) {
            $this->enoughSpace($rec);
            $rec->update(['step' => 'downloading', 'session_uri' => null]);   // a new file needs a new upload session
            $this->zoom->downloadRecording((string) $rec->download_url, $path);

            if ($rec->file_size && filesize($path) !== (int) $rec->file_size) {
                @unlink($path);
                throw new RuntimeException('The video from Zoom was incomplete. It will be downloaded again.');
            }
        }

        $rec->update(['step' => 'uploading']);

        $title = ZoomRecordings::videoTitle($meeting, $rec, $meeting->recordings()->count());
        $modules = $meeting->subjects->map(fn ($s) => $s->code.' - '.$s->name)->implode("\n");
        $description = "Class recording from TT Metro Campus.\n\nModules:\n{$modules}";

        if (! $rec->session_uri) {
            $rec->update(['session_uri' => $this->youtube->startUpload($path, $title, $description, 'unlisted', 'video/mp4')]);
        }

        $id = $this->youtube->sendFile($rec->session_uri, $path, 'video/mp4');
        $rec->update(['youtube_video_id' => $id, 'session_uri' => null, 'step' => null]);
    }

    /** The server must have room for the video while it is moved. */
    private function enoughSpace(ZoomRecording $rec): void
    {
        $free = @disk_free_space(self::folder()) ?: @disk_free_space(dirname(self::folder()));

        if ($free !== false && $rec->file_size > 0 && $free < $rec->file_size * 1.15) {
            $need = round($rec->file_size / 1048576);
            $have = round($free / 1048576);
            throw new RuntimeException("Not enough free space on the server for this video (needs about {$need} MB, {$have} MB free).");
        }
    }

    /** One Recording per module of the class: this is what students see. */
    private function makeModuleRecordings(ZoomRecording $rec, $meeting): void
    {
        $parts = $meeting->recordings()->count();
        $date = \Carbon\Carbon::parse($meeting->attendanceDate())->format('d M Y');

        DB::transaction(function () use ($rec, $meeting, $parts, $date) {
            foreach ($meeting->subjects as $subject) {
                Recording::firstOrCreate(
                    ['subject_id' => $subject->id, 'youtube_video_id' => $rec->youtube_video_id],
                    ['title' => mb_substr($subject->name.' - '.$date.($parts > 1 ? ' - Part '.$rec->part : ''), 0, 250)]
                );
            }

            $rec->update(['lms_done' => true]);
        });
    }

    /** Add the video to the playlist of every programme (course + level) the class covers. */
    private function addToPlaylists(ZoomRecording $rec, $meeting): void
    {
        $names = [];

        $pairs = $meeting->subjects->filter(fn ($s) => $s->course && $s->level)
            ->unique(fn ($s) => $s->course_id.'-'.$s->level_id);

        foreach ($pairs as $subject) {
            $names[] = $this->playlists->addVideo($subject->course, $subject->level, $rec->youtube_video_id)['title'];
        }

        $rec->update(['playlists' => implode(', ', $names)]);
    }

    private function wait(ZoomRecording $rec, string $message, int $minutes, bool $countAttempt = true): string
    {
        $rec->update([
            'status' => 'waiting', 'step' => null, 'last_error' => $message, 'next_try_at' => now()->addMinutes($minutes),
            'attempts' => $rec->attempts + ($countAttempt ? 1 : 0),
        ]);

        return 'waiting';
    }

    private function failedAttempt(ZoomRecording $rec, Throwable $e, string $path): string
    {
        $message = $e->getMessage();
        Log::warning("Zoom recording {$rec->id}: {$message}");
        $rec->refresh();
        $attempts = $rec->attempts + 1;

        // Everything is on YouTube and in the modules already: only the playlist is missing.
        // The video is watchable, so after a few tries stop and say so.
        if ($rec->youtube_video_id && $rec->lms_done && $attempts >= 3) {
            $rec->update(['status' => 'done', 'step' => null, 'attempts' => $attempts, 'playlists' => '', 'finished_at' => now(),
                'last_error' => 'The video is on YouTube and in the modules, but it could not be added to a playlist: '.$message]);
            @unlink($path);

            return 'done';
        }

        if ($attempts >= self::MAX_ATTEMPTS) {
            $rec->update(['status' => 'failed', 'step' => null, 'attempts' => $attempts, 'last_error' => $message, 'next_try_at' => null]);
            @unlink($path);

            return 'failed';
        }

        // YouTube's daily upload limit: tomorrow is the next chance. Anything else: a little later.
        $minutes = str_contains($message, 'limit reached') ? 180 : min(60, 5 * $attempts);

        $rec->update([
            'status' => 'waiting', 'step' => null, 'attempts' => $attempts, 'last_error' => $message, 'next_try_at' => now()->addMinutes($minutes),
        ]);

        return 'waiting';
    }
}
