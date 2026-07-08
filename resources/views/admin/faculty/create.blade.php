<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Faculty | TT Metro Campus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:#f4f6f9;
            color:#012147;
            padding:40px;
        }
        .container {
            max-width:500px;
            margin:auto;
            background:#fff;
            padding:30px;
            border-radius:12px;
            box-shadow:0 4px 10px rgba(0,0,0,.1);
        }
        h2 {text-align:center; margin-bottom:20px;}

        input {
            width:100%;
            padding:12px;
            margin-bottom:15px;
            border-radius:8px;
            border:1px solid #ccc;
        }

        button {
            background:#012147;
            color:#fff;
            padding:10px 20px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            display:flex;
            align-items:center;
            gap:5px;
        }

        button:hover {background:#021634;}

        .error {
            background:#ffe6e6;
            color:red;
            padding:10px;
            border-radius:8px;
            margin-bottom:10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add Faculty</h2>

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ✅ IMPORTANT: enctype added -->
    <form action="{{ route('admin.faculties.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <input type="text" name="name" placeholder="Enter Faculty Name" required>

        <!-- ✅ IMAGE FIELD ADDED -->
        <input type="file" name="image" accept="image/*">

        <button type="submit">
            <i class="fa fa-plus"></i> Add Faculty
        </button>
    </form>
</div>

</body>
</html>