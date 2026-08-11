<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'student_id',
        'certificate_number',
        'student_name',
        'father_name',
        'date_of_birth',
        'course',
        'course_start',
        'course_end',
        'award_status',
        'photo',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'course_start' => 'date',
        'course_end' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}