<?php

namespace App\Http\Controllers\Student;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\QuizSubmissionAnswer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class QuizController extends Controller
{
    // Show all quizzes for a subject (student view)
    public function index($subjectId)
    {
        $subject = auth('web')->user()->subjects()->findOrFail($subjectId);
        $quizzes = $subject->quizzes()->where('is_published', true)->get();

        // Add submission status for each quiz
        $student = auth('web')->user();
        foreach ($quizzes as $quiz) {
            $quiz->student_attempt = $student->quizSubmissions()
                ->where('quiz_id', $quiz->id)
                ->count();
            $quiz->can_attempt = $quiz->isAvailable() && $quiz->student_attempt < $quiz->max_attempts;
        }

        return view('student.quizzes.index', compact('subject', 'quizzes'));
    }

    // Start quiz (create a submission)
    public function start(Quiz $quiz)
    {
        $student = auth('web')->user();

        // Check if quiz is available
        if (!$quiz->isAvailable()) {
            return back()->with('error', 'Quiz is not available for taking');
        }

        // Check attempts
        $attempts = $student->quizSubmissions()
            ->where('quiz_id', $quiz->id)
            ->count();

        if ($attempts >= $quiz->max_attempts) {
            return back()->with('error', 'You have exceeded maximum attempts for this quiz');
        }

        // Create submission
        $submission = QuizSubmission::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'started_at' => now(),
            'attempt_number' => $attempts + 1,
            'status' => 'in_progress',
        ]);

        return redirect()->route('student.quiz.attempt', $submission->id);
    }

    // Show quiz attempt interface
    public function attempt(QuizSubmission $submission)
    {
        $student = auth('web')->user();

        // Check if submission belongs to this student
        if ($submission->student_id !== $student->id) {
            abort(403);
        }

        // Check if submission is still in progress
        if ($submission->status !== 'in_progress') {
            return redirect()->route('student.quiz.result', $submission->id);
        }

        $quiz = $submission->quiz;
        $questions = $quiz->questions()->get();

        // Load existing answers for this submission
        $submittedAnswers = $submission->answers()->pluck('answer_text', 'quiz_question_id')
            ->toArray();

        // Check time limit
        $startedAt = $submission->started_at;
        $timeElapsed = now()->diffInMinutes($startedAt);
        $timeRemaining = max(0, $quiz->duration_minutes - $timeElapsed);

        // If time is up, auto-submit
        if ($timeRemaining <= 0 && !$submission->submitted_at) {
            return redirect()->route('student.quiz.submit', $submission->id);
        }

        return view('student.quizzes.attempt', compact('submission', 'quiz', 'questions', 'timeRemaining', 'submittedAnswers'));
    }

    // Submit quiz
    public function submit(Request $request, QuizSubmission $submission)
    {
        $student = auth('web')->user();

        if ($submission->student_id !== $student->id) {
            abort(403);
        }

        if ($submission->status !== 'in_progress') {
            return back()->with('error', 'Quiz is already submitted');
        }

        // Validate answers
        $quiz = $submission->quiz;
        $rules = [];
        $messages = [];

        foreach ($quiz->questions as $question) {
            if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
                $rules['answers.' . $question->id] = 'required|exists:quiz_answers,id';
            } else {
                $rules['answers.' . $question->id] = 'required|string';
            }
        }

        $validated = $request->validate($rules, $messages);

        // Store answers
        $automaticScore = 0;

        foreach ($quiz->questions as $question) {
            $answer = $validated['answers'][$question->id] ?? null;

            if (!$answer) {
                continue;
            }

            $isCorrect = null;

            if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
                // Multiple choice or true/false
                $correctAnswer = $question->answers()->where('is_correct', true)->first();
                $isCorrect = $answer == $correctAnswer->id;

                if ($isCorrect) {
                    $automaticScore += $question->points;
                }

                QuizSubmissionAnswer::create([
                    'quiz_submission_id' => $submission->id,
                    'quiz_question_id' => $question->id,
                    'quiz_answer_id' => $answer,
                    'is_correct' => $isCorrect,
                ]);
            } else {
                // Short answer - store for manual grading
                QuizSubmissionAnswer::create([
                    'quiz_submission_id' => $submission->id,
                    'quiz_question_id' => $question->id,
                    'answer_text' => $answer,
                    'is_correct' => null, // Will be marked by lecturer
                ]);
            }
        }

        // Update submission
        $submission->update([
            'submitted_at' => now(),
            'automatic_score' => $quiz->grading_type !== 'manual' ? $automaticScore : null,
            'status' => $quiz->grading_type === 'automatic' ? 'graded' : 'submitted',
        ]);

        return redirect()->route('student.quiz.result', $submission->id)
            ->with('success', 'Quiz submitted successfully');
    }

    // Show quiz result
    public function result(QuizSubmission $submission)
    {
        $student = auth('web')->user();

        if ($submission->student_id !== $student->id) {
            abort(403);
        }

        $quiz = $submission->quiz;
        $answers = $submission->answers()->with('question', 'answer')->get();

        // Group answers by question
        $answersGrouped = $answers->groupBy('quiz_question_id');

        // Check if can show answers
        $canShowAnswers = $submission->isGraded() && $quiz->show_correct_answers;

        return view('student.quizzes.result', compact('submission', 'quiz', 'answers', 'answersGrouped', 'canShowAnswers'));
    }
}
