@extends('layouts.admin')
@section('title')
Edit Assignment
@endsection
@section('back', route('admin.subjects.assignments.index', $subject->id))
@push('styles')
<style>
.form-check-input { width:18px; height:18px; cursor:pointer;
}
.form-check-label { cursor:pointer;
}
.current-file { font-size:13px; color:#64748b; margin-bottom:8px;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Assignment</h3>
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
        <form action="{{ route('admin.subjects.assignments.update', [$subject->id, $assignment->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $assignment->title) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" required>{{ old('description', $assignment->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Due Date</label>
                <input type="datetime-local" name="due_date" class="form-control"
                       value="{{ old('due_date', \Carbon\Carbon::parse($assignment->due_date)->format('Y-m-d\TH:i')) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Total Points <span class="text-danger">*</span></label>
                <input type="number" name="total_points" class="form-control" min="0" value="{{ old('total_points', $assignment->total_points) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Submission Type</label>
                <select name="submission_type" class="form-select">
                    @foreach(['file' => 'File Upload', 'text' => 'Text'] as $value => $label)
                        <option value="{{ $value }}" {{ $assignment->submission_type == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Late Penalty (%)</label>
                <input type="number" name="late_penalty" class="form-control" min="0" max="100" value="{{ old('late_penalty', $assignment->late_penalty) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Attachment (assignment brief / instructions document)</label>

                @if($assignment->file_path)
                    <div class="current-file">
                        Current file:
                        <a href="{{ Storage::disk('public')->url($assignment->file_path) }}" target="_blank">
                            {{ basename($assignment->file_path) }}
                        </a>
                    </div>
                @endif

                <input type="file" name="assignment_file" class="form-control">
            </div>

            <button class="btn btn-navy w-100">Update Assignment</button>
        </form>
    </div>
</div>
@endsection
