@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <a href="{{ route('student.quiz.index', $quiz->subject_id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Quizzes
    </a>

    <div class="page-header mb-4">
        <h3><i class="bi bi-check-circle"></i> Quiz Submitted</h3>
        <p class="text-muted">{{ $quiz->title }}</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Submission Status</h6>
                    <h3 class="text-success mb-3">
                        <i class="bi bi-check-circle-fill"></i> Submitted
                    </h3>
                    <p class="text-muted mb-0">
                        Submitted at: {{ $submission->submitted_at->format('M d, Y H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Grading Status</h6>
                    @if($submission->isGraded())
                        <h3 class="text-success mb-3">
                            <i class="bi bi-clipboard-check"></i> Graded
                        </h3>
                        <p class="mb-0">
                            <strong>Score: {{ number_format($submission->getTotalScore(), 2) }} / {{ $quiz->total_points }}</strong>
                        </p>
                    @else
                        <h3 class="text-warning mb-3">
                            <i class="bi bi-clock-history"></i> Pending
                        </h3>
                        <p class="text-muted mb-0">Waiting for grading by instructor</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($submission->isGraded())
        @if($submission->lecturer_remarks)
        <div class="card border-0 shadow-sm mb-4 border-start border-4 border-info">
            <div class="card-body">
                <h6 class="card-title text-info"><i class="bi bi-chat-left-quote"></i> Instructor Remarks</h6>
                <p class="mb-0">{{ $submission->lecturer_remarks }}</p>
            </div>
        </div>
        @endif

        @if($canShowAnswers)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-file-earmark"></i> Review Your Answers</h6>
            </div>
            <div class="card-body">
                @php $qIndex = 1; @endphp
                @foreach($answersGrouped as $questionId => $answers)
                @php
                    $question = $answers->first()->question;
                    $answer = $answers->first();
                @endphp
                <div class="mb-4 pb-4 border-bottom" @if($loop->last) style="border-bottom:none;" @endif>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6>Q{{ $qIndex }}. {{ $question->question_text }}</h6>
                        <span class="badge bg-secondary">{{ $question->points }} pts</span>
                    </div>

                    @if($question->type === 'multiple_choice' || $question->type === 'true_false')
                        <div class="bg-light p-3 rounded mb-2">
                            <strong>Your Answer:</strong> {{ $answer->answer?->answer_text ?? 'Not answered' }}<br>
                            @php $correct = $question->answers()->where('is_correct', true)->first(); @endphp
                            <strong class="text-success">Correct Answer:</strong> {{ $correct->answer_text }}
                        </div>
                        <div>
                            @if($answer->is_correct)
                                <span class="badge bg-success"><i class="bi bi-check"></i> Correct</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-x"></i> Incorrect</span>
                            @endif
                        </div>
                    @else
                        <div class="bg-light p-3 rounded mb-2">
                            <strong>Your Answer:</strong><br>
                            {{ $answer->answer_text }}
                        </div>
                        <div class="bg-light p-3 rounded">
                            <strong class="text-success">Model Answer:</strong><br>
                            {{ $question->correct_answer }}
                        </div>
                    @endif
                </div>
                @php $qIndex++; @endphp
                @endforeach
            </div>
        </div>
        @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Your answers will be available after grading is complete.
        </div>
        @endif
    @else
    <div class="alert alert-info">
        <i class="bi bi-clock-history"></i>
        Your quiz is waiting to be graded by the instructor. Check back later for your score and feedback.
    </div>
    @endif

    <div class="mt-4 text-center">
        <a href="{{ route('student.quiz.index', $quiz->subject_id) }}" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i> Back to Quizzes
        </a>
    </div>
</div>

<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
    }
</style>
@endsection
