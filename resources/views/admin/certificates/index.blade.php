<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Certificate Verification | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:1100px; margin:auto; }

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
    .page-header h2 { margin:0 0 4px; font-weight:700; font-size:22px; }
    .page-header small { opacity:0.85; }

    .search-box { max-width:420px; margin:0 0 25px; }
    .search-box .form-control { border-radius:10px 0 0 10px; }
    .search-box .btn { border-radius:0 10px 10px 0; }

    .card-box { background:#fff; border-radius:14px; box-shadow:0 8px 26px rgba(0,0,0,0.06); overflow:hidden; }

    table { width:100%; border-collapse:collapse; }
    th, td { padding:12px 14px; border-bottom:1px solid #e2e8f0; text-align:left; font-size:13.5px; }
    th { background:#012147; color:#fff; font-weight:600; }
    tbody tr:nth-child(even) { background:#f8fafc; }
    tbody tr:hover { background:#eef2f9; }

    .cert-badge { background:#eef2f9; color:#012147; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .manage-btn { background:#012147; color:#fff; padding:7px 14px; border-radius:8px; text-decoration:none; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px; }
    .manage-btn:hover { background:#1e3a6e; color:#fff; }

    .pagination { margin-top:16px; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        table { display:block; overflow-x:auto; white-space:nowrap; }
    }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.dashboard') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h2><i class="bi bi-patch-check"></i> Certificate Verification</h2>
        <small>Search a student and manage their certificates.</small>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.certificates.index') }}" class="search-box">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name or reg no..." value="{{ $search }}">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
            @if($search)
                <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    <div class="card-box">
        <div class="table-responsive">
        <table>
            <tr>
                <th>Reg No</th>
                <th>Name</th>
                <th>Course</th>
                <th>Certificates</th>
                <th>Action</th>
            </tr>
            @forelse($students as $student)
                <tr>
                    <td>{{ $student->registration_no }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->course->name ?? '—' }}</td>
                    <td><span class="cert-badge">{{ $student->certificates_count }}</span></td>
                    <td>
                        <a href="{{ route('admin.certificates.student', $student->id) }}" class="manage-btn">
                            <i class="bi bi-folder2-open"></i> Manage
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No students found.</td></tr>
            @endforelse
        </table>
        </div>
    </div>

    <div class="pagination">
        {{ $students->links() }}
    </div>
</div>
</body>
</html>