<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoomParticipant extends Model
{
    protected $fillable = [
        'zoom_meeting_id', 'name', 'email', 'join_time', 'leave_time', 'duration_seconds', 'student_id', 'matched_by',
    ];

    protected $casts = [
        'join_time' => 'datetime',
        'leave_time' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(ZoomMeeting::class, 'zoom_meeting_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
