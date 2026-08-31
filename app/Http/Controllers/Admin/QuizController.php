<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Quiz;
use App\Models\Subject;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    // Show all quizzes for a subject
    public function index(Subject $subject)
    {
        $quizzes = $subject->quizzes()->orderBy('created_at', 'desc')->get();
        return view('admin.subjects.quizzes.index', compact('subject', 'quizzes'));
    }

    // Show create quiz form
    public function create(Subject $subject)
    {
        return view('admin.subjects.quizzes.create', compact('subject'));
    }

    // Store new quiz
    public function store(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration_minutes' => 'required|integer|min:1',
            'total_points' => 'required|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'grading_type' => 'required|in:automatic,manual,both',
            'show_correct_answers' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['subject_id'] = $subject->id;
        $validated['show_correct_answers'] = $request->has('show_correct_answers');

        $quiz = Quiz::create($validated);

        return redirect()->route('admin.subjects.quizzes.edit', ['subject' => $subject->id, 'quiz' => $quiz->id])
            ->with('success', 'Quiz created successfully. Add questions now.');
    }

    // Show edit quiz form (with questions)
    public function edit(Subject $subject, Quiz $quiz)
    {
        // Verify quiz belongs to this subject
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        // Can't edit if quiz has started
        if (!$quiz->canBeEdited()) {
            return redirect()->route('admin.subjects.quizzes.show', ['subject' => $subject->id, 'quiz' => $quiz->id])
                ->with('error', 'Cannot edit quiz after start date');
        }

        $questions = $quiz->questions()->get();
        return view('admin.subjects.quizzes.edit', compact('subject', 'quiz', 'questions'));
    }

    // Update quiz
    public function update(Request $request, Subject $subject, Quiz $quiz)
    {
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        // Can't edit if quiz has started
        if (!$quiz->canBeEdited()) {
            return back()->with('error', 'Cannot edit quiz after start date');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration_minutes' => 'required|integer|min:1',
            'total_points' => 'required|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
            'grading_type' => 'required|in:automatic,manual,both',
            'show_correct_answers' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['show_correct_answers'] = $request->has('show_correct_answers');

        $quiz->update($validated);

        return redirect()->route('admin.subjects.quizzes.show', ['subject' => $subject->id, 'quiz' => $quiz->id])
            ->with('success', 'Quiz updated successfully');
    }

    // Show quiz details
    public function show(Subject $subject, Quiz $quiz)
    {
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        $questions = $quiz->questions()->get();
        $submissions = $quiz->submissions()->with('student')->get();

        // Calculate analytics
        $analytics = [
            'total_attempts' => $submissions->count(),
            'unique_students' => $submissions->unique('student_id')->count(),
            'average_score' => $submissions->avg('manual_score') ?? 0,
        ];

        return view('admin.subjects.quizzes.show', compact('subject', 'quiz', 'questions', 'submissions', 'analytics'));
    }

    // Delete quiz
    public function destroy(Subject $subject, Quiz $quiz)
    {
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        // Can't delete if quiz has started
        if (!$quiz->canBeEdited()) {
            return back()->with('error', 'Cannot delete quiz after start date');
        }

        $quiz->delete();

        return redirect()->route('admin.subjects.quizzes.index', $subject->id)
            ->with('success', 'Quiz deleted successfully');
    }

    // Store question
    public function storeQuestion(Request $request, Subject $subject, Quiz $quiz)
    {
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        if (!$quiz->canBeEdited()) {
            return back()->with('error', 'Cannot add questions after quiz has started');
        }

        $request->validate([
            'type' => 'required|in:multiple_choice,true_false,short_answer',
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        if ($request->type === 'multiple_choice') {
            $request->validate([
                'answers' => 'required|array|min:2',
                'answers.*' => 'required|string',
                'correct_answer_index' => 'required|integer',
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

        $order = $quiz->questions()->max('order') + 1 ?? 0;

        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'type' => $request->type,
            'question_text' => $request->question_text,
            'points' => $request->points,
            'order' => $order,
            'correct_answer' => $request->type === 'multiple_choice' ? null : $request->correct_answer,
        ]);

        if ($request->type === 'multiple_choice') {
            foreach ($request->answers as $index => $answer) {
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

        return back()->with('success', 'Question added successfully');
    }

    // Update question
    public function updateQuestion(Request $request, Subject $subject, Quiz $quiz, QuizQuestion $question)
    {
        if ($quiz->subject_id !== $subject->id || $question->quiz_id !== $quiz->id) {
            abort(403);
        }

        if (!$quiz->canBeEdited()) {
            return back()->with('error', 'Cannot edit questions after quiz has started');
        }

        $request->validate([
            'type' => 'required|in:multiple_choice,true_false,short_answer',
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        if ($request->type === 'multiple_choice') {
            $request->validate([
                'answers' => 'required|array|min:2',
                'answers.*' => 'required|string',
                'correct_answer_index' => 'required|integer',
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

        $question->update([
            'type' => $request->type,
            'question_text' => $request->question_text,
            'points' => $request->points,
            'correct_answer' => $request->type === 'multiple_choice' ? null : $request->correct_answer,
        ]);

        if ($request->type === 'multiple_choice') {
            $question->answers()->delete();
            foreach ($request->answers as $index => $answer) {
                QuizAnswer::create([
                    'quiz_question_id' => $question->id,
                    'answer_text' => $answer,
                    'is_correct' => $index == $request->correct_answer_index,
                    'order' => $index,
                ]);
            }
        } elseif ($request->type === 'true_false') {
            $question->answers()->delete();
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

        return back()->with('success', 'Question updated successfully');
    }

    // Delete question
    public function destroyQuestion(Subject $subject, Quiz $quiz, QuizQuestion $question)
    {
        if ($quiz->subject_id !== $subject->id || $question->quiz_id !== $quiz->id) {
            abort(403);
        }

        if (!$quiz->canBeEdited()) {
            return back()->with('error', 'Cannot delete questions after quiz has started');
        }

        $question->delete();
        return back()->with('success', 'Question deleted successfully');
    }

    // Show grading interface for a submission
    public function gradeSubmission(Subject $subject, Quiz $quiz, $submissionId)
    {
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        $submission = $quiz->submissions()->findOrFail($submissionId);
        $submission->load('student', 'answers.question', 'answers.answer');

        return view('admin.subjects.quizzes.grade', compact('subject', 'quiz', 'submission'));
    }

    // Save grades for a submission
    public function saveGrades(Request $request, Subject $subject, Quiz $quiz, $submissionId)
    {
        if ($quiz->subject_id !== $subject->id) {
            abort(403);
        }

        $submission = $quiz->submissions()->findOrFail($submissionId);

        $validated = $request->validate([
            'manual_score' => 'required|numeric|min:0|max:' . $quiz->total_points,
            'lecturer_remarks' => 'nullable|string',
            'answer_corrections' => 'nullable|array',
        ]);

        $submission->update([
            'manual_score' => $validated['manual_score'],
            'lecturer_remarks' => $validated['lecturer_remarks'],
            'status' => 'graded',
        ]);

        // Update individual answer correctness if provided
        if ($request->has('answer_corrections')) {
            foreach ($request->input('answer_corrections', []) as $answerId => $isCorrect) {
                \App\Models\QuizSubmissionAnswer::where('id', $answerId)
                    ->update(['is_correct' => $isCorrect]);
            }
        }

        return back()->with('success', 'Grades saved successfully');
    }
}

