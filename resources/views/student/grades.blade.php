<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject->name }} - Grades</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
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
    .section-title{
        font-weight:700; color:#012147; margin-bottom:16px;
        display:flex; align-items:center; gap:8px; font-size:17px;
    }

    table.grades-table{ border-collapse:separate; border-spacing:0; }
    table.grades-table thead th{
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap;
    }
    table.grades-table thead th:first-child{ border-top-left-radius:10px; }
    table.grades-table thead th:last-child{ border-top-right-radius:10px; }
    table.grades-table tbody td{ vertical-align:middle; padding:10px; }
    table.grades-table tbody tr:nth-child(even){ background:#f8fafc; }
    table.grades-table tbody tr:hover{ background:#eef2f9; }

    .overall-banner{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:14px; padding:20px 24px;
        display:flex; justify-content:space-between; align-items:center;
        box-shadow:0 8px 24px rgba(1,33,71,0.2);
    }
    .overall-banner .grade-value{ font-size:28px; font-weight:700; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('student.subject.portal.show', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <div>
            <h2><i class="bi bi-clipboard-data"></i> {{ $subject->code }} - {{ $subject->name }}</h2>
            <small>Grades</small>
        </div>
        <i class="bi bi-mortarboard" style="font-size:44px; opacity:0.85;"></i>
    </div>

    <div class="card-box">
        <div class="section-title"><i class="bi bi-journal-text"></i> Assignments</div>
        <div class="table-responsive">
        <table class="table grades-table align-middle mb-0">
            <thead>
                <tr><th>Assignment</th><th>Marks</th><th>Grade</th></tr>
            </thead>
            <tbody>
                @forelse($subject->assignments as $assignment)
                    @php $mark = $assignment->marks->first(); @endphp
                    <tr>
                        <td>{{ $assignment->title }}</td>
                        <td>{{ $mark->marks ?? 'Not graded yet' }}</td>
                        <td>{{ $mark->grade ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted text-center">No assignments for this subject yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="card-box">
        <div class="section-title"><i class="bi bi-bar-chart-steps"></i> Overall Subject Marks</div>
        <div class="table-responsive">
        <table class="table grades-table align-middle mb-0">
            <thead>
                <tr>
                    <th>Mid Marks</th>
                    <th>Final Exam</th>
                    <th>Final Mark</th>
                    <th>Final Grade</th>
                </tr>
            </thead>
            <tbody>
                @if($subjectMark)
                <tr>
                    <td>{{ $subjectMark->mid_marks }}</td>
                    <td>{{ $subjectMark->final_exam_marks }}</td>
                    <td>{{ $subjectMark->final_marks }}</td>
                    <td><strong>{{ $subjectMark->final_grade }}</strong></td>
                </tr>
                @else
                <tr><td colspan="4" class="text-muted text-center">No marks recorded yet for this subject.</td></tr>
                @endif
            </tbody>
        </table>
        </div>
    </div>

    <div class="overall-banner">
        <span><i class="bi bi-award"></i> Overall Grade</span>
        <span class="grade-value">{{ $subjectMark->final_grade ?? 'N/A' }}</span>
    </div>
</div>
</body>
</html>