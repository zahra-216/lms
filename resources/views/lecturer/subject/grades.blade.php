<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Grades</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background:#f4f6f9; font-family:'Segoe UI', sans-serif; padding:40px; }
    .container { max-width:1000px; margin:auto; }
    h2 { color:#012147; }
    .assignment-row { display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border:1px solid #e5e7eb; border-radius:10px; margin-bottom:10px; background:#fff; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2 class="mb-4">{{ $subject->code }} - {{ $subject->name }} — Grades</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h5 class="mb-3">Assignments</h5>

    @if($subject->assignments->count())
        @foreach($subject->assignments as $assignment)
            <div class="assignment-row">
                <div>
                    <strong>{{ $assignment->title }}</strong><br>
                    <small class="text-muted">
                        {{ $assignment->submissions->count() }} submission(s) ·
                        {{ $assignment->marks->count() }} graded
                    </small>
                </div>
                <a href="{{ route('lecturer.marks.create', $assignment->id) }}" class="btn btn-sm btn-primary">
                    Enter / Edit Marks
                </a>
            </div>
        @endforeach
    @else
        <p class="text-muted">No assignments found for this subject.</p>
    @endif

    <h5 class="mt-5 mb-3">All Marks</h5>

    @php
        $allMarks = $subject->assignments->flatMap->marks;
    @endphp

    @if($allMarks->count())
        <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Reg No</th>
                    <th>Assignment</th>
                    <th>Marks</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allMarks as $mark)
                <tr>
                    <td>{{ $mark->student->name ?? '—' }}</td>
                    <td>{{ $mark->student->registration_no ?? '—' }}</td>
                    <td>{{ $mark->assignment->title ?? '—' }}</td>
                    <td>{{ $mark->marks }}</td>
                    <td><span class="badge bg-primary">{{ $mark->grade }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">No marks entered yet for this subject.</p>
    @endif
</div>
</body>
</html>