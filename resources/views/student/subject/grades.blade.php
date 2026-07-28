<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject->name }} - Grades</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body{ margin:0; font-family:'Segoe UI', sans-serif; background:#f4f6fb; }
    .topbar{
        height:55px; background:#1f2a44; color:white; display:flex;
        justify-content:space-between; align-items:center; padding:0 15px;
        position:fixed; top:0; left:0; right:0; z-index:1000;
    }
    .topbar-profile{ width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid #fff; cursor:pointer; }
    .header{
        height:70px; background:white; display:flex; align-items:center;
        padding-left:15px; position:fixed; top:55px; left:0; right:0;
        z-index:900; box-shadow:0 2px 8px rgba(0,0,0,0.05);
    }
    .logo-area{ display:flex; align-items:center; gap:10px; }
    .logo-area img{ width:50px; height:50px; border-radius:8px; }
    .main{ padding:140px 20px 30px; max-width:1000px; margin:0 auto; }

    @media (max-width:576px){
        .main{ padding:130px 12px 20px; }
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
    }

    .back-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover{ background:#012147; color:#fff; }

    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:26px 30px; margin-bottom:24px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2{ margin:0 0 8px; font-weight:700; font-size:22px; }
    .page-header-icon{ font-size:44px; opacity:0.85; }
    .subject-pill{ background:rgba(255,255,255,0.15); padding:6px 14px; border-radius:20px; font-size:13px; }

    .section-card{
        background:white; border-radius:16px; box-shadow:0 6px 20px rgba(0,0,0,0.06);
        margin-bottom:22px; overflow:hidden;
    }
    .section-card-header{
        display:flex; align-items:center; gap:10px;
        padding:16px 22px; background:#eef2f9; border-bottom:1px solid #e2e8f0;
        font-weight:700; color:#012147; font-size:16px;
    }
    .section-card-body{ padding:6px 0; }

    table { width:100%; border-collapse:collapse; }
    th, td { padding:12px 22px; border-bottom:1px solid #eef0f4; text-align:left; }
    th { background:#012147; color:#fff; font-size:13px; text-transform:uppercase; letter-spacing:0.5px; }
    tbody tr:last-child td{ border-bottom:none; }
    tr:hover td { background:#f8fafc; }

    .grade-badge{
        display:inline-block; min-width:34px; text-align:center;
        padding:5px 12px; border-radius:20px; font-weight:700; font-size:13px;
    }
    .grade-A{ background:#dcfce7; color:#15803d; }
    .grade-B{ background:#dbeafe; color:#1d4ed8; }
    .grade-C{ background:#fef3c7; color:#b45309; }
    .grade-F{ background:#fee2e2; color:#b91c1c; }
    .grade-none{ background:#e2e8f0; color:#64748b; }

    .empty-state{ text-align:center; color:#94a3b8; padding:36px 0; }

    .overall-banner{
        background:linear-gradient(120deg,#0ea5e9,#0369a1); color:#fff;
        border-radius:16px; padding:22px 28px; margin-top:6px;
        box-shadow:0 10px 24px rgba(3,105,161,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;
    }
    .overall-banner .label{ font-size:13px; opacity:0.9; text-transform:uppercase; letter-spacing:0.5px; }
    .overall-banner .value{ font-size:26px; font-weight:800; }
</style>
</head>
<body>

<div class="topbar">
    <b>LMS Portal</b>
    <div class="d-flex align-items-center gap-3">
        <div class="small text-white">{{ $student->name }} ({{ $student->registration_no }})</div>
        <div class="dropdown">
            <img src="{{ $student->photo ? asset('storage/'.$student->photo) : asset('images/user.png') }}"
                 class="topbar-profile dropdown-toggle" data-bs-toggle="dropdown">
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li><a class="dropdown-item" href="{{ route('student.profile') }}"><i class="bi bi-person"></i> Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('student.my.payments') }}"><i class="bi bi-wallet2"></i> My Payments</a></li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                </form>
            </ul>
        </div>
    </div>
</div>

<div class="header">
    <div class="logo-area">
        <img src="{{ asset('images/logo.png.jpeg') }}">
        <div>
            <div class="campus-name">TT Metro Campus</div>
            <div class="lms-name">Learning Management System</div>
        </div>
    </div>
</div>

<div class="main">

    <a href="{{ route('student.subject.portal.show', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <div>
            <h2><i class="bi bi-bar-chart-line"></i> {{ $subject->code }} — {{ $subject->name }}</h2>
            <span class="subject-pill">Grades</span>
        </div>
        <i class="bi bi-clipboard-data page-header-icon"></i>
    </div>

    <!-- Assignments -->
    <div class="section-card">
        <div class="section-card-header">
            <i class="bi bi-journal-text"></i> Assignments
        </div>
        <div class="section-card-body">
            <table>
                <thead>
                    <tr><th>Assignment</th><th>Marks</th><th>Grade</th></tr>
                </thead>
                <tbody>
                    @forelse($subject->assignments as $assignment)
                        @php $mark = $assignment->marks->first(); @endphp
                        <tr>
                            <td>{{ $assignment->title }}</td>
                            <td>{{ $mark->marks ?? 'Not graded yet' }}</td>
                            <td>
                                @if($mark && $mark->grade)
                                    <span class="grade-badge grade-{{ $mark->grade }}">{{ $mark->grade }}</span>
                                @else
                                    <span class="grade-badge grade-none">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="empty-state">No assignments for this subject yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Overall Subject Marks -->
    <div class="section-card">
        <div class="section-card-header">
            <i class="bi bi-graph-up"></i> Overall Subject Marks
        </div>
        <div class="section-card-body">
            <table>
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
                        <td>
                            @if($subjectMark->final_grade)
                                <span class="grade-badge grade-{{ $subjectMark->final_grade }}">{{ $subjectMark->final_grade }}</span>
                            @else
                                <span class="grade-badge grade-none">-</span>
                            @endif
                        </td>
                    </tr>
                    @else
                    <tr><td colspan="4" class="empty-state">No marks recorded yet for this subject.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="overall-banner">
        <span class="label">Overall Grade</span>
        <span class="value">{{ $subjectMark->final_grade ?? 'N/A' }}</span>
    </div>

</div>

</body>
</html>