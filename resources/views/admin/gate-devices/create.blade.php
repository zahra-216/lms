<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Gate Device</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    .container { max-width:500px; margin:auto; }
    .card-box{ background:#fff; padding:26px; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.06); }
    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff; border-radius:18px;
        padding:22px 26px; margin:18px 0 26px;
    }
    .page-header h2{ margin:0; font-weight:700; font-size:20px; }
    .btn-save{ background:#012147; color:#fff; border:none; padding:10px 20px; border-radius:10px; font-weight:600; }
    .btn-save:hover{ background:#0a2452; color:#fff; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.gate-devices.index') }}" class="back-btn" style="border:none;background:#fff;color:#012147;font-weight:600;padding:8px 16px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.06);text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-bottom:10px;">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header"><h2><i class="bi bi-hdd-network"></i> Add Gate Device</h2></div>

    <div class="card-box">
        <form action="{{ route('admin.gate-devices.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Device Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Main Gate Tablet" required>
            </div>
            <button type="submit" class="btn btn-save">Create Device & Generate Key</button>
            <a href="{{ route('admin.gate-devices.index') }}" style="display:inline-block; padding:10px 20px; border-radius:10px; font-weight:600; color:#012147; border:1px solid #e2e8f0; text-decoration:none; margin-left:8px;">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>