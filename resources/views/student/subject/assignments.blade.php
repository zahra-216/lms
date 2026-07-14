<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Assignments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
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
    <a href="{{ route('student.subject.portal.show', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2 class="mb-4">{{ $subject->code }} - {{ $subject->name }} — Assignments</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($subject->assignments && $subject->assignments->count())

        @foreach($subject->assignments as $assignment)
        @php
            $submission = $assignment->submissions->first();
        @endphp
        <div class="card assignment-card">
            <div class="assignment-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{ $assignment->title }}</h5>
                    <small>Due: {{ $assignment->due_date?->format('d M Y, h:i A') ?? 'No due date set' }}</small>
                </div>
                <span class="badge bg-{{ now()->gt($assignment->due_date) ? 'danger' : 'success' }}">
                    {{ now()->gt($assignment->due_date) ? 'Overdue' : 'Active' }}
                </span>
            </div>

            <div class="assignment-body">
                <p class="mb-2">{{ $assignment->description ?? 'No description provided.' }}</p>

                @if($assignment->file_path)
                    <p class="mb-2">
                        <a href="{{ asset('storage/' . $assignment->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-paperclip"></i> View Attachment
                        </a>
                    </p>
                @endif

                <div class="mb-3">
                    <span class="badge bg-primary meta-badge">
                        <i class="bi bi-star"></i> {{ $assignment->total_points ?? 'N/A' }} pts
                    </span>
                    <span class="badge bg-{{ $assignment->allow_late ? 'warning text-dark' : 'secondary' }} meta-badge">
                        {{ $assignment->allow_late ? 'Late submissions allowed' : 'No late submissions' }}
                    </span>
                </div>

                <div class="mb-3">
                    <strong>Time remaining:</strong>
                    <span class="countdown" data-deadline="{{ $assignment->due_date }}"></span>
                </div>

                @if($submission)
                    @php
                        $isLate = $assignment->due_date && $submission->submitted_at->gt($assignment->due_date);
                        $diff = $submission->submitted_at->diff($assignment->due_date);
                        $parts = [];
                        if ($diff->d > 0) $parts[] = $diff->d . 'd';
                        if ($diff->h > 0) $parts[] = $diff->h . 'h';
                        if ($diff->i > 0) $parts[] = $diff->i . 'm';
                        $durationText = $parts ? implode(' ', $parts) : 'less than a minute';
                    @endphp
                    <div class="alert alert-{{ $isLate ? 'danger' : 'success' }}">
                        Submitted on {{ $submission->submitted_at->format('d M Y, h:i A') }} —
                        <strong>{{ $isLate ? 'Late by ' . $durationText : 'Early by ' . $durationText }}</strong>
                        <br>
                        <a href="{{ asset('storage/' . $submission->file) }}" target="_blank">View my submission</a>
                    </div>
                @elseif(now()->lte($assignment->due_date) || $assignment->allow_late)
                    <form action="{{ route('assignment.submit') }}" method="POST" enctype="multipart/form-data" class="border rounded p-3 bg-light">
                        @csrf
                        <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <div class="mb-2">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <button class="btn btn-primary btn-sm">Submit Assignment</button>
                    </form>
                @else
                    <p class="text-danger">Deadline passed — late submissions are not allowed for this assignment.</p>
                @endif
            </div>
        </div>
        @endforeach

    @else
        <p class="text-muted mt-3">No assignments posted yet for this subject.</p>
    @endif
</div>

<script>
function startCountdown() {
    document.querySelectorAll('.countdown').forEach(el => {
        const deadline = new Date(el.getAttribute('data-deadline')).getTime();
        const timer = setInterval(() => {
            const now = new Date().getTime();
            const diff = deadline - now;
            if (diff <= 0) {
                const overdueMs = Math.abs(diff);
                const d = Math.floor(overdueMs / (1000*60*60*24));
                const h = Math.floor((overdueMs % (1000*60*60*24)) / (1000*60*60));
                el.innerHTML = `<span class="text-danger">Overdue by ${d}d ${h}h</span>`;
                return;
            }
            const d = Math.floor(diff / (1000*60*60*24));
            const h = Math.floor((diff % (1000*60*60*24)) / (1000*60*60));
            const m = Math.floor((diff % (1000*60*60)) / (1000*60));
            el.innerHTML = `${d}d ${h}h ${m}m`;
        }, 1000);
    });
}
startCountdown();
</script>
</body>
</html>