@extends('layouts.student')
@section('title')
{{ $subject->name }} - Assignments
@endsection
@section('back', route('student.subject.portal.show', $subject->id))
@push('styles')
<style>
:root {
        --navy:#0a2452;
        --navy-light:#153a7a;
        --bg:#f5f7fb;
        --border:#e6eaf1;
        --muted:#64748b;
}
.page-header .subtitle { opacity:.75; font-size:13px; font-weight:500; margin-top:2px;
}
.page-header i.icon-deco { font-size:38px; opacity:.7;
}
.assignment-card {
        border-radius:16px; box-shadow:0 1px 2px rgba(15,23,42,0.04), 0 8px 20px -6px rgba(15,23,42,0.08);
        margin-bottom:22px; border:1px solid var(--border); overflow:hidden; background:#fff;
}
.assignment-header {
        padding:20px 24px; border-bottom:1px solid var(--border); background:#fafbfd;
        display:flex; justify-content:space-between; align-items:flex-start; gap:14px; flex-wrap:wrap;
}
.assignment-header h5 { font-weight:700; font-size:16.5px; color:#0f172a; margin:0 0 4px;
}
.due-line { color:var(--muted); font-size:13px;
}
.status-pill { padding:5px 13px; border-radius:20px; font-weight:600; font-size:12px; white-space:nowrap;
}
.pill-overdue { background:#fee2e2; color:#b91c1c;
}
.pill-active { background:#dcfce7; color:#15803d;
}
.assignment-body { padding:22px 24px;
}
.desc-text { color:#334155; font-size:14.5px; line-height:1.6; margin-bottom:16px;
}
.attach-link {
        display:inline-flex; align-items:center; gap:6px; font-size:13.5px; font-weight:600;
        color:var(--navy); background:#eef2f9; padding:8px 14px; border-radius:9px;
        text-decoration:none; margin-bottom:16px; transition:.15s;
}
.attach-link:hover { background:var(--navy); color:#fff;
}
.meta-row { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:18px;
}
.meta-badge { font-size:12.5px; font-weight:600; padding:6px 13px; border-radius:20px; display:inline-flex; align-items:center; gap:5px;
}
.b-points { background:#eef2ff; color:#4338ca;
}
.b-late-on { background:#dcfce7; color:#15803d;
}
.b-late-off { background:#fef2f2; color:#b91c1c;
}
.countdown-box {
        background:#fff7ed; border:1px solid #fed7aa; color:#c2410c;
        border-radius:10px; padding:12px 16px; font-size:13.5px; font-weight:600;
        margin-bottom:18px; display:flex; align-items:center; gap:8px;
}
.submit-form { background:#f8fafc; border:1px solid var(--border); border-radius:14px; padding:20px;
}
.submit-form .form-control { border-radius:9px; border:1px solid var(--border); padding:10px 12px; font-size:13.5px;
}
.submission-box {
        border-radius:14px; padding:18px 20px; font-size:14px; border:1px solid;
}
.submission-ontime { background:#f0fdf4; border-color:#bbf7d0; color:#166534;
}
.submission-late { background:#fef2f2; border-color:#fecaca; color:#991b1b;
}
.submission-box a { color:inherit; font-weight:700; text-decoration:underline;
}
.sub-actions { display:flex; gap:8px; margin-top:12px;
}
.btn-ghost {
        background:#fff; border:1px solid rgba(0,0,0,0.12); color:inherit;
        font-size:12.5px; font-weight:600; padding:7px 14px; border-radius:8px;
        display:inline-flex; align-items:center; gap:6px; transition:.15s;
}
.btn-ghost:hover { background:rgba(0,0,0,0.05);
}
.btn-ghost.danger:hover { background:#dc2626; color:#fff; border-color:#dc2626;
}
.edit-form { background:#fff; border:1px solid rgba(0,0,0,0.1); border-radius:10px; padding:14px; margin-top:12px;
}
.locked-note { color:var(--muted); font-size:13px; display:flex; align-items:center; gap:6px; margin-top:12px;
}
.deadline-warning {
        color:#b91c1c; font-size:13.5px; font-weight:600; display:flex; align-items:center; gap:6px;
        background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:12px 16px;
}
@media (max-width:576px) {
.assignment-header, .assignment-body { padding:18px;
}
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <div>
            <h2><i class="bi bi-journal-text me-2"></i>{{ $subject->code }} - {{ $subject->name }}</h2>
            <div class="subtitle">Assignments</div>
        </div>
        <i class="bi bi-clipboard-check icon-deco"></i>
    </div>

    
    

    @if($subject->assignments && $subject->assignments->count())

        @foreach($subject->assignments as $assignment)
        @php
            $submission = $assignment->submissions->first();
            $isOverdue = !$submission && now()->gt($assignment->due_date);
        @endphp
        <div class="assignment-card">
            <div class="assignment-header">
                <div>
                    <h5>{{ $assignment->title }}</h5>
                    <div class="due-line"><i class="bi bi-clock"></i> Due {{ $assignment->due_date?->format('d M Y, h:i A') ?? 'No due date set' }}</div>
                </div>
                <span class="status-pill {{ $isOverdue ? 'pill-overdue' : 'pill-active' }}">
                    {{ $isOverdue ? 'Overdue' : 'Active' }}
                </span>
            </div>

            <div class="assignment-body">
                <p class="desc-text">{{ $assignment->description ?? 'No description provided.' }}</p>

                @if($assignment->file_path)
                    <a href="{{ asset('storage/' . $assignment->file_path) }}" target="_blank" class="attach-link">
                        <i class="bi bi-paperclip"></i> View Attachment
                    </a>
                @endif

                <div class="meta-row">
                    <span class="meta-badge b-points"><i class="bi bi-star-fill"></i> {{ $assignment->total_points ?? 'N/A' }} pts</span>
                    <span class="meta-badge {{ $assignment->allow_late ? 'b-late-on' : 'b-late-off' }}">
                        <i class="bi bi-hourglass-split"></i> {{ $assignment->allow_late ? 'Late submissions allowed' : 'No late submissions' }}
                    </span>
                </div>

                @if(!$submission)
                    <div class="countdown-box">
                        <i class="bi bi-stopwatch"></i>
                        <span>Time remaining: <span class="countdown" data-deadline="{{ $assignment->due_date }}"></span></span>
                    </div>
                @endif

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
                    <div class="submission-box {{ $isLate ? 'submission-late' : 'submission-ontime' }}">
                        <div><i class="bi bi-check-circle-fill"></i> Submitted on {{ $submission->submitted_at->format('d M Y, h:i A') }}</div>
                        <div class="mt-1"><strong>{{ $isLate ? 'Late by ' . $durationText : 'Early by ' . $durationText }}</strong></div>
                        <div class="mt-1"><a href="{{ asset('storage/' . $submission->file) }}" target="_blank">View my submission</a></div>

                        @if(now()->lte($assignment->due_date))
                            <div class="sub-actions">
                                <button type="button" class="btn-ghost"
                                        onclick="document.getElementById('edit-sub-{{ $submission->id }}').classList.toggle('d-none')">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <form action="{{ route('assignment.submission.destroy', $submission->id) }}" method="POST"
                                      onsubmit="return confirm('Delete your submission? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>

                            <form id="edit-sub-{{ $submission->id }}" action="{{ route('assignment.submission.update', $submission->id) }}"
                                  method="POST" enctype="multipart/form-data" class="edit-form d-none">
                                @csrf
                                @method('PUT')
                                <input type="file" name="file" class="form-control form-control-sm mb-2" required>
                                <button class="btn-navy btn btn-sm">Replace File</button>
                            </form>
                        @else
                            <div class="locked-note"><i class="bi bi-lock-fill"></i> Deadline passed - submission is locked.</div>
                        @endif
                    </div>

                @elseif(now()->lte($assignment->due_date) || $assignment->allow_late)
                    <form action="{{ route('assignment.submit') }}" method="POST" enctype="multipart/form-data" class="submit-form">
                        @csrf
                        <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <div class="mb-3">
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <button class="btn-navy"><i class="bi bi-upload me-1"></i> Submit Assignment</button>
                    </form>
                @else
                    <div class="deadline-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Deadline passed - late submissions are not allowed for this assignment.
                    </div>
                @endif
            </div>
        </div>
        @endforeach

    @else
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            No assignments posted yet for this subject.
        </div>
    @endif
</div>
@endsection
@push('scripts')
<script>
function startCountdown() {
    document.querySelectorAll('.countdown').forEach(el => {
        const deadline = new Date(el.getAttribute('data-deadline')).getTime();
        const timer = setInterval(() => {
            const now = new Date().getTime();
            const diff = deadline - now;
            if (diff <= 0) {
                el.innerHTML = "Expired";
                clearInterval(timer);
                return;
            }
            const d = Math.floor(diff / (1000*60*60*24));
            const h = Math.floor((diff % (1000*60*60*24)) / (1000*60*60));
            const m = Math.floor((diff % (1000*60*60)) / (1000*60));
            const s = Math.floor((diff % (1000*60)) / 1000);
            el.innerHTML = `${d}d ${h}h ${m}m ${s}s`;
        }, 1000);
    });
}
startCountdown();
</script>
@endpush
