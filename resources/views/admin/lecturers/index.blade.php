<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lecturers | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:1000px; margin:auto; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:26px 30px; margin-bottom:26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2 { margin:0; font-weight:700; font-size:22px; }
    .page-header small { opacity:0.85; }

    .btn-add {
        background:#fff; color:#012147; font-weight:600; padding:10px 20px;
        border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;
        transition:0.2s;
    }
    .btn-add:hover { background:#e2e8f0; color:#012147; }

    .card-box { background:#fff; border-radius:16px; padding:20px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }

    table.lecturer-table { width:100%; border-collapse:separate; border-spacing:0; }
    table.lecturer-table thead th {
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 14px; text-align:left; white-space:nowrap;
    }
    table.lecturer-table thead th:first-child { border-top-left-radius:10px; }
    table.lecturer-table thead th:last-child { border-top-right-radius:10px; }
    table.lecturer-table tbody td { vertical-align:middle; padding:12px 14px; }
    table.lecturer-table tbody tr:nth-child(even) { background:#f8fafc; }
    table.lecturer-table tbody tr:hover { background:#eef2f9; }

    .action-btn {
        display:inline-flex; align-items:center; gap:6px; padding:6px 14px;
        border-radius:8px; text-decoration:none; font-size:13px; font-weight:600; border:none;
    }
    .edit-btn { background:#eef2f9; color:#012147; }
    .edit-btn:hover { background:#012147; color:#fff; }
    .delete-btn { background:#fee2e2; color:#991b1b; cursor:pointer; }
    .delete-btn:hover { background:#dc2626; color:#fff; }

    .empty-note { text-align:center; color:#6b7280; padding:20px; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header { flex-direction:column; align-items:flex-start; }
        table.lecturer-table { display:block; overflow-x:auto; white-space:nowrap; }
    }
</style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-person-workspace"></i> Lecturers</h2>
            <small>{{ $lecturers->count() }} total</small>
        </div>
        <a href="{{ route('admin.lecturers.create') }}" class="btn-add">
            <i class="bi bi-plus-circle"></i> Add Lecturer
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <div class="card-box">
        <div class="table-responsive">
        <table class="table lecturer-table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($lecturers as $lecturer)
                    <tr>
                        <td>{{ $lecturer->id }}</td>
                        <td>{{ $lecturer->username }}</td>
                        <td>{{ $lecturer->name }}</td>
                        <td>{{ $lecturer->email ?? '—' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.lecturers.edit', $lecturer->id) }}" class="action-btn edit-btn">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('admin.lecturers.destroy', $lecturer->id) }}" method="POST" onsubmit="return confirm('Delete this lecturer?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-note">No lecturers found.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
</body>
</html>