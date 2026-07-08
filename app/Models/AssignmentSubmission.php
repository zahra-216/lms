<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{

    protected $fillable = [
        'assignment_id',
        'student_id',
        'file',
        'comment',
        'submitted_at'
    ];
    

    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class);
    }

    public function assignment()
    {
        return $this->belongsTo(\App\Models\Assignment::class);
    }

}
