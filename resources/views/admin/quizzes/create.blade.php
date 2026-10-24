@extends('layouts.admin')
@section('title')
Create Quiz
@endsection
@push('styles')
<style>
.form-check-input { width:18px; height:18px; cursor:pointer;
}
.form-check-label { cursor:pointer;
}
.btn-outline-navy { border:1px solid #e2e8f0; color:#012147; padding:12px; font-weight:600; border-radius:10px; background:#fff;
}
.btn-outline-navy:hover { background:#012147; color:#fff;
}
</style>
@endpush
@section('content')
<div class="container">
    <a href="{{ route('admin.subjects.quizzes.index', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back to Quizzes
    </a>

    <div class="page-header">
        <h3><i class="bi bi-plus-circle"></i> Create New Quiz</h3>
        <small>{{ $subject->name }}</small>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-box">
        <form action="{{ route('admin.subjects.quizzes.store', $subject->id) }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Total Points <span class="text-danger">*</span></label>
                    <input type="number" name="total_points" class="form-control" min="1" value="{{ old('total_points', 100) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                    <input type="number" name="duration_minutes" class="form-control" min="1" value="{{ old('duration_minutes', 60) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Max Attempts <span class="text-danger">*</span></label>
                    <input type="number" name="max_attempts" class="form-control" min="1" value="{{ old('max_attempts', 1) }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date & Time</label>
                    <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date & Time</label>
                    <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Grading Type <span class="text-danger">*</span></label>
                <select name="grading_type" class="form-select" required>
                    <option value="automatic">Automatic (for MC/True-False)</option>
                    <option value="manual">Manual (Lecturer)</option>
                    <option value="both">Both</option>
                </select>
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
                <button type="submit" class="btn btn-navy flex-fill">
                    <i class="bi bi-check-circle"></i> Create Quiz
                </button>
                <a href="{{ route('admin.subjects.quizzes.index', $subject->id) }}" class="btn btn-outline-navy flex-fill text-center text-decoration-none">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
