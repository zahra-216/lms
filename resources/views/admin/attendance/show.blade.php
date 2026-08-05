<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject->name }} - Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:700px; margin:auto; }

    .back-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover { background:#012147; color:#fff; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:26px 30px; margin-bottom:26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h2 { margin:0; font-weight:700; font-size:22px; }

    .action-card {
        background:#fff; border-radius:16px; padding:30px; text-align:center;
        box-shadow:0 8px 26px rgba(0,0,0,0.06); text-decoration:none; color:#012147;
        display:flex; flex-direction:column; align-items:center; gap:12px; transition:.15s;
        height:100%;
    }
    .action-card:hover { background:#012147; color:#fff; transform:translateY(-2px); }
    .action-card i { font-size:38px; }

    @media (max-width:576px){ body { padding:20px 12px; } }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.attendance.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h2><i class="bi bi-calendar-check"></i> {{ $subject->code }} - {{ $subject->name }}</h2>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <a href="{{ route('admin.attendance.mark', $subject->id) }}" class="action-card">
                <i class="bi bi-clipboard2-check"></i>
                <div class="fw-bold fs-5">Mark Attendance</div>
                <div class="text-muted small" style="color:inherit; opacity:.75;">Mark today's or a past date's attendance</div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('admin.attendance.history', $subject->id) }}" class="action-card">
                <i class="bi bi-clock-history"></i>
                <div class="fw-bold fs-5">View History</div>
                <div class="text-muted small" style="color:inherit; opacity:.75;">Browse and download monthly reports</div>
            </a>
        </div>
    </div>
</div>
</body>
</html>