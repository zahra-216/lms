<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'level_id',
        'semester_id',
        'code',
        'name',
        'description',
        'credits',
        'status',
    ];

    // Relationships
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

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
     public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
