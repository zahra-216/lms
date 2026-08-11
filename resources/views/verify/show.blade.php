<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Certificate Verification | TT Metro Campus</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body {
        font-family:'Segoe UI', sans-serif; margin:0; min-height:100vh; color:#012147;
        background:linear-gradient(135deg,#012147,#1e3a6e);
        display:flex; align-items:center; justify-content:center; padding:20px;
    }
    .verify-card {
        background:#fff; border-radius:18px; padding:36px 32px; max-width:440px; width:100%;
        box-shadow:0 20px 50px rgba(0,0,0,0.25);
    }
    .verify-icon { text-align:center; font-size:40px; color:#012147; margin-bottom:10px; }
    h2 { text-align:center; font-weight:700; font-size:20px; margin-bottom:6px; }
    p.sub { text-align:center; color:#64748b; font-size:13.5px; margin-bottom:26px; }

    label { font-weight:600; font-size:13.5px; margin-bottom:6px; display:block; }
    .form-control { border-radius:10px; padding:11px 14px; border:1px solid #e2e8f0; margin-bottom:18px; }
    .form-control:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.08); }

    .verify-btn { background:#012147; color:#fff; border:none; padding:12px 26px; border-radius:10px; font-weight:600; width:100%; }
    .verify-btn:hover { background:#1e3a6e; }
</style>
</head>
<body>
<div class="verify-card">
    <div class="verify-icon"><i class="bi bi-patch-check-fill"></i></div>
    <h2>Certificate Verification</h2>
    <p class="sub">Enter your registration number and certificate number to verify.</p>

    @if(session('error'))
        <div class="alert alert-danger rounded-3">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('verify.check') }}">
        @csrf
        <label>Registration Number</label>
        <input type="text" name="registration_no" class="form-control" value="{{ old('registration_no') }}" required>

        <label>Certificate Number</label>
        <input type="text" name="certificate_number" class="form-control" value="{{ old('certificate_number') }}" required>

        <button type="submit" class="verify-btn"><i class="bi bi-search"></i> Verify</button>
    </form>
</div>
</body>
</html>