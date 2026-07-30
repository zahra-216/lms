<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faculties | TT Metro Campus Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; color:#012147; }

    .sidebar { width:260px; background:#012147; color:#fff; min-height:100vh; position:fixed; top:0; left:0; z-index:1030; overflow-y:auto; transition:transform .3s ease; }
    .sidebar-brand { padding:22px 20px; background:#01193a; text-align:center; font-weight:700; font-size:18px; }
    .sidebar .profile { padding:16px 20px; display:flex; align-items:center; gap:12px; border-bottom:1px solid rgba(255,255,255,0.1); }
    .sidebar .profile img { width:46px; height:46px; border-radius:50%; object-fit:cover; border:2px solid rgba(255,255,255,0.3); }
    .sidebar .profile .name { font-weight:600; font-size:14px; }
    .sidebar .profile .email { font-size:11px; color:#93c5fd; }
    .sidebar-nav { padding:14px 12px; }
    .sidebar-nav a { color:#cbd5e1; display:flex; align-items:center; gap:10px; padding:11px 14px; text-decoration:none; border-radius:10px; font-size:14px; margin-bottom:4px; transition:0.2s; }
    .sidebar-nav a:hover, .sidebar-nav a.active { background:rgba(255,255,255,0.1); color:#fff; }
    .sidebar-nav a i { font-size:16px; width:20px; text-align:center; }

    .topbar { position:fixed; top:0; left:260px; right:0; height:70px; background:#fff; box-shadow:0 2px 12px rgba(0,0,0,0.06); display:flex; align-items:center; justify-content:space-between; padding:0 26px; z-index:1020; }
    .topbar h5 { margin:0; font-weight:700; font-size:16px; color:#012147; }
    .menu-toggle { display:none; background:none; border:none; font-size:22px; color:#012147; }

    .main { margin-left:260px; padding:96px 26px 30px; }
    .page-title { font-weight:700; font-size:22px; margin-bottom:6px; color:#012147; }
    .page-subtitle { color:#64748b; font-size:14px; margin-bottom:26px; }

    .add-btn { background:#012147; color:#fff; padding:10px 20px; border-radius:12px; text-decoration:none; font-weight:600; font-size:14px; display:inline-flex; align-items:center; gap:8px; box-shadow:0 6px 16px rgba(1,33,71,0.15); transition:.2s; }
    .add-btn:hover { background:#1e3a6e; color:#fff; }

    .alert-success-pill { background:#d1fae5; color:#065f46; padding:12px 18px; border-radius:12px; font-size:14px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:8px; }

    .faculty-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:22px; }

    /* Outer wrapper is a DIV (not a link) so it can safely contain both
       the clickable overlay link AND the edit/delete buttons */
    .faculty-card { position:relative; background:#fff; border-radius:18px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,.07); transition:transform .25s ease, box-shadow .25s ease; }
    .faculty-card:hover { transform:translateY(-6px); box-shadow:0 14px 32px rgba(0,0,0,.12); }

    .faculty-card-link { position:absolute; inset:0; z-index:1; }
    .faculty-card-img { height:150px; background:#f2f5fb; overflow:hidden; }
    .faculty-card-img img { width:100%; height:100%; object-fit:cover; display:block; }
    .faculty-card-body { padding:18px 20px 20px; position:relative; z-index:0; }
    .faculty-card-body h3 { margin:0 0 8px; font-size:17px; color:#012147; font-weight:700; }
    .faculty-card-cta { font-size:13px; font-weight:600; color:#012147; display:inline-flex; align-items:center; gap:6px; }

    .card-actions { position:absolute; top:12px; right:12px; display:flex; gap:6px; z-index:5; }
    .card-actions button, .card-actions a { width:32px; height:32px; border-radius:9px; border:none; display:flex; align-items:center; justify-content:center; font-size:13px; box-shadow:0 4px 10px rgba(0,0,0,0.15); }
    .icon-edit { background:#ffc107; color:#012147; }
    .icon-delete { background:#ef4444; color:#fff; }

    @media (max-width:992px){
        .sidebar { transform:translateX(-100%); }
        .sidebar.open { transform:translateX(0); }
        .topbar { left:0; }
        .main { margin-left:0; }
        .menu-toggle { display:inline-block; }
    }
    @media (max-width:576px){
        .main { padding:88px 14px 24px; }
        .faculty-grid { grid-template-columns:1fr; }
    }
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">TT METRO CAMPUS</div>
    <div class="profile">
        <img src="{{ auth('admin')->user()->profile_image ?? asset('images/logo.png.jpeg') }}">
        <div>
            <div class="name">{{ auth('admin')->user()->name ?? 'Admin' }}</div>
            <div class="email">{{ auth('admin')->user()->email ?? 'admin@example.com' }}</div>
        </div>
    </div>
    <div class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="{{ route('admin.students.index') }}"><i class="bi bi-people"></i> Students</a>
        <a href="{{ route('admin.lecturers.index') }}"><i class="bi bi-person-workspace"></i> Lecturers</a>
        <a href="{{ route('admin.faculties.index') }}" class="active"><i class="bi bi-person-badge"></i> Faculties</a>
        <a href="{{ route('admin.attendance.index') }}"><i class="bi bi-calendar-check"></i> Attendance</a>
        <a href="{{ route('admin.lecture-records.index') }}"><i class="bi bi-journal-plus"></i> Lecture Records</a>
        <a href="{{ route('admin.payments.index') }}"><i class="bi bi-cash-coin"></i> Student Payments</a>
        <a href="{{ route('admin.lecturer-payments.index') }}"><i class="bi bi-wallet2"></i> Lecturer Payments</a>
    </div>
</div>

<nav class="topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="menu-toggle" id="menuToggle"><i class="bi bi-list"></i></button>
        <h5>Faculties</h5>
    </div>
</nav>

<div class="main">
    <div class="page-title">Faculties</div>
    <div class="page-subtitle">Select a faculty to manage its courses, semesters, and subjects.</div>

    @if(session('success'))
        <div class="alert-success-pill"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('admin.faculties.create') }}" class="add-btn"><i class="bi bi-plus-lg"></i> Add Faculty</a>
    </div>

    <div class="faculty-grid">
        @foreach($faculties as $faculty)
            @php
                $imgPath = $faculty->image ? asset('storage/faculty/'.$faculty->image) : 'https://picsum.photos/320/220?random='.$faculty->id;
            @endphp
            <div class="faculty-card">
                <a href="{{ route('admin.faculties.courses', $faculty->id) }}" class="faculty-card-link" aria-label="Manage {{ $faculty->name }}"></a>

                <div class="card-actions">
                    <a href="{{ route('admin.faculties.edit', $faculty->id) }}" class="icon-edit"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('admin.faculties.destroy', $faculty->id) }}" method="POST" onsubmit="return confirm('Delete this faculty?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="icon-delete"><i class="bi bi-trash"></i></button>
                    </form>
                </div>

                <div class="faculty-card-img"><img src="{{ $imgPath }}" alt="{{ $faculty->name }}"></div>
                <div class="faculty-card-body">
                    <h3>{{ $faculty->name }}</h3>
                    <span class="faculty-card-cta">Manage Courses <i class="bi bi-arrow-right"></i></span>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('menuToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
});
</script>
</body>
</html>