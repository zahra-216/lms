<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body {
        margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
        background: linear-gradient(135deg, #012147, #0b2d5a);
        font-family:'Segoe UI',sans-serif; padding:20px; box-sizing:border-box;
    }
    .card-box {
        width:100%; max-width:440px; background:#fff; border-radius:18px;
        padding:36px; box-shadow:0 20px 50px rgba(0,0,0,0.25);
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn { from{opacity:0; transform:translateY(15px);} to{opacity:1; transform:translateY(0);} }

    .icon-box { text-align:center; font-size:40px; color:#012147; margin-bottom:8px; }
    .card-box h2 { text-align:center; color:#012147; font-weight:700; margin-bottom:6px; font-size:22px; }
    .card-box .subtitle { text-align:center; color:#6b7280; font-size:13px; margin-bottom:24px; }

    .form-label { font-weight:600; color:#012147; font-size:14px; margin-bottom:6px; }
    .password-wrap { position:relative; margin-bottom:18px; }
    .form-control { border-radius:12px; padding:11px 42px 11px 14px; border:1px solid #e2e8f0; width:100%; }
    .form-control:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); outline:none; }
    .toggle-eye { position:absolute; top:38px; right:14px; cursor:pointer; color:#64748b; }

    .btn-navy {
        width:100%; background:#012147; color:#fff; border:none; padding:12px;
        border-radius:12px; font-weight:600; transition:0.3s;
    }
    .btn-navy:hover { background:#0b2d5a; transform:scale(1.01); }

    .alert { border-radius:12px; font-size:14px; }
    .back-link { display:block; text-align:center; margin-top:18px; font-size:13px; color:#012147; text-decoration:none; }
    .back-link:hover { text-decoration:underline; }

    @media (max-width:480px){ .card-box{ padding:26px; } }
</style>
</head>
<body>
<div class="card-box">
    <div class="icon-box"><i class="bi bi-shield-lock-fill"></i></div>
    <h2>Change Password</h2>
    <div class="subtitle">Update your admin account password</div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf

        <label class="form-label">New Password</label>
        <div class="password-wrap">
            <input type="password" name="password" id="newPassword" class="form-control" required>
            <i class="bi bi-eye toggle-eye" onclick="togglePassword('newPassword', this)"></i>
        </div>

        <label class="form-label">Confirm Password</label>
        <div class="password-wrap">
            <input type="password" name="password_confirmation" id="confirmPassword" class="form-control" required>
            <i class="bi bi-eye toggle-eye" onclick="togglePassword('confirmPassword', this)"></i>
        </div>

        <button type="submit" class="btn-navy">Update Password</button>
    </form>

    <a href="{{ route('admin.dashboard') }}" class="back-link"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
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