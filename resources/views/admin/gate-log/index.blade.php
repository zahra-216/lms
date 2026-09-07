<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gate Log</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
    }
    .container { max-width:1000px; margin:auto; }
    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff; border-radius:18px;
        padding:26px 30px; margin:18px 0 26px; box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }
    .card-box{ background:#fff; padding:20px; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:26px; }
    table.gl-table thead th{ background:#012147; color:#fff; font-weight:600; border:none; padding:12px 10px; white-space:nowrap; }
    table.gl-table thead th:first-child{ border-top-left-radius:10px; }
    table.gl-table thead th:last-child{ border-top-right-radius:10px; }
    table.gl-table tbody td{ vertical-align:middle; padding:10px; }
    table.gl-table tbody tr:nth-child(even){ background:#f8fafc; }
    .badge-in{ background:#d1fae5; color:#065f46; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .badge-out{ background:#fef3c7; color:#92400e; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .type-pill{ background:#eef2f9; color:#012147; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .date-filter{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.dashboard') }}" class="back-btn" style="border:none;background:#fff;color:#012147;font-weight:600;padding:8px 16px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.06);text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-bottom:10px;">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    
    <div class="page-header">
        <div>
            <h2><i class="bi bi-door-open"></i> Gate Log</h2>
            <small>Campus check-in / check-out records</small>
        </div>
        <a href="{{ route('admin.gate-devices.index') }}" class="back-btn" style="border:none;background:#fff;color:#012147;font-weight:600;padding:8px 16px;border-radius:10px;text-decoration:none;">
            <i class="bi bi-hdd-network"></i> Manage Devices
        </a>
    </div>

    <div class="card-box">
        <form method="GET" class="date-filter">
            <label class="form-label mb-0 fw-semibold">Date:</label>
            <input type="date" name="date" value="{{ $date }}" class="form-control" style="max-width:200px;" onchange="this.form.submit()">
            <input type="text" id="nameSearch" class="form-control" placeholder="Search by name or reg no..." style="max-width:260px;">
        </form>
    </div>

    <div class="card-box">
        <div class="table-responsive">
        <table class="table gl-table align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Reg No / Username</th>
                    <th>Type</th>
                    <th>Action</th>
                    <th>Time</th>
                    <th>Device</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr data-name="{{ strtolower($log->person_name . ' ' . $log->person_ref) }}">
                        <td>{{ $log->person_name }}</td>
                        <td>{{ $log->person_ref }}</td>
                        <td><span class="type-pill">{{ ucfirst($log->user_type) }}</span></td>
                        <td>
                            @if($log->scan_type === 'in')
                                <span class="badge-in">Checked In</span>
                            @else
                                <span class="badge-out">Checked Out</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($log->scanned_at)->format('h:i A') }}</td>
                        <td>{{ $log->device->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No gate log entries for this date.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

<script>
document.getElementById('nameSearch').addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();
    document.querySelectorAll('table.gl-table tbody tr[data-name]').forEach(row => {
        row.style.display = row.dataset.name.includes(query) ? '' : 'none';
    });
});
</script>
</body>
</html>