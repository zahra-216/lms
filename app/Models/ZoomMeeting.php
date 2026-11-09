<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoomMeeting extends Model
{
    protected $fillable = [
        'zoom_meeting_id', 'zoom_uuid', 'topic', 'start_time', 'duration_minutes', 'join_url', 'passcode',
        'status', 'attempts', 'ended_at', 'next_try_at', 'processed_at', 'last_error', 'created_by',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'ended_at' => 'datetime',
        'next_try_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'zoom_meeting_subject');
    }

    public function participants()
    {
        return $this->hasMany(ZoomParticipant::class);
    }

    /** The day the attendance is saved under: the day the class started. */
    public function attendanceDate(): string
    {
        return $this->start_time->toDateString();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'scheduled' => 'Waiting for the class',
            'ended' => 'Fetching attendance',
            'processed' => 'Attendance saved',
            'failed' => 'Needs attention',
            default => $this->status,
        };
    }
}
