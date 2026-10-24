@extends('layouts.admin')
@section('title')
Edit Enrollment
@endsection
@push('styles')
<style>
.card {
    border-radius: 15px;
}
</style>
@endpush
@section('content')
<div class="container py-5">
<div class="card shadow p-4">
    <h3 class="mb-4">✏️ Edit Enrollment</h3>

    <form method="POST" action="{{ route('admin.enrollments.update', $enrollment->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Student</label>
            <select name="student_id" class="form-control">
                @foreach($students as $student)
                    <option value="{{ $student->id }}"
                        {{ $enrollment->student_id == $student->id ? 'selected' : '' }}>
                        {{ $student->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Course</label>
            <select name="course_id" class="form-control">
                @foreach($courses as $course)
                    <option value="{{ $course->id }}"
                        {{ $enrollment->course_id == $course->id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="enrolled" {{ $enrollment->status == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                <option value="completed" {{ $enrollment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="dropped" {{ $enrollment->status == 'dropped' ? 'selected' : '' }}>Dropped</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="enrolled_at" class="form-control"
                   value="{{ $enrollment->enrolled_at }}">
        </div>

        <button class="btn btn-primary w-100">Update</button>
    </form>
</div>
</div>
@endsection
