<?php

namespace App\Http\Controllers\Student;

use App\Models\Quiz;
use App\Models\Student;
use App\Models\QuizSubmission;
use App\Models\QuizSubmissionAnswer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        $subject = \App\Models\Subject::findOrFail($subjectId);
        $quizzes = $subject->quizzes()->where('is_published', true)->get();

        // Finish any attempt whose time ran out (e.g. the browser was closed mid-quiz).
        $student->quizSubmissions()->where('status', 'in_progress')->get()
            ->filter(fn ($stale) => now()->greaterThan($this->deadline($stale)))
            ->each(fn ($stale) => $this->finalize($stale, []));

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
        $elapsedSeconds = now()->diffInSeconds($startedAt);
        $totalSeconds = $quiz->duration_minutes * 60;
        $remainingSeconds = max(0, $totalSeconds - $elapsedSeconds);

        if ($remainingSeconds <= 0 && !$submission->submitted_at) {
            $this->finalize($submission, []);

            return redirect()->route('student.quiz.result', $submission->id)
                ->with('error', 'Time ran out before your answers were submitted.');
        }

        return view('student.quizzes.attempt', compact('submission', 'quiz', 'questions', 'remainingSeconds', 'submittedAnswers'));
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
            return redirect()->route('student.quiz.result', $submission->id)
                ->with('error', 'This quiz was already submitted.');
        }

        $quiz = $submission->quiz;
        $questions = $quiz->questions()->with('answers')->get();

        // Blank answers are allowed: when the timer ends the page submits whatever has been written so far.
        // Long written answers are fine; the limit only guards against abuse.
        $rules = ['answers' => 'nullable|array'];
        foreach ($questions as $question) {
            $rules['answers.'.$question->id] = $question->type === 'short_answer'
                ? 'nullable|string|max:50000'
                : ['nullable', Rule::exists('quiz_answers', 'id')->where('quiz_question_id', $question->id)];
        }
        $validated = $request->validate($rules);

        // The server enforces the time limit too (a short grace covers slow connections).
        $tooLate = now()->greaterThan($this->deadline($submission)->addSeconds(60));
        $this->finalize($submission, $tooLate ? [] : ($validated['answers'] ?? []));

        if ($tooLate) {
            return redirect()->route('student.quiz.result', $submission->id)
                ->with('error', 'The time limit had already passed, so your answers could not be accepted.');
        }

        return redirect()->route('student.quiz.result', $submission->id)
            ->with('success', 'Quiz submitted successfully');
    }

    /** When this attempt must be handed in. */
    private function deadline(QuizSubmission $submission)
    {
        return $submission->started_at->copy()->addMinutes($submission->quiz->duration_minutes);
    }

    /**
     * Save the answers, mark what can be marked automatically and close the attempt.
     *
     * @param  array<int|string, mixed>  $answers  question id => chosen answer id (choice questions) or text (written questions)
     */
    private function finalize(QuizSubmission $submission, array $answers): void
    {
        $quiz = $submission->quiz;
        $questions = $quiz->questions()->with('answers')->get();
        $score = 0;

        foreach ($questions as $question) {
            $given = $answers[$question->id] ?? null;

            if ($given === null || $given === '') {
                continue;
            }

            if ($question->type === 'short_answer') {
                QuizSubmissionAnswer::create([
                    'quiz_submission_id' => $submission->id,
                    'quiz_question_id' => $question->id,
                    'answer_text' => $given,
                    'is_correct' => null, // marked by the lecturer
                ]);
                continue;
            }

            $correct = $question->answers->firstWhere('is_correct', true);
            $isCorrect = $correct && (int) $given === (int) $correct->id;
            $score += $isCorrect ? $question->points : 0;

            QuizSubmissionAnswer::create([
                'quiz_submission_id' => $submission->id,
                'quiz_question_id' => $question->id,
                'quiz_answer_id' => $given,
                'is_correct' => $isCorrect,
            ]);
        }

        // Written answers always wait for the lecturer, even on an "automatic" quiz.
        $needsLecturer = $questions->contains('type', 'short_answer');

        $submission->update([
            'submitted_at' => now()->min($this->deadline($submission)->addSeconds(60)),
            'automatic_score' => $quiz->grading_type !== 'manual' ? $score : null,
            'status' => ($quiz->grading_type === 'automatic' && !$needsLecturer) ? 'graded' : 'submitted',
        ]);
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