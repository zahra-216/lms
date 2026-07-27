<!DOCTYPE html>
<html>
<head>
    <title>Lecturer Payments | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI'; background: #f4f6f9; color:#012147; padding:40px; }
        .container { max-width:1000px; margin:auto; }
        h1 { text-align:center; margin-bottom:20px; }
        .search-box { max-width:400px; margin:0 auto 25px; }
        table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; }
        th, td { padding:12px; border:1px solid #ddd; text-align:center; font-size:14px; }
        th { background:#012147; color:#fff; }
        .btn { padding:6px 12px; border:none; border-radius:6px; text-decoration:none; }
        .view-btn { background:#012147; color:#fff; }
        .view-btn:hover { background:#021634; color:#fff; }
    </style>
</head>
<body>
<div class="container">
    <h1>Lecturer Payments</h1>

    <form method="GET" action="{{ route('admin.lecturer-payments.index') }}" class="search-box">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name or username..." value="{{ $search }}">
            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
            @if($search)
                <a href="{{ route('admin.lecturer-payments.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    <table>
        <tr><th>Name</th><th>Username</th><th>Action</th></tr>
        @forelse($lecturers as $lecturer)
            <tr>
                <td>{{ $lecturer->name }}</td>
                <td>{{ $lecturer->username }}</td>
                <td>
                    <a href="{{ route('admin.lecturer-payments.show', $lecturer->id) }}" class="btn view-btn">
                        <i class="fa fa-eye"></i> View Payments
                    </a>
                </td>
            </tr>
        @empty
            <tr><td colspan="3">No lecturers found.</td></tr>
        @endforelse
    </table>
</div>
</body>
</html>