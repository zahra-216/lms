<!DOCTYPE html>
<html>
<head>
<title>Marks List</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background:#f4f6fb;">

<div class="container mt-5"><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Marks | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
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
    .page-header h3 { margin:0; font-weight:700; font-size:20px; }

    .card-box { background:#fff; border-radius:16px; padding:20px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }

    table.marks-table { border-collapse:separate; border-spacing:0; width:100%; }
    table.marks-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 14px; white-space:nowrap; }
    table.marks-table thead th:first-child { border-top-left-radius:10px; }
    table.marks-table thead th:last-child { border-top-right-radius:10px; }
    table.marks-table tbody td { vertical-align:middle; padding:12px 14px; }
    table.marks-table tbody tr:nth-child(even) { background:#f8fafc; }
    table.marks-table tbody tr:hover { background:#eef2f9; }

    .grade-badge { background:#eef2f9; color:#012147; padding:6px 14px; border-radius:20px; font-weight:600; font-size:13px; }

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
        <h3><i class="bi bi-bar-chart-line"></i> All Marks</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <div class="card-box">
        <div class="table-responsive">
        <table class="table marks-table align-middle">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Assignment</th>
                    <th>Subject</th>
                    <th>Marks</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marks as $mark)
                    <tr>
                        <td>{{ $mark->student->name }}</td>
                        <td>{{ $mark->assignment->title }}</td>
                        <td>{{ $mark->assignment->subject->name }}</td>
                        <td>{{ $mark->marks }}</td>
                        <td><span class="grade-badge">{{ $mark->grade }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
</body>
</html>

<div class="d-flex justify-content-between mb-3">
    <h4>📊 All Marks</h4>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<table class="table table-bordered bg-white">

<thead>
<tr>
    <th>Student</th>
    <th>Assignment</th>
    <th>Subject</th>
    <th>Marks</th>
    <th>Grade</th>
</tr>
</thead>

<tbody>

@foreach($marks as $mark)

<tr>
    <td>{{ $mark->student->name }}</td>

    <td>{{ $mark->assignment->title }}</td>

    <td>{{ $mark->assignment->subject->name }}</td>

    <td>{{ $mark->marks }}</td>

    <td>
        <span class="badge bg-primary">
            {{ $mark->grade }}
        </span>
    </td>
</tr>

@endforeach

</tbody>

</table>

</div>

</body>
</html>