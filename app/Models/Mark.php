<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $fillable = [
        'student_id',
        'assignment_id',
        'marks',
        'grade'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}