<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSubmissionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_submission_id',
        'quiz_question_id',
        'quiz_answer_id',
        'answer_text',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    // Relationships
    public function submission()
    {
        return $this->belongsTo(QuizSubmission::class, 'quiz_submission_id');
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    public function answer()
    {
        return $this->belongsTo(QuizAnswer::class, 'quiz_answer_id');
    }
}
