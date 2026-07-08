<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Level extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'name'];

    // Relationship: Level belongs to Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Optional: Level has many Semesters (if needed)
    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }
}