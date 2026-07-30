<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lecture Records</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
        .top-actions{ flex-direction:column; align-items:stretch !important; }
    }
    .container { max-width:1100px; margin:auto; }
    .action-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .action-btn:hover{ background:#012147; color:#fff; }
    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:26px 30px; margin:18px 0 26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }
    .top-actions{ display:flex; gap:10px; flex-wrap:wrap; }
    .card-box{
        background:#fff; padding:20px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:26px;
    }
    table.lr-table{ border-collapse:separate; border-spacing:0; }
    table.lr-table thead th{
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap;
    }
    table.lr-table thead th:first-child{ border-top-left-radius:10px; }
    table.lr-table thead th:last-child{ border-top-right-radius:10px; }
    table.lr-table tbody td{ vertical-align:middle; padding:10px; }
    table.lr-table tbody tr:nth-child(even){ background:#f8fafc; }
    table.lr-table tbody tr:hover{ background:#eef2f9; }
    .badge-pending{ background:#fef3c7; color:#92400e; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .badge-complete{ background:#d1fae5; color:#065f46; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
</style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-journal-text"></i> Lecture Records</h2>
            <small>All lecture records across every module</small>
        </div>
        <div class="top-actions">
            <a href="{{ route('admin.lecture-records.create') }}" class="action-btn">
                <i class="bi bi-plus-circle"></i> Add Record
            </a>
            <a href="{{ route('admin.lecture-records.pdf-all') }}" class="action-btn">
                <i class="bi bi-download"></i> Download All (Grouped PDF)
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    @endif

    <div class="card-box">
        <div class="table-responsive">
        <table class="table lr-table align-middle">
            <thead>
                <tr>
                    <th>Module</th>
                    <th>Date</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Lecturer</th>
                    <th>Content Covered</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    <tr>
                        <td>{{ $record->subject ? $record->subject->code . ' - ' . $record->subject->name : '—' }}</td>
                        <td>{{ $record->date ? \Carbon\Carbon::parse($record->date)->format('d M Y') : '—' }}</td>
                        <td>{{ $record->start_time ? \Carbon\Carbon::parse($record->start_time)->format('h:i A') : '—' }}</td>
                        <td>{{ $record->end_time ? \Carbon\Carbon::parse($record->end_time)->format('h:i A') : '—' }}</td>
                        <td>{{ $record->lecturer->name ?? '—' }}</td>
                        <td>{{ $record->content_covered ?? '—' }}</td>
                        <td>
                            @if($record->content_covered && $record->date)
                                <span class="badge-complete">Complete</span>
                            @else
                                <span class="badge-pending">Pending</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.lecture-records.edit', $record->id) }}" class="action-btn">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No lecture records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
</body>
</html>