<?php

namespace App\Services;

use App\Models\ZoomMeeting;
use App\Models\ZoomRecording;
use Carbon\Carbon;
use Throwable;

/**
 * Turns the list of files Zoom has for a class into rows in zoom_recordings (one video per recording part).
 * The list comes from Zoom's "recording completed" message, or from asking Zoom (safety net / admin button).
 */
class ZoomRecordings
{
    /** Video types in the order we prefer them. Audio, chat, transcript and timeline files are never uploaded. */
    private const PREFERRED = [
        'shared_screen_with_speaker_view',
        'shared_screen_with_gallery_view',
        'shared_screen',
        'active_speaker',
        'gallery_view',
    ];

    /** Files smaller than this are Zoom leftovers (a recording stopped at once), not a class. */
    private const MIN_BYTES = 100 * 1024;

    public function __construct(private ZoomService $zoom)
    {
    }

    /**
     * Pick the video file of each recording part and queue it. Safe to run again: a file already known is not duplicated.
     *
     * @param  array<int, array<string, mixed>>  $files  Zoom's "recording_files"
     * @return int how many new parts were queued
     */
    public function ingest(ZoomMeeting $meeting, array $files): int
    {
        $videos = collect($files)->filter(function ($f) {
            $isVideo = strtoupper((string) ($f['file_type'] ?? $f['file_extension'] ?? '')) === 'MP4';
            $done = ! isset($f['status']) || strtolower((string) $f['status']) === 'completed';

            return $isVideo && $done && ! empty($f['id']) && (int) ($f['file_size'] ?? 0) >= self::MIN_BYTES;
        });

        // files of the same recording part share a start time: keep the best view of each
        $parts = $videos->groupBy(fn ($f) => (string) ($f['recording_start'] ?? ''))
            ->map(fn ($group) => $group->sortBy(function ($f) {
                $rank = array_search($f['recording_type'] ?? '', self::PREFERRED, true);

                return $rank === false ? 99 : $rank;
            })->first())
            ->sortKeys()
            ->values();

        $new = 0;

        foreach ($parts as $i => $f) {
            $row = ZoomRecording::firstOrNew(['file_id' => (string) $f['id']]);
            $isNew = ! $row->exists;

            if (! $isNew && in_array($row->status, ['done', 'working'], true)) {
                continue;   // already moved (or moving)
            }

            $row->fill([
                'zoom_meeting_id' => $meeting->id,
                'recording_type' => $f['recording_type'] ?? null,
                'file_size' => (int) ($f['file_size'] ?? 0),
                'recorded_from' => $this->time($f['recording_start'] ?? null),
                'recorded_to' => $this->time($f['recording_end'] ?? null),
                'part' => $i + 1,
                'download_url' => $f['download_url'] ?? $row->download_url,
            ]);

            if ($isNew) {
                $row->status = 'waiting';
                $new++;
            }

            $row->save();
        }

        return $new;
    }

    /**
     * Ask Zoom for the class's recording now and queue what it has.
     *
     * @return int|null how many new parts were queued, or null when Zoom has no recording for the class (yet)
     */
    public function check(ZoomMeeting $meeting): ?int
    {
        $meeting->update(['recordings_checked_at' => now(), 'recordings_checks' => $meeting->recordings_checks + 1]);

        $found = $this->zoom->meetingRecordings($meeting->zoom_meeting_id, $meeting->zoom_uuid);

        if (! $found || empty($found['recording_files'])) {
            return null;
        }

        return $this->ingest($meeting, $found['recording_files']);
    }

    /** Names for the video on YouTube and for the module pages. */
    public static function videoTitle(ZoomMeeting $meeting, ZoomRecording $rec, int $parts): string
    {
        $date = $meeting->attendanceDate();

        return YoutubeService::cleanTitle($meeting->topic.' - '.Carbon::parse($date)->format('d M Y').($parts > 1 ? ' - Part '.$rec->part : ''));
    }

    private function time($value): ?Carbon
    {
        try {
            return $value ? Carbon::parse($value)->setTimezone(config('app.timezone')) : null;
        } catch (Throwable) {
            return null;
        }
    }
}
