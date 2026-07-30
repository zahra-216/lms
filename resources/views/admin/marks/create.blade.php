<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Enter Marks | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:900px; margin:auto; }

    .back-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover { background:#012147; color:#fff; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:24px 28px; margin-bottom:26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h3 { margin:0; font-weight:700; font-size:20px; }

    .card-box { background:#fff; border-radius:16px; padding:26px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }

    table.marks-table { border-collapse:separate; border-spacing:0; width:100%; }
    table.marks-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 14px; white-space:nowrap; }
    table.marks-table thead th:first-child { border-top-left-radius:10px; }
    table.marks-table thead th:last-child { border-top-right-radius:10px; }
    table.marks-table tbody td { vertical-align:middle; padding:12px 14px; }
    table.marks-table tbody tr:nth-child(even) { background:#f8fafc; }

    .form-control { border-radius:8px; border:1px solid #e2e8f0; }
    .form-control:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }

    .view-link { color:#012147; font-weight:600; text-decoration:none; }
    .view-link:hover { text-decoration:underline; }

    .btn-navy { width:100%; background:#012147; color:#fff; border:none; padding:12px; border-radius:12px; font-weight:600; margin-top:18px; }
    .btn-navy:hover { background:#0b2d5a; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        table.marks-table { display:block; overflow-x:auto; white-space:nowrap; }
    }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.dashboard') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h3><i class="bi bi-clipboard-data"></i> {{ $assignment->title }} - Marks Entry</h3>
    </div>

    <div class="card-box">
        <form method="POST" action="{{ route('admin.marks.store') }}">
            @csrf
            <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

            <div class="table-responsive">
            <table class="table marks-table align-middle">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Subject</th>
                        <th>File</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $sub)
                        <tr>
                            <td>{{ $sub->student->name }}</td>
                            <td>{{ $assignment->subject->name }}</td>
                            <td>
                                <a href="{{ asset('storage/'.$sub->file) }}" target="_blank" class="view-link">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                            <td>
                                <input type="number" name="marks[{{ $sub->student_id }}]" class="form-control" min="0" max="100" required>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <button type="submit" class="btn-navy">Save Marks</button>
        </form>
    </div>
</div>
</body>
</html>