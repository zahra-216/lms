<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Lecturer | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; margin:0; padding:40px; }
        .card { max-width: 540px; margin:auto; background:#fff; padding:30px; border-radius:16px; box-shadow:0 12px 32px rgba(0,0,0,0.08); }
        h1 { margin-bottom:18px; font-size:24px; text-align:center; }
        input { width:100%; padding:12px; border-radius:10px; border:1px solid #d1d5db; margin-bottom:16px; }
        button { width:100%; padding:12px; border:none; border-radius:10px; background:#012147; color:#fff; font-weight:700; cursor:pointer; }
        .alert { margin-bottom:20px; padding:12px 14px; border-radius:10px; }
        .alert-success { background:#e6ffed; color:#065f46; }
        .alert-error { background:#fee2e2; color:#991b1b; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Create Lecturer</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.lecturers.store') }}" method="POST">
            @csrf
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email (optional)">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Save Lecturer</button>
        </form>
    </div>
</body>
</html>
