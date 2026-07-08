<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AssignmentSubmission; // ✅ ADD THIS

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'due_date',
        'total_points',
        'allow_late',
        'late_penalty',
        'submission_type',
        'max_file_size',
        'is_published',
        'file_path'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'allow_late' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

   
    public function submissions()
{
    return $this->hasMany(\App\Models\AssignmentSubmission::class);
}
public function marks()
{
    return $this->hasMany(\App\Models\Mark::class);
}
}