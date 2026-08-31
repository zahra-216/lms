<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'student_id',
        'started_at',
        'submitted_at',
        'attempt_number',
        'automatic_score',
        'manual_score',
        'lecturer_remarks',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizSubmissionAnswer::class);
    }

    // Get total score (either automatic or manual, or both)
    public function getTotalScore()
    {
        if ($this->quiz->grading_type === 'both') {
            return ($this->automatic_score + $this->manual_score) / 2;
        } elseif ($this->quiz->grading_type === 'automatic') {
            return $this->automatic_score;
        }
        return $this->manual_score;
    }

    // Check if submission is graded
    public function isGraded()
    {
        if ($this->quiz->grading_type === 'automatic') {
            return $this->automatic_score !== null;
        } elseif ($this->quiz->grading_type === 'manual') {
            return $this->manual_score !== null;
        }
        // For 'both', both scores should be present
        return $this->automatic_score !== null && $this->manual_score !== null;
    }
}
