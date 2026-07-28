<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject->name }} - Assignments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
        .assignment-header{ flex-direction:column; align-items:flex-start !important; gap:8px; padding:15px 16px; }
        .assignment-body{ padding:16px; }
    }

    .container { max-width:1000px; margin:auto; }

    .back-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .back-btn:hover{ background:#012147; color:#fff; }

    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:26px 30px; margin:18px 0 26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }
    .page-header small{ opacity:0.85; }

    .assignment-card {
        background:#fff; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.06);
        margin-bottom:22px; overflow:hidden; border:none;
    }
    .assignment-header {
        background:#eef2f9; color:#012147; padding:16px 22px;
        border-bottom:1px solid #e2e8f0;
    }
    .assignment-header h5{ font-weight:700; }
    .assignment-body { padding:20px 22px; background:#fff; }

    .meta-badge { font-size:0.8rem; margin-right:6px; padding:6px 12px; border-radius:20px; }
    .late-badge { background:#fee2e2; color:#b91c1c; }
    .ontime-badge { background:#dcfce7; color:#15803d; }

    .status-badge{ padding:6px 14px; border-radius:20px; font-weight:600; font-size:13px; }
    .status-overdue{ background:#fee2e2; color:#b91c1c; }
    .status-active{ background:#dcfce7; color:#15803d; }

    .submission-box{ border-radius:12px; }
    .submit-form{ background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; }

    .empty-state{ text-align:center; color:#94a3b8; padding:36px 0; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('student.subject.portal.show', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <div>
            <h2><i class="bi bi-journal-text"></i> {{ $subject->code }} — {{ $subject->name }}</h2>
            <small>Assignments</small>
        </div>
        <i class="bi bi-clipboard-check" style="font-size:44px; opacity:0.85;"></i>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3"><i class="bi bi-exclamation-circle"></i> {{ session('error') }}</div>
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
                    <small class="text-muted">Due: {{ $assignment->due_date?->format('d M Y, h:i A') ?? 'No due date set' }}</small>
                </div>
                <span class="status-badge {{ now()->gt($assignment->due_date) ? 'status-overdue' : 'status-active' }}">
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
                    <span class="badge meta-badge {{ $assignment->allow_late ? 'ontime-badge' : 'late-badge' }}">
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
                    <div class="alert submission-box {{ $isLate ? 'alert-danger' : 'alert-success' }}">
                        Submitted on {{ $submission->submitted_at->format('d M Y, h:i A') }} —
                        <strong>{{ $isLate ? 'Late by ' . $durationText : 'Early by ' . $durationText }}</strong>
                        <br>
                        <a href="{{ asset('storage/' . $submission->file) }}" target="_blank">View my submission</a>
                    </div>
                @elseif(now()->lte($assignment->due_date) || $assignment->allow_late)
                    <form action="{{ route('assignment.submit') }}" method="POST" enctype="multipart/form-data" class="submit-form">
                        @csrf
                        <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <div class="mb-2">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <button class="btn btn-navy btn-sm" style="background:#012147; color:#fff;">Submit Assignment</button>
                    </form>
                @else
                    <p class="text-danger mb-0"><i class="bi bi-exclamation-triangle"></i> Deadline passed — late submissions are not allowed for this assignment.</p>
                @endif
            </div>
        </div>
        @endforeach

    @else
        <div class="assignment-card">
            <div class="empty-state">No assignments posted yet for this subject.</div>
        </div>
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