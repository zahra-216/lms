<!DOCTYPE html>
<html>
<head>
<title>Enter Marks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background:#f4f6fb;">

<div class="container mt-5">

<div class="card p-4">

<h4>📚 {{ $assignment->title }} - Marks Entry</h4>

<form method="POST" action="{{ route('admin.marks.store') }}">
@csrf

<input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

<table class="table table-bordered mt-3">

<thead>
<tr>
    <th>Student</th>
    <th>Subject</th>
    <th>File</th>
    <th>Marks</th>
</tr>
</thead>

<tbody>

@foreach($submissions as $sub)

<tr>
    <td>{{ $sub->student->name }}</td>

    <td>{{ $assignment->subject->name }}</td>

    <td>
        <a href="{{ asset('storage/'.$sub->file) }}" target="_blank">
            View
        </a>
    </td>

    <td>
        <input type="number"
               name="marks[{{ $sub->student_id }}]"
               class="form-control"
               min="0"
               max="100"
               required>
    </td>
</tr>

@endforeach

</tbody>

</table>

<button class="btn btn-primary w-100">
Save Marks
</button>

</form>

</div>

</div>

</body>
</html>