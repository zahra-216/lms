<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TT Mentor Campus - Admin Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
body {
    background: #012147;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', sans-serif;
}

/* Card */
.login-card {
    background: #fff;
    border-radius: 15px;
    padding: 40px 30px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 420px;
}

/* Header */
.login-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 25px;
}

.login-header img {
    width: 45px;
    height: auto;
}

.login-header h2 {
    margin: 0;
    font-weight: 700;
    color: #333;
    font-size: 22px;
}

/* Form */
.position-relative { position: relative; }

.form-control {
    border-radius: 50px;
    padding: 10px 20px 10px 40px;
}

.form-control-icon {
    position: absolute;
    top: 50%;
    left: 15px;
    transform: translateY(-50%);
    color: #aaa;
}

/* Button */
.btn-login {
    border-radius: 50px;
    padding: 8px 30px;   /* small size */
    font-weight: bold;
    background: #012147;
    border: none;
    color: white;
    transition: 0.3s;
    display: block;
    margin: 15px auto 0 auto; /* center */
    width: auto; 
}

.btn-login:hover {
    background: #17a673;
}

/* Alert */
.alert {
    border-radius: 10px;
}

/* Forgot link */
.form-text {
    text-align: center;
    margin-top: 15px;
}

.form-text a {
    color: #4e73df;
    text-decoration: none;
}

.form-text a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="login-card">

    <!-- Logo + Title -->
    <div class="login-header">
        <img src="{{ asset('images/logo.png.jpeg') }}" alt="TT Mentor Campus Logo">
        <h1>Admin Login</h1>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <div class="mb-3 position-relative">
            <i class="fa fa-user form-control-icon"></i>
            <input type="email" name="email" class="form-control" placeholder="Username" required>
        </div>
        

        <div class="mb-3 position-relative">
            <i class="fa fa-lock form-control-icon"></i>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <button type="submit" class="btn btn-login">
            <i class="fa fa-sign-in-alt me-1"></i> Login
        </button>
    </form>

    <div class="form-text">
    <small>
        Forgot your password?
        <a href="{{ url('/admin/forgot-password') }}">Reset Here</a>
    </small>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>