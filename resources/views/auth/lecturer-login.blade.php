<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg,#012147,#0353a4); font-family:'Segoe UI',sans-serif; }
        .card { width:100%; max-width:420px; background:#fff; border-radius:18px; padding:30px; box-shadow:0 20px 50px rgba(0,0,0,0.15); }
        .card h3 { margin-bottom:10px; color:#012147; }
        .form-control { border-radius:12px; padding:12px; }
        .btn-login { width:100%; padding:12px; border-radius:12px; background:#012147; color:#fff; border:none; font-weight:600; }
        .btn-login:hover { background:#0353a4; }
        .text-center a { color:#012147; }
    </style>
</head>
<body>
    <div class="card">
        <h3>Lecturer Login</h3>
        <p class="text-muted">Login with your lecturer username and password.</p>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('lecturer.login.submit') }}">
            @csrf
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-login">Login</button>
        </form>
    </div>
</body>
</html>
