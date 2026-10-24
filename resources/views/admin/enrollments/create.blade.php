@extends('layouts.admin')
@section('title')
Add Enrollment
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
    <h3 class="mb-4">➕ Add Enrollment</h3>

    

    

    <form method="POST" action="{{ route('admin.enrollments.store') }}">
        @csrf

        <div class="mb-3">
            <label>Student</label>
            <select name="student_id" class="form-control">
                <option value="">Select Student</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}">{{ $student->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Course</label>
            <select name="course_id" class="form-control">
                <option value="">Select Course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="enrolled">Enrolled</option>
                <option value="completed">Completed</option>
                <option value="dropped">Dropped</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="enrolled_at" class="form-control">
        </div>

        <button class="btn btn-success w-100">Save</button>
    </form>
</div>
</div>
@endsection
