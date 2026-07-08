<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students | Admin Panel</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f4f6f9;
            color: #012147;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form input, form select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        form button {
            padding: 10px 15px;
            background: #012147;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin: 0 auto; /* center */
        }

        form button:hover {
            background: #021634;
        }

        .success, .error {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success {background: #d4edda; color: #155724;}
        .error {background: #f8d7da; color: #721c24;}

        
        .btn-edit { background: #ffc107; color: #012147; }
        .btn-edit:hover { background: #e0a800; color: #fff; }

        .btn-delete { background: #dc3545; }
        .btn-delete:hover { background: #a71d2a; }

        @media (max-width: 768px) {
            .container { padding: 20px; }
            table, th, td { font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1> Add Student</h1>

        {{-- Success message --}}
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        
        <form action="{{ route('admin.students.store') }}" method="POST">
    @csrf

    <input type="text" name="registration_no" placeholder="Registration No" required>

    <input type="text" name="name" placeholder="Full Name" required>

    <input type="email" name="email" placeholder="Email (optional)">

    <select name="branch" required>
        <option value="">Select Branch</option>
        <option value="Head Office – Mount Lavinia">Head Office – Mount Lavinia</option>
        <option value="Sammanthurai Branch">Sammanthurai Branch</option>
        <option value="Batticaloa Branch">Batticaloa Branch</option>
        <option value="Trincomalee Branch">Trincomalee Branch</option>
        <option value="Nuwara Eliya Branch">Nuwara Eliya Branch</option>
    </select>

    <!-- 🔥 ADD COURSE -->
    <input type="number" name="course_id" placeholder="Course ID" required>

    <!-- 🔥 ADD LEVEL -->
    <input type="number" name="level_id" placeholder="Level ID" required>

    <!-- 🔥 PASSWORD -->
    <input type="password" name="password" placeholder="Password" required>

    <button type="submit">
        <i class="fa fa-plus"></i> Add Student
    </button>
</form>
    </div>
</body>
</html>
