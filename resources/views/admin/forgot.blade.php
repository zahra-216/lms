<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>

/* BACKGROUND */
body{
    height:100vh;
    margin:0;
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(135deg, #012147, #0b2d5a);
    font-family: 'Segoe UI', sans-serif;
}

/* CARD */
.forgot-card{
    width:100%;
    max-width:380px;
    background:#fff;
    border-radius:18px;
    padding:30px;
    box-shadow:0 20px 50px rgba(0,0,0,0.25);
    animation: fadeIn 0.6s ease-in-out;
}

/* ANIMATION */
@keyframes fadeIn{
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1; transform:translateY(0);}
}

/* TITLE */
.forgot-card h3{
    text-align:center;
    color:#012147;
    font-weight:700;
    margin-bottom:20px;
}

/* INPUT */
.form-control{
    border-radius:12px;
    padding:12px;
    border:1px solid #ddd;
}

.form-control:focus{
    border-color:#012147;
    box-shadow:0 0 0 0.15rem rgba(1,33,71,0.2);
}

/* BUTTON */
.btn-custom{
    background:#012147;
    color:#fff;
    border:none;
    border-radius:12px;
    padding:12px;
    font-weight:600;
    transition:0.3s;
}

.btn-custom:hover{
    background:#0b2d5a;
    transform:scale(1.02);
}

/* ICON */
.icon-box{
    text-align:center;
    font-size:45px;
    color:#012147;
    margin-bottom:10px;
}

/* ALERT */
.alert{
    border-radius:12px;
    font-size:14px;
}

</style>

</head>
<body>

<div class="forgot-card">

    <!-- ICON -->
    <div class="icon-box">
        <i class="bi bi-lock-fill"></i>
    </div>

    <h3>Forgot Password</h3>

    <form method="POST" action="{{ route('admin.password.email') }}">
        @csrf

        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Enter your email address" required>
        </div>

        <button class="btn btn-custom w-100">
            Send Reset Link
        </button>
    </form>

    {{-- ERROR --}}
    @if($errors->any())
    <div class="alert alert-danger mt-3">
        {{ $errors->first() }}
    </div>
    @endif

    {{-- SUCCESS --}}
    @if(session('status'))
    <div class="alert alert-success mt-3">
        {{ session('status') }}
    </div>
    @endif

    {{-- RESET LINK --}}
    @if(session('link'))
    <div class="alert alert-info mt-3">
        <i class="bi bi-link-45deg"></i>
        <a href="{{ session('link') }}" style="color:#012147;font-weight:600;">
            Click here to reset password
        </a>
    </div>
    @endif

</div>

</body>
</html>