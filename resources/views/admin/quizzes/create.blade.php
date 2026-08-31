@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <a href="{{ route('admin.quizzes.index', $subject->id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Quizzes
    </a>

    <div class="page-header mb-4">
        <h3><i class="bi bi-plus-circle"></i> Create New Quiz</h3>
        <p class="text-muted">{{ $subject->name }}</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.quizzes.store', $subject->id) }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Total Points <span class="text-danger">*</span></label>
                        <input type="number" name="total_points" class="form-control" min="1" value="{{ old('total_points', 100) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Duration (minutes) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_minutes" class="form-control" min="1" value="{{ old('duration_minutes', 60) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Max Attempts <span class="text-danger">*</span></label>
                        <input type="number" name="max_attempts" class="form-control" min="1" value="{{ old('max_attempts', 1) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Start Date & Time</label>
                        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">End Date & Time</label>
                        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Grading Type <span class="text-danger">*</span></label>
                        <select name="grading_type" class="form-select" required>
                            <option value="automatic">Automatic (for MC/True-False)</option>
                            <option value="manual">Manual (Lecturer)</option>
                            <option value="both">Both</option>
                        </select>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="show_correct_answers" value="1" id="showAnswers">
                    <label class="form-check-label" for="showAnswers">
                        Show correct answers to students after grading
                    </label>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="publishQuiz">
                    <label class="form-check-label" for="publishQuiz">
                        <strong>Publish Quiz</strong> (Make available for students)
                    </label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Create Quiz
                    </button>
                    <a href="{{ route('admin.quizzes.index', $subject->id) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .page-header h3 { margin: 0; }
    .page-header p { margin: 8px 0 0 0; }
</style>
@endsection
