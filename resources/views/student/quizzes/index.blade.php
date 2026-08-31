@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2><i class="bi bi-puzzle"></i> Quizzes - {{ $subject->name }}</h2>
    <p class="text-muted">Available quizzes for this subject</p>

    @if($quizzes->count() > 0)
        <div class="row">
            @foreach($quizzes as $quiz)
            <div class="col-md-6 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $quiz->title }}</h5>
                        <p class="card-text text-muted small">{{ Str::limit($quiz->description, 100) }}</p>

                        <div class="small text-muted mb-3">
                            <div><i class="bi bi-question-circle"></i> {{ $quiz->questions->count() }} Questions</div>
                            <div><i class="bi bi-hourglass-split"></i> {{ $quiz->duration_minutes }} minutes</div>
                            <div><i class="bi bi-award"></i> {{ $quiz->total_points }} points</div>
                            @if($quiz->start_date)
                            <div><i class="bi bi-calendar-event"></i> {{ $quiz->start_date->format('M d, Y H:i') }}</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            @php
                                $attempts = $quiz->student_attempt;
                                $canAttempt = $quiz->can_attempt;
                            @endphp
                            <span class="badge bg-info">Attempts: {{ $attempts }} / {{ $quiz->max_attempts }}</span>
                            @if(!$canAttempt && $attempts >= $quiz->max_attempts)
                                <span class="badge bg-danger">Max attempts reached</span>
                            @elseif(!$quiz->isAvailable())
                                <span class="badge bg-warning">Not available</span>
                            @endif
                        </div>

                        @if($canAttempt)
                        <form action="{{ route('student.quiz.start', $quiz->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-play-circle"></i> Start Quiz
                            </button>
                        </form>
                        @else
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="bi bi-lock"></i> Not Available
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-3">No quizzes available for this subject</p>
        </div>
    @endif
</div>
@endsection
