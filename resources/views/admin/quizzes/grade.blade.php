@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <a href="{{ route('admin.quizzes.show', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header mb-4">
        <h3><i class="bi bi-clipboard-check"></i> Grade Submission</h3>
        <p class="text-muted">{{ $subject->name }} | {{ $quiz->title }}</p>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="text-primary"><i class="bi bi-person"></i> Student</h6>
                    <hr>
                    <strong>{{ $submission->student->name }}</strong><br>
                    <small class="text-muted">{{ $submission->student->email }}</small>
                    <dl class="row small mt-3 mb-0">
                        <dt class="col-6">Attempt:</dt>
                        <dd class="col-6">{{ $submission->attempt_number }}</dd>
                        <dt class="col-6">Status:</dt>
                        <dd class="col-6"><span class="badge bg-info">{{ ucfirst($submission->status) }}</span></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <form action="{{ route('admin.quizzes.submissions.saveGrades', ['subject' => $subject->id, 'quiz' => $quiz->id, 'submission' => $submission->id]) }}" method="POST">
                @csrf

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Answers</h6>
                    </div>
                    <div class="card-body">
                        @foreach($submission->answers as $ans)
                        <div class="mb-4 pb-4 border-bottom" @if($loop->last) style="border-bottom:none;" @endif>
                            <h6>{{ $ans->question->question_text }}</h6>
                            <div class="bg-light p-3 rounded">
                                <strong>Student:</strong> {{ $ans->answer?->answer_text ?? $ans->answer_text ?? 'Not answered' }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">Grading</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Score <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="manual_score" class="form-control" min="0" max="{{ $quiz->total_points }}" 
                                       value="{{ $submission->manual_score ?? '' }}" step="0.1" required>
                                <span class="input-group-text">/ {{ $quiz->total_points }}</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Remarks</label>
                            <textarea name="lecturer_remarks" class="form-control" rows="4" placeholder="Feedback...">{{ $submission->lecturer_remarks }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> Save Grade
                        </button>
                    </div>
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
    }
</style>
@endsection
