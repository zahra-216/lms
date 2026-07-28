<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject->name }} - Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
        .date-form{ flex-direction:column; align-items:flex-start !important; }
        .date-form > div{ width:100%; }
    }

    .container { max-width:900px; margin:auto; }

    .back-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .back-btn:hover{ background:#012147; color:#fff; }

    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:26px 30px; margin:18px 0 26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }
    .page-header small{ opacity:0.85; }

    .card-box{
        background:#fff; padding:20px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:26px;
    }

    .date-form label{ font-weight:600; color:#012147; font-size:14px; margin-bottom:4px; display:block; }
    .date-form input[type="date"]{ border-radius:10px; padding:10px 14px; border:1px solid #e2e8f0; }

    .input-group-text{ border-radius:10px 0 0 10px !important; }
    #studentSearch{ border-radius:0 10px 10px 0 !important; }

    table.attendance-table{ border-collapse:separate; border-spacing:0; }
    table.attendance-table thead th{
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap;
    }
    table.attendance-table thead th:first-child{ border-top-left-radius:10px; }
    table.attendance-table thead th:last-child{ border-top-right-radius:10px; }
    table.attendance-table tbody td{ vertical-align:middle; padding:10px; }
    table.attendance-table tbody tr:nth-child(even){ background:#f8fafc; }
    table.attendance-table tbody tr:hover{ background:#eef2f9; }

    .form-check-input{ width:18px; height:18px; cursor:pointer; }

    .btn-navy{ background:#012147; color:#fff; border:none; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }
</style>
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="back-btn">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <a href="{{ route('lecturer.subject.attendance.history', $subject->id) }}" class="back-btn">
            <i class="bi bi-clock-history"></i> View History
        </a>
    </div>

    <div class="page-header">
        <div>
            <h2><i class="bi bi-calendar-check"></i> {{ $subject->code }} - {{ $subject->name }}</h2>
            <small>Attendance</small>
        </div>
        <i class="bi bi-clipboard2-check" style="font-size:44px; opacity:0.85;"></i>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    @endif

    @if($alreadyMarked)
        <div class="alert alert-warning rounded-3">
            <i class="bi bi-exclamation-triangle"></i> Attendance for {{ $date }} has already been marked. Saving again will overwrite it.
        </div>
    @endif

    <div class="card-box">
        <form method="GET" action="{{ route('lecturer.subject.attendance', $subject->id) }}" class="date-form mb-4 d-flex gap-2 align-items-end">
            <div>
                <label>Select Date</label>
                <input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}" class="form-control" onchange="this.form.submit()">
            </div>
        </form>

        <form method="POST" action="{{ route('lecturer.subject.attendance.store', $subject->id) }}">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="mb-4">
                <div class="input-group shadow-sm" style="max-width:400px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="studentSearch" class="form-control border-start-0 ps-0"
                        placeholder="Search by name or reg no..."
                        style="box-shadow:none;">
                </div>
            </div>

            <div class="table-responsive">
            <table class="table attendance-table align-middle">
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Student Name</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Not Marked</th>
                    </tr>
                </thead>
                <tbody id="attendanceTableBody">
                    @foreach($students as $student)
                        <tr>
                            <td>{{ $student->registration_no }}</td>
                            <td>{{ $student->name }}</td>
                            <td>
                                <input class="form-check-input" type="radio" name="status[{{ $student->id }}]" value="present">
                            </td>
                            <td>
                                <input class="form-check-input" type="radio" name="status[{{ $student->id }}]" value="absent">
                            </td>
                            <td>
                                <input class="form-check-input" type="radio" name="status[{{ $student->id }}]" value="" checked>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <button type="submit" class="btn btn-navy">
                <i class="bi bi-save"></i> Save Attendance
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('studentSearch').addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#attendanceTableBody tr');

    rows.forEach(row => {
        const regNo = row.children[0].textContent.toLowerCase();
        const name = row.children[1].textContent.toLowerCase();
        row.style.display = (regNo.includes(query) || name.includes(query)) ? '' : 'none';
    });
});
</script>

</body>
</html>