<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject->name }} - Attendance History</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    .container { max-width:700px; margin:auto; }
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
    }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }
    .month-card{
        background:#fff; border-radius:14px; padding:18px 20px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); text-decoration:none; color:#012147;
        display:flex; justify-content:space-between; align-items:center;
        transition:0.15s;
    }
    .month-card:hover{ background:#eef2f9; color:#012147; }
    .month-card .icons a{ color:#012147; margin-left:14px; font-size:18px; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.attendance.show', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <h2><i class="bi bi-clock-history"></i> {{ $subject->code }} - Attendance History</h2>
    </div>

    <div class="d-flex flex-column gap-3">
        @forelse($months as $month)
            <div class="month-card">
                <span><i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</span>
                <span class="icons">
                    <a href="{{ route('admin.attendance.monthly.pdf', ['id' => $subject->id, 'month' => $month]) }}" target="_blank" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.attendance.monthly.pdf', ['id' => $subject->id, 'month' => $month]) }}?download=1" title="Download">
                        <i class="bi bi-download"></i>
                    </a>
                </span>
            </div>
        @empty
            <div class="month-card justify-content-center text-muted">No attendance records yet.</div>
        @endforelse
    </div>
</div>
</body>
</html>