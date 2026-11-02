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

    /**
     * Final score.
     * Manual quiz: the lecturer's score for the whole quiz.
     * Automatic: the system's score for choice questions plus the lecturer's marks for the
     * written part (if any), never more than the quiz total.
     */
    public function getTotalScore()
    {
        if ($this->quiz->grading_type === 'manual') {
            return $this->manual_score;
        }

        $total = (float) ($this->automatic_score ?? 0) + (float) ($this->manual_score ?? 0);

        return min($total, (float) $this->quiz->total_points);
    }

    // Marking is finished only when the status says so (written answers wait for the lecturer)
    public function isGraded()
    {
        return $this->status === 'graded';
    }
}
