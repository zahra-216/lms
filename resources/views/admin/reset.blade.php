<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>

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
    .reset-card{
        width:100%;
        max-width:420px;
        background:#fff;
        padding:30px;
        border-radius:18px;
        box-shadow:0 20px 50px rgba(0,0,0,0.25);
        animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn{
        from{opacity:0; transform:translateY(20px);}
        to{opacity:1; transform:translateY(0);}
    }

    /* TITLE */
    .reset-card h3{
        text-align:center;
        font-weight:700;
        color:#012147;
        margin-bottom:20px;
    }

    /* INPUT */
    .form-control{
        border-radius:12px;
        padding:12px;
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

<div class="reset-card">

    <!-- ICON -->
    <div class="icon-box">
        <i class="bi bi-shield-lock-fill"></i>
    </div>

    <h3>Reset Password</h3>

    {{-- SUCCESS --}}
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    {{-- ERRORS --}}
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ url('/admin/reset-password') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="New Password" required>
        </div>

        <div class="mb-3">
            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
        </div>

        <button class="btn btn-custom w-100">
            Reset Password
        </button>
    </form>

</div>

</body>
</html>