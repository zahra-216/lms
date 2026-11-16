<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Level extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** Levels the campus offers (a level with students is never switched off). */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

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