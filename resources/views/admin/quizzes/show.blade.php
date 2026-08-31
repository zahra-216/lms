@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <a href="{{ route('admin.quizzes.index', $subject->id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header mb-4">
        <h3><i class="bi bi-puzzle-fill"></i> {{ $quiz->title }}</h3>
        <p class="text-muted">{{ $subject->name }}</p>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-primary"><i class="bi bi-info-circle"></i> Info</h6>
                    <hr>
                    <dl class="row small mb-0">
                        <dt class="col-6">Status:</dt>
                        <dd class="col-6"><span class="badge {{ $quiz->is_published ? 'bg-success' : 'bg-warning' }}">{{ $quiz->is_published ? 'Pub' : 'Draft' }}</span></dd>
                        <dt class="col-6">Questions:</dt>
                        <dd class="col-6">{{ $questions->count() }}</dd>
                        <dt class="col-6">Duration:</dt>
                        <dd class="col-6">{{ $quiz->duration_minutes }}m</dd>
                        <dt class="col-6">Points:</dt>
                        <dd class="col-6">{{ $quiz->total_points }}</dd>
                        <dt class="col-6">Attempts:</dt>
                        <dd class="col-6">{{ $quiz->max_attempts }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-success"><i class="bi bi-graph-up"></i> Analytics</h6>
                    <hr>
                    <div class="text-center mb-3">
                        <div class="small text-muted">Attempts</div>
                        <h3 class="text-primary">{{ $analytics['total_attempts'] }}</h3>
                    </div>
                    <div class="text-center">
                        <div class="small text-muted">Students</div>
                        <h3 class="text-info">{{ $analytics['unique_students'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-warning"><i class="bi bi-sliders"></i> Actions</h6>
                    <hr>
                    <div class="d-grid gap-2">
                        @if($quiz->canBeEdited())
                        <a href="{{ route('admin.quizzes.edit', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.quizzes.destroy', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Delete?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                        @else
                        <div class="alert alert-danger small mb-0">
                            <i class="bi bi-exclamation"></i> Quiz started
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="bi bi-list"></i> Questions</h6>
        </div>
        <div class="card-body">
            @foreach($questions as $index => $q)
            <div class="mb-4 pb-4 border-bottom" @if($loop->last) style="border-bottom:none;" @endif>
                <div class="d-flex justify-content-between">
                    <h6>{{ $index + 1 }}. {{ Str::limit($q->question_text, 80) }}</h6>
                    <span class="badge bg-secondary">{{ $q->points }}pts</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0"><i class="bi bi-file-check"></i> Submissions ({{ $submissions->count() }})</h6>
        </div>
        <div class="card-body">
            @if($submissions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $sub)
                        <tr>
                            <td>{{ $sub->student->name }}</td>
                            <td><span class="badge {{ $sub->status === 'graded' ? 'bg-success' : 'bg-info' }}">{{ ucfirst($sub->status) }}</span></td>
                            <td>{{ $sub->isGraded() ? number_format($sub->getTotalScore(), 2) : '—' }} / {{ $quiz->total_points }}</td>
                            <td>
                                <a href="{{ route('admin.quizzes.submissions.grade', ['subject' => $subject->id, 'quiz' => $quiz->id, 'submission' => $sub->id]) }}" class="btn btn-sm btn-outline-primary">
                                    Grade
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted text-center py-5">No submissions</p>
            @endif
        </div>
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
