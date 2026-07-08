<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Settings</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(135deg, #012147, #0b2d5a);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:Segoe UI;
}

.card-box{
    width:100%;
    max-width:450px;
    background:#fff;
    padding:30px;
    border-radius:18px;
    box-shadow:0 20px 50px rgba(0,0,0,0.25);
}

h2{
    text-align:center;
    color:#012147;
    margin-bottom:20px;
    font-weight:700;
}

.form-control{
    border-radius:10px;
}

.btn-custom{
    background:#012147;
    color:#fff;
    border:none;
    border-radius:10px;
    padding:10px;
}

.btn-custom:hover{
    background:#0b2d5a;
}
</style>

</head>
<body>

<div class="card-box">

    <h2>Settings</h2>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ERROR MESSAGE --}}
    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf

        <div class="mb-3">
            <label>New Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button class="btn btn-custom w-100">Update Password</button>

    </form>

</div>

</body>
</html>