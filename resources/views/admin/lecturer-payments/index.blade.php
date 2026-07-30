<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lecturer Payments | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:1000px; margin:auto; }

    .back-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover { background:#012147; color:#fff; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:26px 30px; margin-bottom:22px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h2 { margin:0; font-weight:700; font-size:22px; }

    .search-box { max-width:420px; margin:0 auto 25px; }

    .card-box { background:#fff; border-radius:16px; padding:20px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }

    table.lp-table { border-collapse:separate; border-spacing:0; width:100%; }
    table.lp-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 14px; white-space:nowrap; }
    table.lp-table thead th:first-child { border-top-left-radius:10px; }
    table.lp-table thead th:last-child { border-top-right-radius:10px; }
    table.lp-table tbody td { vertical-align:middle; padding:12px 14px; text-align:center; }
    table.lp-table tbody tr:nth-child(even) { background:#f8fafc; }
    table.lp-table tbody tr:hover { background:#eef2f9; }

    .view-btn {
        background:#012147; color:#fff; padding:8px 16px; border-radius:8px;
        text-decoration:none; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px;
    }
    .view-btn:hover { background:#1e3a6e; color:#fff; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        table.lp-table { display:block; overflow-x:auto; white-space:nowrap; }
    }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.dashboard') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h2><i class="bi bi-wallet2"></i> Lecturer Payments</h2>
    </div>

    <form method="GET" action="{{ route('admin.lecturer-payments.index') }}" class="search-box">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name or username..." value="{{ $search }}">
            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
            @if($search)
                <a href="{{ route('admin.lecturer-payments.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    <div class="card-box">
        <div class="table-responsive">
        <table class="table lp-table align-middle">
            <thead>
                <tr><th>Name</th><th>Username</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse($lecturers as $lecturer)
                    <tr>
                        <td>{{ $lecturer->name }}</td>
                        <td>{{ $lecturer->username }}</td>
                        <td>
                            <a href="{{ route('admin.lecturer-payments.show', $lecturer->id) }}" class="view-btn">
                                <i class="bi bi-eye"></i> View Payments
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted py-4">No lecturers found.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
</body>
</html>