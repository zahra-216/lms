<!DOCTYPE html>
<html>
<head>
    <title>Create Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background: #012147;
            min-height: 100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            font-family: 'Segoe UI', sans-serif;
        }

        .card-box{
            background: #fff;
            width: 100%;
            max-width: 420px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .title{
            text-align:center;
            margin-bottom:20px;
            color:#012147;
            font-weight:700;
        }

        .form-control{
            border-radius: 8px;
            padding:10px;
        }

        .btn-custom{
            background:#012147;
            color:#fff;
            width:100%;
            border-radius:8px;
            padding:10px;
            font-weight:600;
        }

        .btn-custom:hover{
            background:#17a673;
        }
    </style>
</head>

<body>

<div class="card-box">

    <h3 class="title">Create Admin</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.store') }}">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-custom">
            Create Admin
        </button>

    </form>

</div>

</body>
</html>