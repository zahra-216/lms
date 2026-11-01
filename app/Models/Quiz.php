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

    // Check if quiz has started (no start date means it opens as soon as it is published)
    public function hasStarted()
    {
        return ! $this->start_date || $this->start_date <= now();
    }

    // Check if quiz has ended
    public function hasEnded()
    {
        return $this->end_date && $this->end_date <= now();
    }

    // Check if quiz is available for taking (a quiz without questions cannot be taken)
    public function isAvailable()
    {
        return $this->is_published && $this->hasStarted() && !$this->hasEnded() && $this->questions()->exists();
    }

    // Has any student started an attempt?
    public function hasAttempts()
    {
        return $this->submissions()->exists();
    }

    // Questions, points, duration and grading type are locked once a student has attempted the quiz
    public function canBeEdited()
    {
        return !$this->hasAttempts();
    }

    // Sum of the points of all questions
    public function questionPoints(): int
    {
        return (int) $this->questions()->sum('points');
    }
}
