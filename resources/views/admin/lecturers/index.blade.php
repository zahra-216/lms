<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturers | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; margin:0; padding:40px; }
        .container { max-width:1000px; margin:auto; background:#fff; padding:30px; border-radius:16px; box-shadow:0 12px 32px rgba(0,0,0,0.08); }
        h1 { text-align:center; margin-bottom:20px; }
        .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        .btn-add { background:#012147; color:white; padding:10px 18px; border:none; border-radius:10px; cursor:pointer; text-decoration:none; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:14px 12px; border-bottom:1px solid #e5e7eb; text-align:left; }
        th { background:#f8fafc; }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <h1>Lecturers</h1>
            <a href="{{ route('admin.lecturers.create') }}" class="btn-add"><i class="fa fa-plus"></i> Add Lecturer</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lecturers as $lecturer)
                    <tr>
                        <td>{{ $lecturer->id }}</td>
                        <td>{{ $lecturer->username }}</td>
                        <td>{{ $lecturer->name }}</td>
                        <td>{{ $lecturer->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
