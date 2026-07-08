<!DOCTYPE html>
<html>
<head>
    <title>Edit Student | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {font-family: 'Segoe UI'; background:#f4f6f9; color:#012147; padding:40px;}
        .container {max-width:500px; margin:auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,.1);}
        h2 {text-align:center; margin-bottom:20px;}
        input {width:100%; padding:12px; margin-bottom:15px; border-radius:8px; border:1px solid #ccc;}
        button {background:#ffc107; color:#012147; border:none; padding:12px 20px; border-radius:8px; cursor:pointer; display:flex; align-items:center; gap:8px;}
        button:hover {background:#e0a800; color:#fff;}
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Student</h2>

    @if($errors->any())
    <div class="error">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="registration_no" value="{{ $student->registration_no }}" required>
        <input type="text" name="name" value="{{ $student->name }}" required>
        <input type="email" name="email" value="{{ $student->email }}">
        <input type="text" name="branch" value="{{ $student->branch }}" required>
        <input type="text" name="password" value="{{ $student->password }}" required>
        <button type="submit"><i class="fa fa-edit"></i> Update Student</button>
    </form>
</div>
</body>
</html>
