<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'registration_no',
        'name',
        'email',
        'branch',
        'password',

        // 🔥 IMPORTANT LMS FIELDS
        'course_id',
        'level_id',
        'last_seen_at',
        'photo' // ✅ ADD THIS
    ];

    protected $hidden = [
        'password',
    ];

    // 🔥 OPTIONAL RELATIONSHIPS (RECOMMENDED)

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
    public function subjects()
{
    return $this->belongsToMany(Subject::class);
}
public function marks()
{
    return $this->hasMany(\App\Models\Mark::class);
}
}