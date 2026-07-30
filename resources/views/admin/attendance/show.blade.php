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
    .container { max-width:900px; margin:auto; }

    .back-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover { background:#012147; color:#fff; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:26px 30px; margin-bottom:22px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h2 { margin:0; font-weight:700; font-size:22px; }

    .card-box { background:#fff; border-radius:16px; padding:22px; box-shadow:0 8px 26px rgba(0,0,0,0.06); margin-bottom:26px; }
    .section-title { font-weight:700; color:#012147; margin-bottom:16px; font-size:16px; display:flex; align-items:center; gap:8px; }

    .form-control { border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px; }
    .form-control:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }

    .btn-navy { background:#012147; color:#fff; border:none; padding:10px 20px; border-radius:10px; font-weight:600; }
    .btn-navy:hover { background:#0b2d5a; color:#fff; }

    table.att-table { border-collapse:separate; border-spacing:0; width:100%; }
    table.att-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 14px; white-space:nowrap; }
    table.att-table thead th:first-child { border-top-left-radius:10px; }
    table.att-table thead th:last-child { border-top-right-radius:10px; }
    table.att-table tbody td { vertical-align:middle; padding:12px 14px; }
    table.att-table tbody tr:nth-child(even) { background:#f8fafc; }
    table.att-table tbody tr:hover { background:#eef2f9; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        table.att-table { display:block; overflow-x:auto; white-space:nowrap; }
    }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.attendance.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h2><i class="bi bi-calendar-check"></i> {{ $subject->code }} - {{ $subject->name }}</h2>
    </div>

    <div class="card-box">
        <div class="section-title"><i class="bi bi-calendar3"></i> By Date</div>

        <form method="GET" action="{{ route('admin.attendance.show', $subject->id) }}" class="mb-3 d-flex gap-2 align-items-end flex-wrap">
            <div>
                <label class="form-label fw-semibold" style="font-size:14px;">Select Date</label>
                <input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}" class="form-control" onchange="this.form.submit()">
            </div>
        </form>

        <a href="{{ route('admin.attendance.pdf', ['id' => $subject->id, 'date' => $date]) }}" class="btn-navy d-inline-flex align-items-center gap-2 mb-3" style="text-decoration:none;">
            <i class="bi bi-download"></i> Download This Date (PDF)
        </a>

        <div class="table-responsive">
        <table class="table att-table align-middle">
            <thead>
                <tr><th>Reg No</th><th>Student Name</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    @php $status = $attendance->get($student->id)?->status; @endphp
                    <tr>
                        <td>{{ $student->registration_no }}</td>
                        <td>{{ $student->name }}</td>
                        <td>
                            @if($status === 'present') <span class="badge bg-success">Present</span>
                            @elseif($status === 'absent') <span class="badge bg-danger">Absent</span>
                            @else <span class="badge bg-secondary">Not Marked</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <div class="card-box">
        <div class="section-title"><i class="bi bi-bar-chart-line"></i> Attendance Summary ({{ $totalClasses }} classes conducted)</div>

        <a href="{{ route('admin.attendance.summary.pdf', $subject->id) }}" class="btn-navy d-inline-flex align-items-center gap-2 mb-3" style="text-decoration:none;">
            <i class="bi bi-download"></i> Download Summary (PDF)
        </a>

        <div class="table-responsive">
        <table class="table att-table align-middle">
            <thead>
                <tr><th>Reg No</th><th>Student Name</th><th>Present</th><th>Total Classes</th><th>Percentage</th></tr>
            </thead>
            <tbody>
                @foreach($summary as $row)
                    <tr>
                        <td>{{ $row->student->registration_no }}</td>
                        <td>{{ $row->student->name }}</td>
                        <td>{{ $row->present_count }}</td>
                        <td>{{ $row->total_classes }}</td>
                        <td>{{ $row->percentage }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
</body>
</html>