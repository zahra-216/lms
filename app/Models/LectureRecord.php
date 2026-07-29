<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LectureRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'lecturer_id',
        'content_covered',
        'date',
        'start_time',
        'end_time',
        'created_by',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    // Auto-calculated duration, e.g. "1h 30m"
    public function getDurationAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        if ($end->lessThanOrEqualTo($start)) {
            return null;
        }

        $minutes = $start->diffInMinutes($end);
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return trim(($h ? "{$h}h " : '') . ($m ? "{$m}m" : '') ?: '0m');
    }
}