<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gate Devices</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
    }
    .container { max-width:1000px; margin:auto; }
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
    .card-box{
        background:#fff; padding:20px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:26px;
    }
    table.gd-table thead th{
        background:#012147; color:#fff; font-weight:600; border:none; padding:12px 10px;
    }
    table.gd-table thead th:first-child{ border-top-left-radius:10px; }
    table.gd-table thead th:last-child{ border-top-right-radius:10px; }
    table.gd-table tbody td{ vertical-align:middle; padding:10px; }
    table.gd-table tbody tr:nth-child(even){ background:#f8fafc; }
    .badge-active{ background:#d1fae5; color:#065f46; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .badge-inactive{ background:#fee2e2; color:#991b1b; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .key-box{
        background:#012147; color:#fff; padding:14px 18px; border-radius:10px;
        font-family:monospace; font-size:14px; word-break:break-all; margin-top:10px;
    }
</style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-hdd-network"></i> Gate Devices</h2>
            <small>Tablets/scanners allowed to record Gate Log entries</small>
        </div>
        <a href="{{ route('admin.gate-devices.create') }}" class="action-btn">
            <i class="bi bi-plus-circle"></i> Add Device
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    @endif

    @if(session('newDeviceKey'))
        <div class="alert alert-warning rounded-3">
            <strong><i class="bi bi-key"></i> Save this key now for "{{ session('newDeviceName') }}"</strong>
            <div class="key-box">{{ session('newDeviceKey') }}</div>
            <small class="d-block mt-2">This will not be shown again. Copy it into the scanner device's setup.</small>
        </div>
    @endif

    <div class="card-box">
        <div class="table-responsive">
        <table class="table gd-table align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Last Used</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($devices as $device)
                    <tr>
                        <td>{{ $device->name }}</td>
                        <td>
                            @if($device->is_active)
                                <span class="badge-active">Active</span>
                            @else
                                <span class="badge-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $device->last_used_at ? $device->last_used_at->diffForHumans() : 'Never' }}</td>
                        <td>{{ $device->created_at->format('d M Y') }}</td>
                        <td>
                            <form action="{{ route('admin.gate-devices.toggle', $device->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="action-btn">
                                    {{ $device->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.gate-devices.destroy', $device->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Remove this device? It will stop working immediately.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn text-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No devices yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
</body>
</html>