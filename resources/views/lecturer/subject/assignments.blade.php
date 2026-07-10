<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Assignments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6f9; font-family:'Segoe UI', sans-serif; padding:40px; }
    .container { max-width:1000px; margin:auto; }
    h2 { color:#012147; }
    .assignment-card { border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.08); margin-bottom:25px; }
    .assignment-header { background:#012147; color:#fff; border-radius:12px 12px 0 0; padding:18px 22px; }
    .assignment-body { padding:20px 22px; }
    .meta-badge { font-size:0.8rem; margin-right:6px; }
    .late-badge { background:#dc3545; }
    .ontime-badge { background:#198754; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2 class="mb-4">{{ $subject->code }} - {{ $subject->name }} — Assignments</h2>

    @if($subject->assignments && $subject->assignments->count())

        @foreach($subject->assignments as $assignment)
        <div class="card assignment-card">
            <div class="assignment-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{ $assignment->title }}</h5>
                    <small>Due: {{ $assignment->due_date?->format('d M Y, h:i A') ?? 'No due date set' }}</small>
                </div>
                <span class="badge bg-{{ $assignment->is_published ? 'success' : 'secondary' }}">
                    {{ $assignment->is_published ? 'Published' : 'Draft' }}
                </span>
            </div>

            <div class="assignment-body">
                <p class="mb-2">{{ $assignment->description ?? 'No description provided.' }}</p>

                <div class="mb-3">
                    <span class="badge bg-primary meta-badge">
                        <i class="bi bi-star"></i> {{ $assignment->total_points ?? 'N/A' }} pts
                    </span>
                    <span class="badge bg-info text-dark meta-badge">
                        <i class="bi bi-file-earmark"></i> {{ ucfirst($assignment->submission_type ?? 'file') }}
                    </span>
                    <span class="badge bg-{{ $assignment->allow_late ? 'warning text-dark' : 'secondary' }} meta-badge">
                        {{ $assignment->allow_late ? 'Late submissions allowed' : 'No late submissions' }}
                    </span>
                    @if($assignment->allow_late && $assignment->late_penalty)
                        <span class="badge bg-danger meta-badge">
                            Late penalty: {{ $assignment->late_penalty }}%
                        </span>
                    @endif
                </div>

                <h6 class="mt-4 mb-2">Student Submissions ({{ $assignment->submissions->count() }})</h6>

                @if($assignment->submissions->count())
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Reg No</th>
                                    <th>Student Name</th>
                                    <th>Submitted On</th>
                                    <th>Status</th>
                                    <th>Comment</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignment->submissions as $submission)
                                    @php
                                        $isLate = $assignment->due_date && $submission->submitted_at
                                            && $submission->submitted_at->gt($assignment->due_date);
                                    @endphp
                                    <tr>
                                        <td>{{ $submission->student->registration_no ?? 'N/A' }}</td>
                                        <td>{{ $submission->student->name ?? 'Unknown student' }}</td>
                                        <td>{{ $submission->submitted_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                        <td>
                                            @if($isLate)
                                                <span class="badge late-badge">Late</span>
                                            @else
                                                <span class="badge ontime-badge">On time</span>
                                            @endif
                                        </td>
                                        <td>{{ $submission->comment ?? '—' }}</td>
                                        <td>
                                            @if($submission->file)
                                                <a href="{{ asset('storage/' . $submission->file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download"></i> View
                                                </a>
                                            @else
                                                <span class="text-muted">No file</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No students have submitted this assignment yet.</p>
                @endif
            </div>
        </div>
        @endforeach

    @else
        <p class="text-muted mt-3">No assignments posted yet for this subject.</p>
    @endif
</div>
</body>
</html>