<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Lecturer | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:560px; margin:auto; }

    .back-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover { background:#012147; color:#fff; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:24px 28px; margin-bottom:26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h3 { margin:0; font-weight:700; font-size:20px; }

    .card-box { background:#fff; border-radius:16px; padding:28px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }

    .form-label { font-weight:600; color:#012147; font-size:14px; margin-bottom:6px; }
    .form-control { border-radius:10px; border:1px solid #e2e8f0; padding:11px 14px; }
    .form-control:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }

    .btn-navy { width:100%; background:#012147; color:#fff; border:none; padding:12px; border-radius:12px; font-weight:600; }
    .btn-navy:hover { background:#0b2d5a; }

    @media (max-width:480px){ .card-box{ padding:20px; } }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.lecturers.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <h3><i class="bi bi-person-plus"></i> Create Lecturer</h3>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-box">
        <form action="{{ route('admin.lecturers.store') }}" method="POST" autocomplete="off">
            @csrf

            <input type="text" name="fake_username" style="display:none">
            <input type="password" name="fake_password" style="display:none">

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autocomplete="off" readonly onfocus="this.removeAttribute('readonly')">
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" required autocomplete="off">
            </div>

            <div class="mb-3">
                <label class="form-label">Email (optional)</label>
                <input type="email" name="email" class="form-control" autocomplete="off">
            </div>

            <div class="mb-4" style="position:relative;">
                <label class="form-label">Password</label>
                <input type="password" name="password" id="lecturerPassword" class="form-control" required autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')" style="padding-right:42px;">
                <i class="bi bi-eye" onclick="togglePassword('lecturerPassword', this)"
                   style="position:absolute; top:38px; right:14px; cursor:pointer; color:#64748b;"></i>
            </div>

            <button type="submit" class="btn-navy">Save Lecturer</button>
        </form>
    </div>
</div>

<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
</body>
</html>