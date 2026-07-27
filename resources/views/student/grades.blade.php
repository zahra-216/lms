<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Grades</title>
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
    .main{ padding:140px 20px 20px; }

    .welcome-banner{
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:26px 30px; margin-bottom:24px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .welcome-banner h4{ margin:0 0 8px; font-weight:700; }
    .welcome-pill{ background:rgba(255,255,255,0.15); padding:6px 14px; border-radius:20px; font-size:13px; }
    .welcome-icon{ font-size:44px; opacity:0.85; }

    .card-box{ background:white; padding:24px; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.06); }
    .section-title{ font-weight:700; color:#012147; margin-bottom:16px; display:flex; align-items:center; gap:8px; }

    table { width:100%; border-collapse:collapse; }
    th, td { padding:12px; border-bottom:1px solid #eef0f4; }
    th { background:#012147; color:#fff; text-align:left; }
    tr:hover td { background:#f8fafc; }
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
    <div class="welcome-banner">
        <div>
            <h4>📚 My Grades</h4>
            <span class="welcome-pill">{{ $student->name }}</span>
            <span class="welcome-pill">{{ $student->course->name ?? 'N/A' }}</span>
        </div>
        <i class="bi bi-clipboard-data welcome-icon"></i>
    </div>

    <div class="card-box">
        <div class="section-title"><i class="bi bi-journal-bookmark"></i> Modules This Semester</div>

        <table>
            <thead><tr><th>Subject</th><th>Final Grade</th></tr></thead>
            <tbody>
                @forelse($subjects as $subject)
                    @php $sm = $subject->subjectMarks->first(); @endphp
                    <tr>
                        <td>
                            <a href="{{ route('student.subject.grades', $subject->id) }}" style="text-decoration:none; font-weight:600; color:#012147;">
                                📘 {{ $subject->name }}
                            </a>
                        </td>
                        <td>
                            @if($sm && $sm->final_grade)
                                <span class="badge bg-primary">{{ $sm->final_grade }}</span>
                            @else
                                <span class="badge bg-secondary">Not Graded</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-center text-muted py-3">No modules found for your current semester.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</body>
</html>