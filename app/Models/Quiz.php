<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'duration_minutes',
        'total_points',
        'max_attempts',
        'grading_type',
        'show_correct_answers',
        'is_published',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_published' => 'boolean',
        'show_correct_answers' => 'boolean',
    ];

    // Relationships
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function submissions()
    {
        return $this->hasMany(QuizSubmission::class);
    }

    // Check if quiz has started
    public function hasStarted()
    {
        return $this->start_date && $this->start_date <= now();
    }

    // Check if quiz has ended
    public function hasEnded()
    {
        return $this->end_date && $this->end_date <= now();
    }

    // Check if quiz is available for taking
    public function isAvailable()
    {
        return $this->is_published && $this->hasStarted() && !$this->hasEnded();
    }

    // Check if quiz can be edited/deleted
    public function canBeEdited()
    {
        return !$this->hasStarted();
    }
}
