<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject->name }} - Grades</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="{{ route('student.subject.portal.show', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2>{{ $subject->code }} - {{ $subject->name }} — Grades</h2>

    <h5 class="mt-4">Assignments</h5>
    <div class="table-responsive">
    <table class="table table-bordered bg-white mt-2">
        <thead>
            <tr><th>Assignment</th><th>Marks</th><th>Grade</th></tr>
        </thead>
        <tbody>
            @forelse($subject->assignments as $assignment)
                @php $mark = $assignment->marks->first(); @endphp
                <tr>
                    <td>{{ $assignment->title }}</td>
                    <td>{{ $mark->marks ?? 'Not graded yet' }}</td>
                    <td>{{ $mark->grade ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-muted text-center">No assignments for this subject yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <h5 class="mt-4">Overall Subject Marks</h5>
    <div class="table-responsive">
    <table class="table table-bordered bg-white mt-2">
        <thead>
            <tr>
                <th>Assignment Marks</th>
                <th>Mid Marks</th>
                <th>Final Exam</th>
                <th>Final Mark</th>
                <th>Final Grade</th>
            </tr>
        </thead>
        <tbody>
            @if($subjectMark)
            <tr>
                <td>{{ $subjectMark->assignment_marks }}</td>
                <td>{{ $subjectMark->mid_marks }}</td>
                <td>{{ $subjectMark->final_exam_marks }}</td>
                <td>{{ $subjectMark->final_marks }}</td>
                <td><strong>{{ $subjectMark->final_grade }}</strong></td>
            </tr>
            @else
            <tr><td colspan="5" class="text-muted text-center">No marks recorded yet for this subject.</td></tr>
            @endif
        </tbody>
    </table>
    </div>

    <div class="alert alert-info">
        <strong>Overall Grade:</strong> {{ $subjectMark->final_grade ?? 'N/A' }}
    </div>
</div>
</body>
</html>