<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TT Metro Campus | Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; color:#012147; }

    /* Sidebar */
    .sidebar {
        width:260px; background:#012147; color:#fff; height:100vh;
        position:fixed; top:0; left:0; z-index:1030; overflow-y:auto;
        transition:transform .3s ease;
    }
    .sidebar-brand {
        padding:22px 20px; background:#01193a; text-align:center;
        font-weight:700; font-size:18px; letter-spacing:0.5px;
    }
    .sidebar .profile {
        padding:16px 20px; display:flex; align-items:center; gap:12px;
        border-bottom:1px solid rgba(255,255,255,0.1);
    }
    .sidebar .profile img { width:46px; height:46px; border-radius:50%; object-fit:cover; border:2px solid rgba(255,255,255,0.3); }
    .sidebar .profile .name { font-weight:600; font-size:14px; }
    .sidebar .profile .email { font-size:11px; color:#93c5fd; }

    .sidebar-nav { padding:14px 12px; }
    .sidebar-nav a {
        color:#cbd5e1; display:flex; align-items:center; gap:10px;
        padding:11px 14px; text-decoration:none; border-radius:10px;
        font-size:14px; margin-bottom:4px; transition:0.2s;
    }
    .sidebar-nav a:hover, .sidebar-nav a.active {
        background:rgba(255,255,255,0.1); color:#fff;
    }
    .sidebar-nav a i { font-size:16px; width:20px; text-align:center; }

    /* Topbar */
    .topbar {
        position:fixed; top:0; left:260px; right:0; height:70px;
        background:#fff; box-shadow:0 2px 12px rgba(0,0,0,0.06);
        display:flex; align-items:center; justify-content:space-between;
        padding:0 26px; z-index:1020;
    }
    .topbar h5 { margin:0; font-weight:700; font-size:16px; color:#012147; }
    .topbar-actions { display:flex; align-items:center; gap:18px; }

    .menu-toggle { display:none; background:none; border:none; font-size:22px; color:#012147; }

    #notifDropdown { position:relative; color:#012147; font-size:20px; }
    #notifDropdown .badge { font-size:9px; padding:4px 6px; }
    .dropdown-menu { border:none; border-radius:14px; box-shadow:0 12px 30px rgba(0,0,0,0.12); padding:0; overflow:hidden; }
    .notif-header { padding:12px 16px; font-weight:700; background:#012147; color:#fff; }
    .dropdown-item { padding:12px 16px; border-bottom:1px solid #f1f5f9; white-space:normal; }
    .dropdown-item:last-child { border-bottom:none; }
    .dropdown-item strong { color:#012147; font-size:13.5px; }
    .dropdown-item small { color:#64748b; font-size:12px; }
    .mark-read-btn { border-radius:50%; width:26px; height:26px; padding:0; font-size:12px; }

    .avatar-btn img { width:38px; height:38px; border-radius:50%; object-fit:cover; border:2px solid #e2e8f0; }

    /* Main */
    .main { margin-left:260px; padding:96px 26px 30px; }

    .page-title { font-weight:700; font-size:22px; margin-bottom:22px; color:#012147; }

    /* Stat cards */
    .stat-card {
        border-radius:16px; padding:22px; color:#fff; position:relative; overflow:hidden;
        background:linear-gradient(135deg,#012147,#1e3a6e);
        box-shadow:0 10px 26px rgba(1,33,71,0.18);
        height:100%;
    }
    .stat-card .stat-icon {
        font-size:26px; width:48px; height:48px; border-radius:12px;
        background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center;
        margin-bottom:14px;
    }
    .stat-card .stat-label { font-size:13px; opacity:0.85; margin-bottom:4px; }
    .stat-card .stat-value { font-size:28px; font-weight:700; }

    /* Calendar */
    .card-box { background:#fff; border-radius:18px; padding:28px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }
    .calendar-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:22px; }
    .calendar-header h4 { margin:0; font-weight:700; font-size:19px; color:#012147; }
    #monthYear { font-size:20px; font-weight:700; color:#012147; }
    .calendar-nav-btn {
        border:none; background:#f1f5f9; color:#012147; width:38px; height:38px;
        border-radius:10px; font-weight:700; transition:0.2s;
    }
    .calendar-nav-btn:hover { background:#012147; color:#fff; }
    .calendar-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:6px; text-align:center; }
    .calendar-day-name { font-weight:600; font-size:13px; color:#64748b; padding:8px 0; }
    .calendar-day { padding:12px 0; font-size:14px; font-weight:500; border-radius:10px; background:#f8fafc; }
    .today { background:#012147 !important; color:#fff; font-weight:700; }

    /* Mobile */
    .sidebar-overlay { display:none; }
    @media (max-width:992px){
        .sidebar { transform:translateX(-100%); }
        .sidebar.open { transform:translateX(0); }
        .topbar { left:0; }
        .main { margin-left:0; }
        .menu-toggle { display:inline-block; }
        .sidebar-overlay.show {
            display:block; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:1025;
        }
    }
    @media (max-width:576px){
        .main { padding:88px 14px 24px; }
        .topbar { padding:0 14px; }
        .topbar h5 { font-size:14px; }
        .stat-card .stat-value { font-size:22px; }
        .card-box { padding:18px; }
        .calendar-day { font-size:12px; padding:10px 0; }
        #monthYear { font-size:16px; }
    }
</style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
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
        <a href="{{ route('admin.dashboard') }}" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="{{ route('admin.students.index') }}"><i class="bi bi-people"></i> Students</a>
        <a href="{{ route('admin.lecturers.index') }}"><i class="bi bi-person-workspace"></i> Lecturers</a>
        <a href="{{ route('admin.faculties.index') }}"><i class="bi bi-person-badge"></i> Faculties</a>
        <a href="{{ route('admin.attendance.index') }}"><i class="bi bi-calendar-check"></i> Attendance</a>
        <a href="{{ route('admin.assignments.browse') }}"><i class="bi bi-journal-text"></i> Assignments</a>
        <a href="{{ route('admin.lecture-records.index') }}"><i class="bi bi-journal-plus"></i> Lecture Records</a>
        <a href="{{ route('admin.payments.index') }}"><i class="bi bi-cash-coin"></i> Student Payments</a>
        <a href="{{ route('admin.lecturer-payments.index') }}"><i class="bi bi-wallet2"></i> Lecturer Payments</a>
        <a href="{{ route('admin.certificates.index') }}"><i class="bi bi-patch-check"></i> Certificate Verification</a>
    </div>
</div>

<!-- Topbar -->
<nav class="topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="menu-toggle" id="menuToggle"><i class="bi bi-list"></i></button>
        <h5>Welcome, {{ auth('admin')->user()->name ?? 'Admin' }} 👋</h5>
    </div>

    <div class="topbar-actions">
        <div class="dropdown">
            <a href="#" id="notifDropdown" data-bs-toggle="dropdown">
                <i class="bi bi-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ auth('admin')->user()->unreadNotifications->count() }}
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" style="width:320px;">
                <li class="notif-header d-flex justify-content-between align-items-center">
                    <span>Notifications</span>
                    @if(auth('admin')->user()->unreadNotifications->count() > 0)
                        <button class="btn btn-sm btn-link text-white text-decoration-none p-0" id="markAllReadBtn" style="font-size:12px;">Mark all read</button>
                    @endif
                </li>

                @if(auth('admin')->user()->unreadNotifications->count() == 0)
                    <li class="text-center text-muted p-3">No new notifications</li>
                @endif

                @foreach(auth('admin')->user()->unreadNotifications->sortByDesc('created_at') as $notif)
                    <li class="dropdown-item d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <strong>{{ $notif->data['title'] }}</strong><br>
                            <small>{{ $notif->data['message'] }}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary mark-read-btn" data-id="{{ $notif->id }}">✓</button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="dropdown">
            <a href="#" class="avatar-btn dropdown-toggle text-decoration-none" id="profileDropdown" data-bs-toggle="dropdown">
                <img src="{{ auth('admin')->user()->photo ? asset('uploads/admin/' . auth('admin')->user()->photo) : asset('images/admin_avatar.png') }}">
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bi bi-person me-2"></i> Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bi bi-gear me-2"></i> Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main -->
<div class="main">
    <div class="page-title">Admin Dashboard</div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-people"></i></div>
                <div class="stat-label">Total Students</div>
                <div class="stat-value">{{ \App\Models\Student::count() }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-person-badge"></i></div>
                <div class="stat-label">Total Faculty</div>
                <div class="stat-value">{{ \App\Models\Faculty::count() }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-journal-bookmark"></i></div>
                <div class="stat-label">Total Courses</div>
                <div class="stat-value">{{ \App\Models\Course::count() }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-mortarboard"></i></div>
                <div class="stat-label">Total Lecturers</div>
                <div class="stat-value">{{ \App\Models\Lecturer::count() }}</div>
            </div>
        </div>
    </div>

    <div class="card-box">
        <div class="calendar-header">
            <button id="prevMonth" class="calendar-nav-btn">←</button>
            <div id="monthYear"></div>
            <button id="nextMonth" class="calendar-nav-btn">→</button>
        </div>
        <div class="calendar-grid" id="calendarDays"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    });
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    });

    const calendarDays = document.getElementById("calendarDays");
    const monthYear = document.getElementById("monthYear");
    let currentDate = new Date();

    function renderCalendar(date) {
        calendarDays.innerHTML = "";
        const month = date.getMonth();
        const year = date.getFullYear();
        monthYear.innerText = date.toLocaleString("default", { month: "long", year: "numeric" });

        const firstDay = new Date(year, month, 1).getDay();
        const lastDate = new Date(year, month + 1, 0).getDate();

        ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"].forEach(day => {
            const div = document.createElement("div");
            div.classList.add("calendar-day-name");
            div.innerText = day;
            calendarDays.appendChild(div);
        });

        for(let i=0;i<firstDay;i++){
            calendarDays.appendChild(document.createElement("div"));
        }

        for(let i=1;i<=lastDate;i++){
            const dayDiv = document.createElement("div");
            dayDiv.classList.add("calendar-day");
            dayDiv.innerText = i;

            const today = new Date();
            if(i === today.getDate() && month === today.getMonth() && year === today.getFullYear()){
                dayDiv.classList.add("today");
            }
            calendarDays.appendChild(dayDiv);
        }
    }

    document.getElementById("prevMonth").onclick = () => { currentDate.setMonth(currentDate.getMonth()-1); renderCalendar(currentDate); };
    document.getElementById("nextMonth").onclick = () => { currentDate.setMonth(currentDate.getMonth()+1); renderCalendar(currentDate); };
    renderCalendar(currentDate);

    const notifCount = document.querySelector("#notifDropdown .badge");
    const notifList = document.querySelector(".dropdown-menu");

    document.querySelectorAll(".mark-read-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            let id = this.getAttribute("data-id");
            let item = this.closest("li");

            fetch("/admin/notification/read/" + id, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    item.remove();
                    let count = parseInt(notifCount.innerText);
                    notifCount.innerText = count > 0 ? count - 1 : 0;
                }
            });
        });
    });

    const markAllBtn = document.getElementById("markAllReadBtn");
    if (markAllBtn) {
        markAllBtn.addEventListener("click", function () {
            fetch("/admin/notification/read-all", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) location.reload();
            });
        });
    }
});
</script>
</body>
</html>