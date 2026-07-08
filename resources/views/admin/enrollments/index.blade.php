<!DOCTYPE html>
<html>
<head>
<title>Enrollments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-5">

<div class="d-flex justify-content-between mb-3">
    <h2>📚 Enrollments</h2>
    <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary">+ Add</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<!-- FILTER -->
<form method="GET" action="{{ route('admin.enrollments.index') }}" class="mb-3 p-3 border rounded">

<div class="row">

    <div class="col-md-2">
        <select name="student_id" class="form-control">
            <option value="">Student</option>
            @foreach($students as $student)
                <option value="{{ $student->id }}">{{ $student->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <select name="course_id" class="form-control">
            <option value="">Course</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}">{{ $course->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <select name="status" class="form-control">
            <option value="">Status</option>
            <option value="enrolled">Enrolled</option>
            <option value="completed">Completed</option>
            <option value="dropped">Dropped</option>
        </select>
    </div>

    <div class="col-md-2">
        <input type="date" name="from_date" class="form-control">
    </div>

    <div class="col-md-2">
        <input type="date" name="to_date" class="form-control">
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary w-100 mb-1">Filter</button>

        <a href="{{ route('admin.enrollments.pdf', request()->all()) }}"
           class="btn btn-danger w-100">
            PDF
        </a>
    </div>

</div>
</form>

<!-- TABLE -->
<table class="table table-bordered">
<thead class="table-dark">
<tr>
    <th>#</th>
    <th>Student</th>
    <th>Course</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
@foreach($enrollments as $key => $enrollment)
<tr>
    <td>{{ $key+1 }}</td>
    <td>{{ $enrollment->student->name }}</td>
    <td>{{ $enrollment->course->name }}</td>
    <td>{{ $enrollment->status }}</td>
    <td>{{ $enrollment->enrolled_at }}</td>
    <td>
        <a href="{{ route('admin.enrollments.edit',$enrollment->id) }}" class="btn btn-primary btn-sm">Edit</a>

        <form method="POST" action="{{ route('admin.enrollments.delete',$enrollment->id) }}" style="display:inline-block">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>

</body>
</html>