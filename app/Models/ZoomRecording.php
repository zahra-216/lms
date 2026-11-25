<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoomRecording extends Model
{
    protected $guarded = [];

    protected $casts = [
        'recorded_from' => 'datetime',
        'recorded_to' => 'datetime',
        'started_at' => 'datetime',
        'next_try_at' => 'datetime',
        'finished_at' => 'datetime',
        'lms_done' => 'boolean',
    ];

    public function meeting()
    {
        return $this->belongsTo(ZoomMeeting::class, 'zoom_meeting_id');
    }

    public function youtubeUrl(): ?string
    {
        return $this->youtube_video_id ? 'https://youtu.be/'.$this->youtube_video_id : null;
    }

    /** What the admin reads on the class page. */
    public function statusLabel(): string
    {
        return match (true) {
            $this->status === 'done' => 'On YouTube',
            $this->status === 'failed' => 'Needs attention',
            $this->status === 'working' && $this->step === 'downloading' => 'Downloading from Zoom...',
            $this->status === 'working' => 'Uploading to YouTube...',
            default => $this->attempts > 0 ? 'Will try again soon' : 'Waiting to be uploaded',
        };
    }
}
