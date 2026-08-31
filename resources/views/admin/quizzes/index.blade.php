@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-puzzle"></i> Quizzes - {{ $subject->name }}</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.quizzes.create', $subject->id) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create Quiz
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Quizzes List -->
    @if($quizzes->count() > 0)
        <div class="row">
            @foreach($quizzes as $quiz)
            <div class="col-md-6 mb-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $quiz->title }}</h5>
                            <span class="badge {{ $quiz->is_published ? 'bg-success' : 'bg-warning' }}">
                                {{ $quiz->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                        <p class="card-text text-muted small">{{ Str::limit($quiz->description, 100) }}</p>

                        <div class="small text-muted mb-2">
                            <div><i class="bi bi-question-circle"></i> {{ $quiz->questions->count() }} Questions</div>
                            <div><i class="bi bi-hourglass-split"></i> {{ $quiz->duration_minutes }} mins</div>
                            <div><i class="bi bi-award"></i> {{ $quiz->total_points }} points</div>
                            @if($quiz->start_date)
                                <div><i class="bi bi-calendar-event"></i> Start: {{ $quiz->start_date->format('M d, Y H:i') }}</div>
                            @endif
                        </div>

                        @if($quiz->canBeEdited())
                            <div class="status-badge mb-3">
                                <span class="badge bg-info">Can be edited</span>
                            </div>
                        @else
                            <div class="status-badge mb-3">
                                <span class="badge bg-danger">Quiz started - Cannot edit</span>
                            </div>
                        @endif

                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('admin.quizzes.show', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                            @if($quiz->canBeEdited())
                            <a href="{{ route('admin.quizzes.edit', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" 
                               class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('admin.quizzes.destroy', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" 
                                  method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-3">No quizzes yet. <a href="{{ route('admin.quizzes.create', $subject->id) }}">Create one now!</a></p>
        </div>
    @endif
</div>
@endsection
