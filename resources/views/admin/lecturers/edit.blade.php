<!DOCTYPE html>
<html>
<head>
    <title>Edit Lecturer | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {font-family: 'Segoe UI'; background:#f4f6f9; color:#012147; padding:40px;}
        .container {max-width:500px; margin:auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,.1);}
        h2 {text-align:center; margin-bottom:20px;}
        input {width:100%; padding:12px; margin-bottom:15px; border-radius:8px; border:1px solid #ccc;}
        .password-wrap { position:relative; }
        .password-wrap input { padding-right:42px; }
        .toggle-eye { position:absolute; top:14px; right:14px; cursor:pointer; color:#6b7280; }
        .hint { font-size:12px; color:#6b7280; margin:-10px 0 15px; }
        button {background:#ffc107; color:#012147; border:none; padding:12px 20px; border-radius:8px; cursor:pointer; display:flex; align-items:center; gap:8px;}
        button:hover {background:#e0a800; color:#fff;}
        .error { color:#dc2626; margin-bottom:15px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Lecturer</h2>

    @if($errors->any())
    <div class="error">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.lecturers.update', $lecturer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <input type="text" name="username" value="{{ $lecturer->username }}" placeholder="Username" required>
        <input type="text" name="name" value="{{ $lecturer->name }}" placeholder="Full Name" required>
        <input type="email" name="email" value="{{ $lecturer->email }}" placeholder="Email">

        <div class="password-wrap">
            <input type="password" name="password" id="lecturerNewPassword" placeholder="New Password (leave blank to keep unchanged)">
            <i class="fa fa-eye toggle-eye" onclick="togglePassword('lecturerNewPassword', this)"></i>
        </div>
        <div class="password-wrap">
            <input type="password" name="password_confirmation" id="lecturerConfirmPassword" placeholder="Confirm New Password">
            <i class="fa fa-eye toggle-eye" onclick="togglePassword('lecturerConfirmPassword', this)"></i>
        </div>
        <div class="hint">Only fill this in if you want to reset the lecturer's password.</div>

        <button type="submit"><i class="fa fa-edit"></i> Update Lecturer</button>
    </form>
</div>

<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
</body>
</html>