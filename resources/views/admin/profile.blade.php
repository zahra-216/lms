<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background:#012147; font-family:'Segoe UI',sans-serif; padding:20px; }
    .card-box { width:100%; max-width:480px; background:#fff; border-radius:20px; padding:40px; box-shadow:0 20px 50px rgba(0,0,0,0.25); }
    .card-box h2 { text-align:center; color:#012147; font-weight:700; margin-bottom:20px; }
    .logo { display:block; margin:0 auto 20px; width:120px; }
    .form-label { font-weight:600; color:#012147; font-size:14px; }
    .form-control { border-radius:12px; padding:11px 14px; border:1px solid #e2e8f0; margin-bottom:16px; }
    .form-control:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }
    .btn-navy { width:100%; background:#012147; color:#fff; border:none; padding:12px; border-radius:12px; font-weight:600; }
    .btn-navy:hover { background:#0353a4; }
    @media (max-width:480px){ .card-box{ padding:26px; } }
</style>
</head>
<body>
<div class="card-box">
    <h2>Admin Profile</h2>
    <img src="{{ asset('images/logo.png.jpeg') }}" class="logo" alt="Logo">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
        @csrf

        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ $admin->name }}" required>

        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ $admin->email }}" required>

        <label class="form-label">Profile Image</label>
        <input type="file" name="photo" class="form-control">

        <button type="submit" class="btn-navy">Update Profile</button>
    </form>
</div>
</body>
</html>