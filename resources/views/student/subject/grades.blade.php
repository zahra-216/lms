<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Grades</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="{{ route('student.subject.portal.show', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2>{{ $subject->code }} - {{ $subject->name }} — Grades</h2>

    @php
        $total = 0; $count = 0;
    @endphp

    <table class="table table-bordered bg-white mt-3">
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
                @php
                    if ($mark) { $total += $mark->marks; $count++; }
                @endphp
            @empty
                <tr><td colspan="3" class="text-muted text-center">No assignments for this subject yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    @php
        $avg = $count ? $total / $count : 0;
        $finalGrade = match(true) {
            $avg >= 80 => 'A',
            $avg >= 60 => 'B',
            $avg >= 40 => 'C',
            default => 'F',
        };
    @endphp

    <div class="alert alert-info">
        <strong>Assignment Average:</strong> {{ $count ? round($avg, 2) : 'N/A' }}
        &nbsp;|&nbsp;
        <strong>Overall Grade:</strong> {{ $count ? $finalGrade : 'N/A' }}
    </div>

    <p class="text-muted small">
        Note: this reflects assignment marks only — exam marks aren't tracked in the system yet.
    </p>
</div>
</body>
</html>