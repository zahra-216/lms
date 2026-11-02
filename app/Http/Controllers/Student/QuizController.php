<?php

namespace App\Http\Controllers\Student;

use App\Models\Quiz;
use App\Models\Student;
use App\Models\QuizSubmission;
use App\Models\QuizSubmissionAnswer;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

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
        $quizzes = $subject->quizzes()->where('is_published', true)->withCount('questions')->get();

        $this->closeExpired($student);

        $mine = $student->quizSubmissions()
            ->whereIn('quiz_id', $quizzes->pluck('id'))
            ->get()
            ->groupBy('quiz_id');

        foreach ($quizzes as $quiz) {
            $attempts = $mine->get($quiz->id, collect());

            $quiz->student_attempt = $attempts->count();
            $quiz->resume_id = $attempts->firstWhere('status', 'in_progress')?->id;
            $quiz->last_result_id = $attempts->where('status', '!=', 'in_progress')->sortByDesc('started_at')->first()?->id;
            $quiz->can_attempt = !$quiz->resume_id && $quiz->isAvailable() && $quiz->student_attempt < $quiz->max_attempts;
        }

        return view('student.quizzes.index', compact('subject', 'quizzes'));
    }

    // Start quiz (create a submission), or carry on with the attempt that is already open
    public function start(Quiz $quiz)
    {
        $student = Student::find(session('student_id'));

        if (!$student) {
            return redirect()->route('login')->with('error', 'Please log in again');
        }

        $this->closeExpired($student);

        $open = $this->openAttempt($student, $quiz);
        if ($open) {
            return redirect()->route('student.quiz.attempt', $open->id);
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

        try {
            $submission = QuizSubmission::create([
                'quiz_id' => $quiz->id,
                'student_id' => $student->id,
                'started_at' => now(),
                'attempt_number' => $attempts + 1,
                'status' => 'in_progress',
            ]);
        } catch (UniqueConstraintViolationException $e) {
            // A double click started the same attempt twice: carry on with the first one.
            $open = $this->openAttempt($student, $quiz);

            return $open
                ? redirect()->route('student.quiz.attempt', $open->id)
                : back()->with('error', 'Could not start the quiz. Please try again.');
        }

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
        $questions = $quiz->questions()->with('answers')->get();

        $submittedAnswers = $submission->answers()->pluck('answer_text', 'quiz_question_id')
            ->toArray();

        // Seconds left until the deadline. (diffInSeconds is signed in Carbon 3: negative when the deadline is in the past.)
        $remainingSeconds = max(0, (int) now()->diffInSeconds($this->deadline($submission), false));

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
        $closed = $this->finalize($submission, $tooLate ? [] : ($validated['answers'] ?? []));

        // Another request (a double click) handed the quiz in first.
        if (!$closed) {
            return redirect()->route('student.quiz.result', $submission->id)
                ->with('error', 'This quiz was already submitted.');
        }

        if ($tooLate) {
            return redirect()->route('student.quiz.result', $submission->id)
                ->with('error', 'The time limit had already passed, so your answers could not be accepted.');
        }

        return redirect()->route('student.quiz.result', $submission->id)
            ->with('success', 'Quiz submitted successfully');
    }

    /** The attempt of this quiz that is still open and has time left, if any. */
    private function openAttempt(Student $student, Quiz $quiz): ?QuizSubmission
    {
        return $student->quizSubmissions()
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->latest('started_at')
            ->first();
    }

    /** Finish every attempt of this student whose time ran out (e.g. the browser was closed mid-quiz). */
    private function closeExpired(Student $student): void
    {
        $student->quizSubmissions()->where('status', 'in_progress')->get()
            ->filter(fn ($stale) => now()->greaterThan($this->deadline($stale)))
            ->each(fn ($stale) => $this->finalize($stale, []));
    }

    /** When this attempt must be handed in: the duration, but never later than the quiz end date. */
    private function deadline(QuizSubmission $submission)
    {
        $quiz = $submission->quiz;
        $deadline = $submission->started_at->copy()->addMinutes($quiz->duration_minutes);

        // An end date that is already behind the start of the attempt (lecturer closed the quiz) does not cut it short.
        if ($quiz->end_date && $quiz->end_date->gt($submission->started_at) && $quiz->end_date->lt($deadline)) {
            $deadline = $quiz->end_date->copy();
        }

        return $deadline;
    }

    /**
     * Save the answers, mark what can be marked automatically and close the attempt.
     * Returns false when the attempt was already closed (so answers are never saved twice).
     *
     * @param  array<int|string, mixed>  $answers  question id => chosen answer id (choice questions) or text (written questions)
     */
    private function finalize(QuizSubmission $submission, array $answers): bool
    {
        return DB::transaction(function () use ($submission, $answers) {
            $locked = QuizSubmission::whereKey($submission->id)->lockForUpdate()->first();

            if (!$locked || $locked->status !== 'in_progress') {
                return false;
            }

            $quiz = $locked->quiz;
            $questions = $quiz->questions()->with('answers')->get();
            $score = 0;

            foreach ($questions as $question) {
                $given = $answers[$question->id] ?? null;

                if ($given === null || $given === '') {
                    continue;
                }

                if ($question->type === 'short_answer') {
                    QuizSubmissionAnswer::create([
                        'quiz_submission_id' => $locked->id,
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
                    'quiz_submission_id' => $locked->id,
                    'quiz_question_id' => $question->id,
                    'quiz_answer_id' => $given,
                    'is_correct' => $isCorrect,
                ]);
            }

            // Manual quizzes and written answers always wait for the lecturer.
            $needsLecturer = $quiz->grading_type === 'manual' || $questions->contains('type', 'short_answer');

            $locked->update([
                'submitted_at' => now()->min($this->deadline($locked)->addSeconds(60)),
                'automatic_score' => $quiz->grading_type !== 'manual' ? $score : null,
                'status' => $needsLecturer ? 'submitted' : 'graded',
            ]);

            return true;
        });
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

        // Nothing to show until the quiz is handed in: go back to the attempt (it closes itself if time ran out).
        if ($submission->status === 'in_progress') {
            return redirect()->route('student.quiz.attempt', $submission->id);
        }

        $quiz = $submission->quiz;
        $questions = $quiz->questions()->with('answers')->get();
        $givenAnswers = $submission->answers()->with('answer')->get()->keyBy('quiz_question_id');

        $canShowAnswers = $submission->isGraded() && $quiz->show_correct_answers;

        return view('student.quizzes.result', compact('submission', 'quiz', 'questions', 'givenAnswers', 'canShowAnswers'));
    }
}
