<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableRow extends Model
{
    protected $fillable = ['timetable_id', 'day', 'start_time', 'end_time', 'subject_id', 'lecturer_id', 'note'];

    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function timeRange(): string
    {
        return Timetable::timeRange($this->start_time, $this->end_time);
    }
}
