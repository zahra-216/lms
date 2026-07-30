<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background:#012147; font-family:'Segoe UI',sans-serif; padding:20px; }
    .card-box { width:100%; max-width:460px; background:#fff; border-radius:20px; padding:40px; box-shadow:0 20px 50px rgba(0,0,0,0.25); }
    .card-box h2 { text-align:center; color:#012147; font-weight:700; margin-bottom:24px; }
    .form-label { font-weight:600; color:#012147; font-size:14px; }
    .password-wrap { position:relative; margin-bottom:16px; }
    .form-control { border-radius:12px; padding:11px 40px 11px 14px; border:1px solid #e2e8f0; width:100%; }
    .form-control:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }
    .toggle-eye { position:absolute; top:50%; right:14px; transform:translateY(-50%); cursor:pointer; color:#64748b; }
    .btn-navy { width:100%; background:#012147; color:#fff; border:none; padding:12px; border-radius:12px; font-weight:600; }
    .btn-navy:hover { background:#0353a4; }
    @media (max-width:480px){ .card-box{ padding:26px; } }
</style>
</head>
<body>
<div class="card-box">
    <h2>Change Password</h2>

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