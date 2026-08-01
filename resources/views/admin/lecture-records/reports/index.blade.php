<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lecture Reports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
        .top-actions{ flex-direction:column; align-items:stretch !important; }
    }
    .container { max-width:900px; margin:auto; }
    .back-btn, .action-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
        cursor:pointer;
    }
    .back-btn:hover, .action-btn:hover{ background:#012147; color:#fff; }
    .action-btn.danger:hover{ background:#ef4444; color:#fff; }
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
    .row-actions{ display:flex; gap:8px; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.lecture-records.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <div>
            <h2><i class="bi bi-file-earmark-pdf"></i> Lecture Reports</h2>
            <small>Generated monthly reports per lecturer</small>
        </div>
        <div class="top-actions">
            <a href="{{ route('admin.lecture-records.reports.create') }}" class="action-btn">
                <i class="bi bi-plus-circle"></i> Generate Report
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
                    <th>Lecturer</th>
                    <th>Month</th>
                    <th>Generated</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>{{ $report->lecturer->name ?? '—' }}</td>
                        <td>{{ $report->month->format('F Y') }}</td>
                        <td>{{ $report->generated_at?->format('d M Y, h:i A') ?? '—' }}</td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('admin.lecture-records.reports.download', $report->id) }}" class="action-btn">
                                    <i class="bi bi-download"></i> Download
                                </a>
                                <form action="{{ route('admin.lecture-records.reports.destroy', $report->id) }}" method="POST"
                                      onsubmit="return confirm('Delete this report? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">No reports generated yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
</body>
</html>