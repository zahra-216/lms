<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** The weekly timetable of one course + level + semester. */
class Timetable extends Model
{
    protected $fillable = ['course_id', 'level_id', 'semester_id', 'batch', 'mode_note'];

    const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function rows()
    {
        return $this->hasMany(TimetableRow::class);
    }

    /** Rows in week order: Monday first, earlier classes first. */
    public function sortedRows()
    {
        return $this->rows->sortBy(fn ($r) => [array_search($r->day, self::DAYS), $r->start_time])->values();
    }

    /** "9.00 - 10.00 pm", or "11.00 am - 12.00 pm" when the class crosses noon. */
    public static function timeRange(string $start, string $end): string
    {
        $a = \Carbon\Carbon::createFromFormat('H:i:s', strlen($start) === 5 ? $start.':00' : $start);
        $b = \Carbon\Carbon::createFromFormat('H:i:s', strlen($end) === 5 ? $end.':00' : $end);

        return $a->format('a') === $b->format('a')
            ? $a->format('g.i').' - '.$b->format('g.i a')
            : $a->format('g.i a').' - '.$b->format('g.i a');
    }
}
