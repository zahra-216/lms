<?php

namespace App\Http\Controllers\Student;

use App\Models\Quiz;
use App\Models\Student;
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
        $student = Student::find(session('student_id'));

        if (!$student) {
            return redirect()->route('login')->with('error', 'Please log in again');
        }

        $subject = $student->subjects()->findOrFail($subjectId);
        $quizzes = $subject->quizzes()->where('is_published', true)->get();

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
        $student = Student::find(session('student_id'));

        if (!$student) {
            return redirect()->route('login')->with('error', 'Please log in again');
        }

        if (!$quiz->isAvailable()) {
            return back()->with('error', 'Quiz is not available for taking');
        }

        $attempts = $student->quizSubmissions()
            ->where('quiz_id', $quiz->id)
            ->count();

        if ($attempts >= $quiz->max_attempts) {
            return back()->with('error', 'You have exceeded maximum attempts for this quiz');
        }

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
        $student = Student::find(session('student_id'));

        if (!$student) {
            return redirect()->route('login')->with('error', 'Please log in again');
        }

        if ($submission->student_id !== $student->id) {
            abort(403);
        }

        if ($submission->status !== 'in_progress') {
            return redirect()->route('student.quiz.result', $submission->id);
        }

        $quiz = $submission->quiz;
        $questions = $quiz->questions()->get();

        $submittedAnswers = $submission->answers()->pluck('answer_text', 'quiz_question_id')
            ->toArray();

        $startedAt = $submission->started_at;
        $timeElapsed = now()->diffInMinutes($startedAt);
        $timeRemaining = max(0, $quiz->duration_minutes - $timeElapsed);

        if ($timeRemaining <= 0 && !$submission->submitted_at) {
            return redirect()->route('student.quiz.submit', $submission->id);
        }

        return view('student.quizzes.attempt', compact('submission', 'quiz', 'questions', 'timeRemaining', 'submittedAnswers'));
    }

    // Submit quiz
    public function submit(Request $request, QuizSubmission $submission)
    {
        $student = Student::find(session('student_id'));

        if (!$student) {
            return redirect()->route('login')->with('error', 'Please log in again');
        }

        if ($submission->student_id !== $student->id) {
            abort(403);
        }

        if ($submission->status !== 'in_progress') {
            return back()->with('error', 'Quiz is already submitted');
        }

        $quiz = $submission->quiz;
        $rules = [];

        foreach ($quiz->questions as $question) {
            if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
                $rules['answers.' . $question->id] = 'required|exists:quiz_answers,id';
            } else {
                $rules['answers.' . $question->id] = 'required|string';
            }
        }

        $validated = $request->validate($rules);

        $automaticScore = 0;

        foreach ($quiz->questions as $question) {
            $answer = $validated['answers'][$question->id] ?? null;

            if (!$answer) {
                continue;
            }

            if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
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
                QuizSubmissionAnswer::create([
                    'quiz_submission_id' => $submission->id,
                    'quiz_question_id' => $question->id,
                    'answer_text' => $answer,
                    'is_correct' => null,
                ]);
            }
        }

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
        $student = Student::find(session('student_id'));

        if (!$student) {
            return redirect()->route('login')->with('error', 'Please log in again');
        }

        if ($submission->student_id !== $student->id) {
            abort(403);
        }

        $quiz = $submission->quiz;
        $answers = $submission->answers()->with('question', 'answer')->get();

        $answersGrouped = $answers->groupBy('quiz_question_id');

        $canShowAnswers = $submission->isGraded() && $quiz->show_correct_answers;

        return view('student.quizzes.result', compact('submission', 'quiz', 'answers', 'answersGrouped', 'canShowAnswers'));
    }
}