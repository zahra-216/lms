<!DOCTYPE html>
<html>
<head>
<title>Marks List</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background:#f4f6fb;">

<div class="container mt-5">

<div class="d-flex justify-content-between mb-3">
    <h4>📊 All Marks</h4>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<table class="table table-bordered bg-white">

<thead>
<tr>
    <th>Student</th>
    <th>Assignment</th>
    <th>Subject</th>
    <th>Marks</th>
    <th>Grade</th>
</tr>
</thead>

<tbody>

@foreach($marks as $mark)

<tr>
    <td>{{ $mark->student->name }}</td>

    <td>{{ $mark->assignment->title }}</td>

    <td>{{ $mark->assignment->subject->name }}</td>

    <td>{{ $mark->marks }}</td>

    <td>
        <span class="badge bg-primary">
            {{ $mark->grade }}
        </span>
    </td>
</tr>

@endforeach

</tbody>

</table>

</div>

</body>
</html>