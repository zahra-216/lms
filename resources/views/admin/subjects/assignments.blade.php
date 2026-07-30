<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject->name }} - Assignments | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){
        body{ padding:20px 12px; }
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
        .page-header .add-btn{ width:100%; text-align:center; }
        .assignment-header{ flex-direction:column; align-items:flex-start !important; gap:8px; padding:15px 16px; }
        .assignment-body{ padding:16px; }
    }
    .container { max-width:1000px; margin:auto; }
    .back-btn{ border:none; background:#fff; color:#012147; font-weight:600; padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06); text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .back-btn:hover{ background:#012147; color:#fff; }
    .page-header{ background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff; border-radius:18px; padding:26px 30px; margin:18px 0 26px; box-shadow:0 10px 30px rgba(1,33,71,0.25); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }
    .add-btn{ background:#fff; color:#012147; font-weight:600; border:none; padding:10px 18px; border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .add-btn:hover{ background:#e2e8f0; color:#012147; }
    .assignment-card{ border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:25px; border:none; overflow:hidden; }
    .assignment-header{ background:#012147; color:#fff; padding:18px 22px; display:flex; justify-content:space-between; align-items:center; }
    .assignment-body{ padding:20px 22px; background:#fff; }
    .meta-badge{ font-size:0.8rem; margin-right:6px; padding:6px 12px; border-radius:20px; }
    .late-badge{ background:#fee2e2; color:#b91c1c; }
    .ontime-badge{ background:#dcfce7; color:#15803d; }
    table.sub-table{ border-collapse:separate; border-spacing:0; }
    table.sub-table thead th{ background:#f1f5f9; color:#012147; font-weight:600; border:none; padding:10px; }
    table.sub-table tbody td{ vertical-align:middle; padding:10px; }
    table.sub-table tbody tr:nth-child(even){ background:#f8fafc; }
    table.sub-table tbody tr:hover{ background:#eef2f9; }
    .btn-edit{ background:#ffc107; color:#012147; border:none; }
    .btn-delete{ background:#dc3545; color:#fff; border:none; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.subjects.show', $subject->id) }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h2><i class="bi bi-journal-text"></i> {{ $subject->code }} - {{ $subject->name }} — Assignments</h2>
        <a href="{{ route('admin.subjects.assignments.create', $subject->id) }}" class="add-btn">
            <i class="bi bi-plus-circle"></i> Add Assignment
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    @if($subject->assignments && $subject->assignments->count())
        @foreach($subject->assignments as $assignment)
        <div class="card assignment-card">
            <div class="assignment-header">
                <div>
                    <h5 class="mb-1">{{ $assignment->title }}</h5>
                    <small><i class="bi bi-clock"></i> Due: {{ $assignment->due_date?->format('d M Y, h:i A') ?? 'No due date set' }}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-{{ $assignment->is_published ? 'success' : 'secondary' }}">
                        {{ $assignment->is_published ? 'Published' : 'Draft' }}
                    </span>
                    <a href="{{ route('admin.subjects.assignments.edit', [$subject->id, $assignment->id]) }}" class="btn btn-sm btn-edit"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('admin.subjects.assignments.destroy', [$subject->id, $assignment->id]) }}" method="POST" onsubmit="return confirm('Delete this assignment?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-delete"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
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
                    <span class="badge bg-primary meta-badge"><i class="bi bi-star"></i> {{ $assignment->total_points ?? 'N/A' }} pts</span>
                    <span class="badge bg-info text-dark meta-badge">{{ ucfirst($assignment->submission_type ?? 'file') }}</span>
                    <span class="badge bg-{{ $assignment->allow_late ? 'warning text-dark' : 'secondary' }} meta-badge">
                        {{ $assignment->allow_late ? 'Late submissions allowed' : 'No late submissions' }}
                    </span>
                </div>

                <h6 class="mt-4 mb-2"><i class="bi bi-people"></i> Student Submissions ({{ $assignment->submissions->count() }})</h6>

                @if($assignment->submissions->count())
                <div class="table-responsive">
                    <table class="table sub-table table-sm align-middle">
                        <thead>
                            <tr><th>Reg No</th><th>Student Name</th><th>Submitted On</th><th>Status</th><th>File</th></tr>
                        </thead>
                        <tbody>
                            @foreach($assignment->submissions as $submission)
                                @php
                                    $isLate = null;
                                    if ($assignment->due_date && $submission->submitted_at) {
                                        $isLate = $submission->submitted_at->gt($assignment->due_date);
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $submission->student->registration_no ?? 'N/A' }}</td>
                                    <td>{{ $submission->student->name ?? 'Unknown student' }}</td>
                                    <td>{{ $submission->submitted_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                    <td>
                                        @if($isLate === null) <span class="text-muted">—</span>
                                        @elseif($isLate) <span class="badge late-badge">Late</span>
                                        @else <span class="badge ontime-badge">On time</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($submission->file)
                                            <a href="{{ asset('storage/' . $submission->file) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> View</a>
                                        @else <span class="text-muted">No file</span>
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