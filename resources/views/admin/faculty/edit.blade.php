<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Faculty | TT Metro Campus</title>
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

        /* ✅ IMAGE PREVIEW */
        .preview-img {
            width:100px;
            height:100px;
            border-radius:50%;
            object-fit:cover;
            border:3px solid #012147;
            margin-bottom:10px;
            display:block;
        }

        .error {
            color:red;
            margin-bottom:10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Faculty</h2>

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- CURRENT IMAGE SHOW -->
    @php
        $img = $faculty->image
            ? asset('storage/faculty/'.$faculty->image)
            : 'https://via.placeholder.com/100';
    @endphp

    <img src="{{ $img }}" class="preview-img">

    <!-- IMPORTANT: enctype added -->
    <form action="{{ route('admin.faculties.update', $faculty->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="text" name="name" value="{{ $faculty->name }}" required>

        <!-- NEW IMAGE INPUT -->
        <input type="file" name="image" accept="image/*">

        <button type="submit">
            <i class="fa fa-edit"></i> Update Faculty
        </button>
    </form>
</div>

</body>
</html>