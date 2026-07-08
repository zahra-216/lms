<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Level;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = ['level_id', 'course_id', 'name'];

    // Level relation
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    // Course relation
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // 🔥 IMPORTANT FIX: Subjects relation
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'semester_id');
    }
}