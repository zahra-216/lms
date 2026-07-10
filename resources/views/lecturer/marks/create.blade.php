<!DOCTYPE html>
<html>
<head>
<title>Enter Marks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f4f6fb;">

<div class="container mt-5">
<div class="card p-4">

    <a href="{{ route('lecturer.subject.grades', $assignment->subject_id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back to Grades</a>

    <h4>📚 {{ $assignment->title }} - Marks Entry</h4>
    <p class="text-muted">{{ $assignment->subject->name }}</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($submissions->count())
    <form method="POST" action="{{ route('lecturer.marks.store') }}">
        @csrf
        <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Reg No</th>
                    <th>File</th>
                    <th>Marks (0-100)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $sub)
                <tr>
                    <td>{{ $sub->student->name ?? 'Unknown' }}</td>
                    <td>{{ $sub->student->registration_no ?? '—' }}</td>
                    <td>
                        @if($sub->file)
                            <a href="{{ asset('storage/'.$sub->file) }}" target="_blank">View</a>
                        @else
                            <span class="text-muted">No file</span>
                        @endif
                    </td>
                    <td>
                        <input type="number"
                               name="marks[{{ $sub->student_id }}]"
                               class="form-control"
                               min="0"
                               max="100"
                               value="{{ $existingMarks[$sub->student_id] ?? '' }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button class="btn btn-primary w-100">Save Marks</button>
    </form>
    @else
        <p class="text-muted mt-3">No students have submitted this assignment yet — marks can't be entered until there's a submission.</p>
    @endif

</div>
</div>

</body>
</html>