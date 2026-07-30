<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    margin:0;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(135deg,#012147,#0353a4);
    font-family: 'Segoe UI', sans-serif;
    padding:20px;
    box-sizing:border-box;
}

/* LOGIN CARD */
.login-card{
    width:100%;
    max-width:420px;
    background:#fff;
    border-radius:18px;
    padding:35px;
    box-shadow:0 15px 40px rgba(0,0,0,0.25);
    text-align:center;
    position:relative;
}

/* TOP STRIP */
.login-card::before{
    content:"";
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:6px;
    background:#012147;
    border-top-left-radius:18px;
    border-top-right-radius:18px;
}

.form-control::placeholder{
    color:#adb5bd;
}

/* LOGO STYLE */
.logo{
    width:90px;
    height:90px;
    object-fit:contain;
    margin:10px auto 15px auto;
    display:block;
    padding:8px;
    background:#fff;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

/* TITLE */
h3{
    font-weight:700;
    color:#012147;
    margin-bottom:5px;
}

.subtitle{
    font-size:13px;
    color:#6b7280;
    margin-bottom:20px;
}

/* INPUT */
.form-control{
    border-radius:12px;
    padding:12px;
    border:1px solid #e5e7eb;
}

.form-control:focus{
    border-color:#012147;
    box-shadow:0 0 0 0.15rem rgba(1,33,71,0.2);
}

/* BUTTON */
.btn-login{
    width:100%;
    padding:12px;
    border-radius:12px;
    background:#012147;
    color:#fff;
    font-weight:600;
    border:none;
    transition:0.3s;
}

.btn-login:hover{
    background:#0353a4;
    transform:scale(1.02);
}

/* ERROR */
.alert{
    font-size:13px;
    padding:8px;
}

/* FOOT TEXT */
.small-text{
    font-size:12px;
    color:#9ca3af;
    margin-top:15px;
}

</style>
</head>

<body>

<div class="login-card">

    <!-- LOGO (FIXED) -->
    <img src="{{ asset('images/logo.png.jpeg') }}" class="logo" alt="Logo">

    <h3>Student Login</h3>
    <div class="subtitle">Welcome back to LMS Portal</div>

    <!-- ERROR -->
    @if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
    @endif

    <!-- FORM -->
    <form method="POST" action="{{ route('login.submit') }}">
    @csrf

        <div class="mb-3 text-start">
            <label class="form-label">Registration Number</label>
            <input type="text" name="registration_no" class="form-control" placeholder="e.g. TTMC/ML/UG/CSE/25/006">
        </div>

        <div class="mb-3 text-start">
            <label class="form-label">Password</label>
            <div style="position:relative;">
                <input type="password" name="password" id="studentPassword" class="form-control" placeholder="Enter Password" style="padding-right:40px;">
                <i class="bi bi-eye" onclick="togglePassword('studentPassword', this)"
                style="position:absolute; top:50%; right:14px; transform:translateY(-50%); cursor:pointer; color:#6b7280;"></i>
            </div>
        </div>

        <button class="btn btn-login">
            <i class="bi bi-box-arrow-in-right"></i> Login
        </button>

    </form>

    <div class="small-text">
        © 2026 TT Metro Campus LMS
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