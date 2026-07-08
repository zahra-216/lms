<!DOCTYPE html>
<html>
<head>
<title>Add Enrollment</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg,#eef2ff,#f8fafc);
}
.card {
    border-radius: 15px;
}
</style>

</head>
<body class="container py-5">

<div class="card shadow p-4">
    <h3 class="mb-4">➕ Add Enrollment</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

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

</body>
</html>