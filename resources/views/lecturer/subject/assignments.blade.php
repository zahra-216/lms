<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject->name }} - Assignments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root{
        --navy:#0a2452;
        --navy-light:#153a7a;
        --blue:#2563eb;
        --bg:#f5f7fb;
        --border:#e6eaf1;
        --muted:#64748b;
    }
    *{ font-family:'Inter', sans-serif; }
    body{ background:var(--bg); padding:36px 16px 60px; }
    .container{ max-width:980px; margin:auto; }

    .back-btn{
        border:none; background:#fff; color:var(--navy); font-weight:600; font-size:14px;
        padding:9px 16px; border-radius:10px; box-shadow:0 1px 3px rgba(15,23,42,0.08);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
        border:1px solid var(--border); transition:.15s;
    }
    .back-btn:hover{ background:var(--navy); color:#fff; border-color:var(--navy); }

    .page-header{
        background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);
        color:#fff; border-radius:20px; padding:28px 32px; margin:20px 0 28px;
        box-shadow:0 12px 28px -8px rgba(10,36,82,0.35);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2{ margin:0; font-weight:700; font-size:21px; letter-spacing:-0.3px; }
    .page-header .subtitle{ opacity:.75; font-size:13px; font-weight:500; margin-top:2px; }

    .add-btn{
        background:#fff; color:var(--navy); font-weight:600; font-size:14px; border:none;
        padding:11px 20px; border-radius:11px; text-decoration:none;
        display:inline-flex; align-items:center; gap:7px; transition:.15s;
        box-shadow:0 4px 12px rgba(0,0,0,0.12);
    }
    .add-btn:hover{ transform:translateY(-1px); box-shadow:0 6px 16px rgba(0,0,0,0.16); color:var(--navy); }

    .assignment-card{
        border-radius:16px; box-shadow:0 1px 2px rgba(15,23,42,0.04), 0 8px 20px -6px rgba(15,23,42,0.08);
        margin-bottom:22px; border:1px solid var(--border); overflow:hidden; background:#fff;
        transition:box-shadow .15s;
    }
    .assignment-card:hover{ box-shadow:0 1px 2px rgba(15,23,42,0.04), 0 12px 28px -6px rgba(15,23,42,0.12); }

    .assignment-header{
        padding:20px 24px; border-bottom:1px solid var(--border);
        display:flex; justify-content:space-between; align-items:flex-start; gap:14px; flex-wrap:wrap;
    }
    .assignment-header h5{ font-weight:700; font-size:16.5px; color:#0f172a; margin:0 0 4px; }
    .due-line{ color:var(--muted); font-size:13px; display:flex; align-items:center; gap:5px; }

    .status-pill{ padding:5px 12px; border-radius:20px; font-weight:600; font-size:12px; white-space:nowrap; }
    .pill-published{ background:#dcfce7; color:#15803d; }
    .pill-draft{ background:#f1f5f9; color:#475569; }

    .actions-row{ display:flex; align-items:center; gap:8px; }
    .icon-btn{
        width:34px; height:34px; border-radius:9px; border:1px solid var(--border);
        background:#fff; display:inline-flex; align-items:center; justify-content:center;
        color:var(--navy); text-decoration:none; transition:.15s; font-size:14.5px;
    }
    .icon-btn:hover{ background:var(--navy); color:#fff; border-color:var(--navy); }
    .icon-btn.danger:hover{ background:#dc2626; border-color:#dc2626; }

    .assignment-body{ padding:22px 24px; }
    .desc-text{ color:#334155; font-size:14.5px; line-height:1.6; margin-bottom:16px; }

    .attach-link{
        display:inline-flex; align-items:center; gap:6px; font-size:13.5px; font-weight:600;
        color:var(--navy); background:#eef2f9; padding:8px 14px; border-radius:9px;
        text-decoration:none; margin-bottom:16px; transition:.15s;
    }
    .attach-link:hover{ background:var(--navy); color:#fff; }

    .meta-row{ display:flex; flex-wrap:wrap; gap:8px; margin-bottom:22px; }
    .meta-badge{
        font-size:12.5px; font-weight:600; padding:6px 13px; border-radius:20px;
        display:inline-flex; align-items:center; gap:5px;
    }
    .b-points{ background:#eef2ff; color:#4338ca; }
    .b-type{ background:#ecfeff; color:#0e7490; }
    .b-late-on{ background:#fff7ed; color:#c2410c; }
    .b-late-off{ background:#f1f5f9; color:#64748b; }
    .b-penalty{ background:#fef2f2; color:#b91c1c; }

    .submissions-block{ background:#f8fafc; border-radius:12px; padding:18px 20px; border:1px solid var(--border); }
    .submissions-title{ font-weight:700; font-size:14.5px; color:#0f172a; margin-bottom:12px; display:flex; align-items:center; gap:7px; }

    table.sub-table{ border-collapse:separate; border-spacing:0; width:100%; }
    table.sub-table thead th{
        background:transparent; color:var(--muted); font-weight:600; font-size:11.5px;
        text-transform:uppercase; letter-spacing:.4px; border:none; border-bottom:1px solid var(--border);
        padding:0 10px 10px;
    }
    table.sub-table tbody td{ vertical-align:middle; padding:11px 10px; font-size:13.5px; border-bottom:1px solid var(--border); }
    table.sub-table tbody tr:last-child td{ border-bottom:none; }
    table.sub-table tbody tr:hover{ background:#fff; }

    .late-badge{ background:#fee2e2; color:#b91c1c; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .ontime-badge{ background:#dcfce7; color:#15803d; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }

    .empty-state{ text-align:center; color:#94a3b8; padding:60px 20px; background:#fff; border-radius:16px; border:1px dashed var(--border); }
    .empty-state i{ font-size:38px; opacity:.5; margin-bottom:10px; display:block; }

    @media (max-width:576px){
        body{ padding:20px 12px 40px; }
        .page-header{ padding:22px; }
        .assignment-header, .assignment-body{ padding:18px; }
    }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <div>
            <h2><i class="bi bi-journal-text me-2"></i>{{ $subject->code }} — {{ $subject->name }}</h2>
            <div class="subtitle">Assignments</div>
        </div>
        <a href="{{ route('lecturer.assignments.create', $subject->id) }}" class="add-btn">
            <i class="bi bi-plus-circle"></i> Add Assignment
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    @endif

    @if($subject->assignments && $subject->assignments->count())

        @foreach($subject->assignments as $assignment)
        <div class="assignment-card">
            <div class="assignment-header">
                <div>
                    <h5>{{ $assignment->title }}</h5>
                    <div class="due-line"><i class="bi bi-clock"></i> Due {{ $assignment->due_date?->format('d M Y, h:i A') ?? 'No due date set' }}</div>
                </div>

                <div class="actions-row">
                    <span class="status-pill {{ $assignment->is_published ? 'pill-published' : 'pill-draft' }}">
                        {{ $assignment->is_published ? 'Published' : 'Draft' }}
                    </span>
                    <a href="{{ route('lecturer.assignments.edit', [$subject->id, $assignment->id]) }}" class="icon-btn" title="Edit">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('lecturer.assignments.destroy', [$subject->id, $assignment->id]) }}"
                          method="POST" onsubmit="return confirm('Delete this assignment? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="icon-btn danger" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
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
                    <span class="meta-badge b-type"><i class="bi bi-file-earmark"></i> {{ ucfirst($assignment->submission_type ?? 'file') }}</span>
                    <span class="meta-badge {{ $assignment->allow_late ? 'b-late-on' : 'b-late-off' }}">
                        <i class="bi bi-hourglass-split"></i> {{ $assignment->allow_late ? 'Late allowed' : 'No late submissions' }}
                    </span>
                    @if($assignment->allow_late && $assignment->late_penalty)
                        <span class="meta-badge b-penalty"><i class="bi bi-percent"></i> {{ $assignment->late_penalty }}% penalty</span>
                    @endif
                </div>

                <div class="submissions-block">
                    <div class="submissions-title">
                        <i class="bi bi-people-fill"></i> Student Submissions ({{ $assignment->submissions->count() }})
                    </div>

                    @if($assignment->submissions->count())
                        <div class="table-responsive">
                            <table class="table sub-table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Reg No</th>
                                        <th>Student</th>
                                        <th>Submitted</th>
                                        <th>Status</th>
                                        <th>Comment</th>
                                        <th>File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignment->submissions as $submission)
                                        @php
                                            $isLate = null; $durationText = '—';
                                            if ($assignment->due_date && $submission->submitted_at) {
                                                $isLate = $submission->submitted_at->gt($assignment->due_date);
                                                $diff = $submission->submitted_at->diff($assignment->due_date);
                                                $parts = [];
                                                if ($diff->d > 0) $parts[] = $diff->d . 'd';
                                                if ($diff->h > 0) $parts[] = $diff->h . 'h';
                                                if ($diff->i > 0) $parts[] = $diff->i . 'm';
                                                $durationText = $parts ? implode(' ', $parts) : 'less than a minute';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $submission->student->registration_no ?? 'N/A' }}</td>
                                            <td>{{ $submission->student->name ?? 'Unknown student' }}</td>
                                            <td>{{ $submission->submitted_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                            <td>
                                                @if($isLate === null)
                                                    <span class="text-muted">—</span>
                                                @elseif($isLate)
                                                    <span class="late-badge">Late by {{ $durationText }}</span>
                                                @else
                                                    <span class="ontime-badge">Early by {{ $durationText }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $submission->comment ?? '—' }}</td>
                                            <td>
                                                @if($submission->file)
                                                    <a href="{{ asset('storage/' . $submission->file) }}" target="_blank" class="icon-btn" title="View">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted small">No file</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0 small">No students have submitted this assignment yet.</p>
                    @endif
                </div>
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
</body>
</html>