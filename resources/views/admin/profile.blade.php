<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>

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
        max-width:500px;
        background:#fff;
        padding:30px;
        border-radius:18px;
        box-shadow:0 20px 50px rgba(0,0,0,0.25);
    }

    h3{
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

    .profile-img{
        width:110px;
        height:110px;
        border-radius:50%;
        object-fit:cover;
        border:3px solid #012147;
    }
    </style>
</head>
<body>

<div class="card-box">

    <h3>Admin Profile</h3>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- PROFILE IMAGE --}}
    <div class="text-center mb-3">

        @if($admin->photo)
            <img src="{{ asset('uploads/admin/'.$admin->photo) }}" class="profile-img">
        @else
            <img src="https://ui-avatars.com/api/?name={{ $admin->name }}" class="profile-img">
        @endif

    </div>

    {{-- FORM --}}
    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $admin->name }}" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ $admin->email }}" required>
        </div>

        <div class="mb-3">
            <label>Profile Image</label>
            <input type="file" name="photo" class="form-control">
        </div>

        <hr>

        <div class="mb-3">
            <label>New Password</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        <button class="btn btn-custom w-100">Update Profile</button>

    </form>

</div>

</body>
</html>