@extends('layouts.admin')
@section('title')
Create Assignment
@endsection
@push('styles')
<style>
.form-check-input { width:18px; height:18px; cursor:pointer;
}
.form-check-label { cursor:pointer;
}
</style>
@endpush
@section('content')
<div class="container">
    <a href="{{ route('admin.subjects.show', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <h3><i class="bi bi-journal-plus"></i> Create Assignment</h3>
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
        <form action="{{ route('admin.subjects.assignments.store', $subject->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Due Date</label>
                <input type="datetime-local" name="due_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Total Points <span class="text-danger">*</span></label>
                <input type="number" name="total_points" class="form-control" min="0" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Submission Type</label>
                <select name="submission_type" class="form-select">
                    <option value="file">File Upload</option>
                    <option value="text">Text</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Late Penalty (%)</label>
                <input type="number" name="late_penalty" class="form-control" min="0" max="100">
            </div>

            <div class="mb-3">
                <label class="form-label">Attachment (assignment brief / instructions document)</label>
                <input type="file" name="assignment_file" class="form-control">
            </div>

            <button class="btn btn-navy w-100">Create Assignment</button>
        </form>
    </div>
</div>
@endsection
