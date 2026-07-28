<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Enter Marks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }

    @media (max-width:576px){
        body { padding:20px 12px; }
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
        color:#fff; border-radius:18px; padding:24px 28px; margin:18px 0 26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h4{ margin:0; font-weight:700; font-size:20px; }
    .page-header small{ opacity:0.85; }

    .card-box{
        background:#fff; padding:24px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06);
    }

    table.marks-entry-table{ border-collapse:separate; border-spacing:0; }
    table.marks-entry-table thead th{
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap;
    }
    table.marks-entry-table thead th:first-child{ border-top-left-radius:10px; }
    table.marks-entry-table thead th:last-child{ border-top-right-radius:10px; }
    table.marks-entry-table tbody td{ vertical-align:middle; padding:10px; }
    table.marks-entry-table tbody tr:nth-child(even){ background:#f8fafc; }
    table.marks-entry-table tbody tr:hover{ background:#eef2f9; }

    .form-control{ border-radius:8px; border:1px solid #e2e8f0; }
    .form-control:focus{ border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }

    .btn-navy{ background:#012147; color:#fff; border:none; padding:12px; font-weight:600; border-radius:10px; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }
</style>
</head>
<body>

<div class="container">
    <a href="{{ route('lecturer.subject.grades', $assignment->subject_id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back to Grades
    </a>

    <div class="page-header">
        <h4><i class="bi bi-pencil-square"></i> {{ $assignment->title }} - Marks Entry</h4>
        <small>{{ $assignment->subject->name }}</small>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-box">
    @if($submissions->count())
    <form method="POST" action="{{ route('lecturer.marks.store') }}">
        @csrf
        <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

        <div class="table-responsive">
        <table class="table marks-entry-table align-middle">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Reg No</th>
                    <th>File</th>
                    <th>Marks (0-100)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $sub)
                <tr>
                    <td>{{ $sub->student->name ?? 'Unknown' }}</td>
                    <td>{{ $sub->student->registration_no ?? '—' }}</td>
                    <td>
                        @if($sub->file)
                            <a href="{{ asset('storage/'.$sub->file) }}" target="_blank"><i class="bi bi-download"></i> View</a>
                        @else
                            <span class="text-muted">No file</span>
                        @endif
                    </td>
                    <td>
                        <input type="number"
                               name="marks[{{ $sub->student_id }}]"
                               class="form-control"
                               min="0"
                               max="100"
                               value="{{ $existingMarks[$sub->student_id] ?? '' }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        <button class="btn btn-navy w-100">
            <i class="bi bi-save"></i> Save Marks
        </button>
    </form>
    @else
        <p class="text-muted mb-0">No students have submitted this assignment yet — marks can't be entered until there's a submission.</p>
    @endif
    </div>
</div>

</body>
</html>