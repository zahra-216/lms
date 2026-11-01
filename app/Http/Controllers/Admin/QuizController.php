<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuizController extends Controller
{
    // Show all quizzes for a subject
    public function index(Subject $subject)
    {
        $quizzes = $subject->quizzes()->orderBy('created_at', 'desc')->get();
        return view('admin.quizzes.index', compact('subject', 'quizzes'));
    }

    // Show create quiz form
    public function create(Subject $subject)
    {
        return view('admin.quizzes.create', compact('subject'));
    }

    // Store new quiz
    public function store(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => $this->endDateRule($request),
            'duration_minutes' => 'required|integer|min:1',
            'total_points' => 'required|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'grading_type' => 'required|in:automatic,manual,both',
            'show_correct_answers' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['subject_id'] = $subject->id;
        $validated['show_correct_answers'] = $request->has('show_correct_answers');

        // A new quiz has no questions yet, so it is always saved as a draft; publish it after adding questions.
        $wantedPublished = $request->has('is_published');
        $validated['is_published'] = false;

        $quiz = Quiz::create($validated);

        return redirect()->route('admin.subjects.quizzes.edit', ['subject' => $subject->id, 'quiz' => $quiz->id])
            ->with('success', $wantedPublished
                ? 'Quiz saved as a draft. Add questions, then tick Publish and save.'
                : 'Quiz created successfully. Add questions now.');
    }

    /** End date must not be before the start date (when a start date is given). */
    private function endDateRule(Request $request): array
    {
        return $request->filled('start_date')
            ? ['nullable', 'date', 'after_or_equal:start_date']
            : ['nullable', 'date'];
    }

    // Show edit quiz form (with questions)
    public function edit(Subject $subject, Quiz $quiz)
    {
        // Verify quiz belongs to this subject
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        $questions = $quiz->questions()->with('answers')->get();
        $locked = !$quiz->canBeEdited();
        $questionPoints = $questions->sum('points');

        return view('admin.quizzes.edit', compact('subject', 'quiz', 'questions', 'locked', 'questionPoints'));
    }

    // Update quiz
    public function update(Request $request, Subject $subject, Quiz $quiz)
    {
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        // Once a student has attempted the quiz only the title, description, end date, attempts,
        // "show answers" and publishing can change. Questions, points, duration and grading are fixed.
        $locked = !$quiz->canBeEdited();

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_attempts' => 'required|integer|min:1',
            'show_correct_answers' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ];

        if ($locked) {
            $rules['end_date'] = ['nullable', 'date'];
            if ($quiz->start_date) {
                $rules['end_date'][] = 'after_or_equal:'.$quiz->start_date->format('Y-m-d H:i:s');
            }
        } else {
            $rules += [
                'start_date' => 'nullable|date',
                'end_date' => $this->endDateRule($request),
                'duration_minutes' => 'required|integer|min:1',
                'total_points' => 'required|integer|min:'.max(1, $quiz->questionPoints()),
                'grading_type' => 'required|in:automatic,manual,both',
            ];
        }

        $validated = $request->validate($rules, [
            'total_points.min' => 'The questions already add up to '.$quiz->questionPoints().' points, so Total Points cannot be lower than that.',
        ]);

        $validated['show_correct_answers'] = $request->has('show_correct_answers');
        $validated['is_published'] = $request->has('is_published');

        if ($validated['is_published'] && !$quiz->questions()->exists()) {
            throw ValidationException::withMessages(['is_published' => 'Add at least one question before publishing the quiz.']);
        }

        $quiz->update($validated);

        return $this->returnTo(route('admin.subjects.quizzes.show', ['subject' => $subject->id, 'quiz' => $quiz->id]))
            ->with('success', 'Quiz updated successfully');
    }

    // Show quiz details
    public function show(Subject $subject, Quiz $quiz)
    {
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        $questions = $quiz->questions()->with('answers')->get();
        $submissions = $quiz->submissions()->with('student')->get()
            ->each(fn ($submission) => $submission->setRelation('quiz', $quiz));

        // Calculate analytics (average of the marked attempts only)
        $graded = $submissions->filter(fn ($submission) => $submission->isGraded());

        $analytics = [
            'total_attempts' => $submissions->count(),
            'unique_students' => $submissions->unique('student_id')->count(),
            'average_score' => $graded->isNotEmpty() ? $graded->avg(fn ($submission) => $submission->getTotalScore()) : 0,
        ];

        return view('admin.quizzes.show', compact('subject', 'quiz', 'questions', 'submissions', 'analytics'));
    }

    // Delete quiz
    public function destroy(Subject $subject, Quiz $quiz)
    {
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        // Deleting would also delete the students' attempts and marks
        if (!$quiz->canBeEdited()) {
            return back()->with('error', 'Students have already attempted this quiz, so it cannot be deleted. Untick Publish to hide it from students.');
        }

        $quiz->delete();

        return $this->returnTo(route('admin.subjects.quizzes.index', $subject->id))
            ->with('success', 'Quiz deleted successfully');
    }

    /**
     * Check the question form: type, text, points and the correct answer.
     * Points of all questions together may not go above the quiz total.
     */
    private function validateQuestion(Request $request, Quiz $quiz, ?QuizQuestion $current = null): void
    {
        $request->validate([
            'type' => 'required|in:multiple_choice,true_false,short_answer',
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        if ($request->type === 'multiple_choice') {
            $request->validate([
                'answers' => 'required|array|min:2',
                'answers.*' => 'required|string',
                'correct_answer_index' => ['required', 'integer', Rule::in(array_keys($request->input('answers', [])))],
            ], [
                'correct_answer_index.required' => 'Select the correct answer with the radio button.',
                'correct_answer_index.in' => 'Select the correct answer with the radio button.',
            ]);
        } elseif ($request->type === 'true_false') {
            $request->validate([
                'correct_answer' => 'required|in:true,false',
            ]);
        } else {
            $request->validate([
                'correct_answer' => 'required|string',
            ]);
        }

        $others = $quiz->questions()
            ->when($current, fn ($query) => $query->where('id', '!=', $current->id))
            ->sum('points');

        if ($others + (int) $request->points > $quiz->total_points) {
            throw ValidationException::withMessages([
                'points' => 'The questions would add up to '.($others + (int) $request->points).' points, but the quiz total is '.$quiz->total_points.'. Raise Total Points first or give this question fewer points.',
            ]);
        }
    }

    // Store question
    public function storeQuestion(Request $request, Subject $subject, Quiz $quiz)
    {
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        if (!$quiz->canBeEdited()) {
            return back()->with('error', 'Students have already attempted this quiz, so questions can no longer be added.');
        }

        $this->validateQuestion($request, $quiz);

        $order = ($quiz->questions()->max('order') ?? -1) + 1;

        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'type' => $request->type,
            'question_text' => $request->question_text,
            'points' => $request->points,
            'order' => $order,
            'correct_answer' => $request->type === 'multiple_choice' ? null : $request->correct_answer,
        ]);

        $this->saveAnswers($request, $question);

        return back()->with('success', 'Question added successfully');
    }

    // Update question
    public function updateQuestion(Request $request, Subject $subject, Quiz $quiz, QuizQuestion $question)
    {
        if ($quiz->subject_id !== $subject->id || $question->quiz_id !== $quiz->id) {
            abort(403);
        }

        if (!$quiz->canBeEdited()) {
            return back()->with('error', 'Students have already attempted this quiz, so questions can no longer be changed.');
        }

        $this->validateQuestion($request, $quiz, $question);

        $question->update([
            'type' => $request->type,
            'question_text' => $request->question_text,
            'points' => $request->points,
            'correct_answer' => $request->type === 'multiple_choice' ? null : $request->correct_answer,
        ]);

        $question->answers()->delete();
        $this->saveAnswers($request, $question);

        return back()->with('success', 'Question updated successfully');
    }

    /** Create the answer options of a choice question (written questions have none). */
    private function saveAnswers(Request $request, QuizQuestion $question): void
    {
        if ($request->type === 'multiple_choice') {
            foreach (array_values($request->answers) as $index => $answer) {
                QuizAnswer::create([
                    'quiz_question_id' => $question->id,
                    'answer_text' => $answer,
                    'is_correct' => $index == $request->correct_answer_index,
                    'order' => $index,
                ]);
            }
        } elseif ($request->type === 'true_false') {
            QuizAnswer::create([
                'quiz_question_id' => $question->id,
                'answer_text' => 'True',
                'is_correct' => $request->correct_answer === 'true',
                'order' => 0,
            ]);
            QuizAnswer::create([
                'quiz_question_id' => $question->id,
                'answer_text' => 'False',
                'is_correct' => $request->correct_answer === 'false',
                'order' => 1,
            ]);
        }
    }

    // Delete question
    public function destroyQuestion(Subject $subject, Quiz $quiz, QuizQuestion $question)
    {
        if ($quiz->subject_id !== $subject->id || $question->quiz_id !== $quiz->id) {
            abort(403);
        }

        if (!$quiz->canBeEdited()) {
            return back()->with('error', 'Students have already attempted this quiz, so questions can no longer be deleted.');
        }

        $question->delete();

        // A quiz without questions cannot stay published
        if (!$quiz->questions()->exists() && $quiz->is_published) {
            $quiz->update(['is_published' => false]);
            return back()->with('success', 'Question deleted. The quiz has no questions left, so it was unpublished.');
        }

        return back()->with('success', 'Question deleted successfully');
    }

    // Show grading interface for a submission
    public function gradeSubmission(Subject $subject, Quiz $quiz, $submissionId)
    {
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        $submission = $quiz->submissions()->findOrFail($submissionId);

        if ($submission->status === 'in_progress') {
            return back()->with('error', 'The student is still taking this quiz. Grade it after it is handed in.');
        }

        $submission->setRelation('quiz', $quiz);
        $submission->load('student', 'answers.answer');
        $quiz->load('questions.answers');

        return view('admin.quizzes.grade', compact('subject', 'quiz', 'submission'));
    }

    // Save grades for a submission
    public function saveGrades(Request $request, Subject $subject, Quiz $quiz, $submissionId)
    {
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        $submission = $quiz->submissions()->findOrFail($submissionId);

        if ($submission->status === 'in_progress') {
            return back()->with('error', 'The student is still taking this quiz. Grade it after it is handed in.');
        }

        // Manual quiz: marks for the whole quiz. Otherwise: marks for the written part, added to the automatic score.
        $max = $quiz->grading_type === 'manual'
            ? $quiz->total_points
            : max(0, $quiz->total_points - (float) ($submission->automatic_score ?? 0));

        $validated = $request->validate([
            'manual_score' => 'required|numeric|min:0|max:'.$max,
            'lecturer_remarks' => 'nullable|string',
            'answer_corrections' => 'nullable|array',
            'answer_corrections.*' => 'in:0,1',
        ]);

        $submission->update([
            'manual_score' => $validated['manual_score'],
            'lecturer_remarks' => $validated['lecturer_remarks'] ?? null,
            'status' => 'graded',
        ]);

        // Update individual answer correctness (only answers of this submission)
        foreach ($validated['answer_corrections'] ?? [] as $answerId => $isCorrect) {
            $submission->answers()->whereKey($answerId)->update(['is_correct' => (bool) $isCorrect]);
        }

        return back()->with('success', 'Grades saved successfully');
    }
}
