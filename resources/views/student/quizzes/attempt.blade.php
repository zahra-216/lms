@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Timer Bar -->
    <div class="alert alert-warning alert-dismissible" id="timerAlert">
        <strong><i class="bi bi-hourglass-split"></i> Time Remaining: <span id="timeDisplay">{{ $timeRemaining }}:00</span></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-3">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-puzzle"></i> {{ $quiz->title }}</h6>
                    <hr>
                    <div class="small">
                        <div class="mb-2">
                            <strong>Total Questions:</strong> {{ $questions->count() }}
                        </div>
                        <div class="mb-3">
                            <strong>Duration:</strong> {{ $quiz->duration_minutes }} minutes
                        </div>
                        <div id="timerBox" class="alert alert-info mb-0 text-center">
                            <h4 id="timer" style="margin: 0;">{{ $timeRemaining }}:00</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quiz Questions -->
        <div class="col-lg-9">
            <form action="{{ route('student.quiz.submit', $submission->id) }}" method="POST" id="quizForm">
                @csrf

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @foreach($questions as $index => $question)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="mb-3">
                            <strong>Question {{ $index + 1 }} of {{ $questions->count() }}</strong>
                            <span class="badge bg-secondary float-end">{{ $question->points }} point{{ $question->points > 1 ? 's' : '' }}</span>
                        </h6>

                        <p class="lead">{{ $question->question_text }}</p>

                        @if($question->type === 'multiple_choice')
                            @foreach($question->answers as $answer)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" 
                                       value="{{ $answer->id }}" id="answer{{ $answer->id }}"
                                       {{ old("answers.{$question->id}") == $answer->id ? 'checked' : (isset($submittedAnswers[$question->id]) && $submittedAnswers[$question->id] == $answer->id ? 'checked' : '') }}>
                                <label class="form-check-label" for="answer{{ $answer->id }}">
                                    {{ $answer->answer_text }}
                                </label>
                            </div>
                            @endforeach

                        @elseif($question->type === 'true_false')
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" 
                                       value="{{ $question->answers()->where('answer_text', 'True')->first()->id }}" 
                                       id="true{{ $question->id }}"
                                       {{ (old("answers.{$question->id}") || (isset($submittedAnswers[$question->id]) && $submittedAnswers[$question->id] === 'True')) ? 'checked' : '' }}>
                                <label class="form-check-label" for="true{{ $question->id }}">True</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" 
                                       value="{{ $question->answers()->where('answer_text', 'False')->first()->id }}" 
                                       id="false{{ $question->id }}"
                                       {{ (old("answers.{$question->id}") || (isset($submittedAnswers[$question->id]) && $submittedAnswers[$question->id] === 'False')) ? 'checked' : '' }}>
                                <label class="form-check-label" for="false{{ $question->id }}">False</label>
                            </div>

                        @else
                            <textarea name="answers[{{ $question->id }}]" class="form-control" rows="4" 
                                      placeholder="Enter your answer here...">{{ old("answers.{$question->id}") ?? ($submittedAnswers[$question->id] ?? '') }}</textarea>
                        @endif
                    </div>
                </div>
                @endforeach

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Are you sure? You cannot change your answers after submitting.')">
                        <i class="bi bi-check-circle"></i> Submit Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @media (max-width: 992px) {
        .sticky-top { position: relative !important; top: auto !important; }
    }
</style>

<script>
let timeRemaining = {{ $timeRemaining * 60 }};

function updateTimer() {
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    document.getElementById('timeDisplay').textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
    document.getElementById('timer').textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
    
    if (timeRemaining <= 0) {
        document.getElementById('quizForm').submit();
        return;
    }
    
    timeRemaining--;
}

setInterval(updateTimer, 1000);
updateTimer();
</script>
@endsection
