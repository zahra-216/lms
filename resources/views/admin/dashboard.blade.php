<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TT Mentor Admin Dashboard</title>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body { font-family: 'Inter', sans-serif; background:#f4f6f9; margin:0; }

/* Sidebar */
.sidebar { width:250px; background:#1e293b; color:#fff; min-height:100vh; position:fixed; }
.sidebar h3 { padding:20px; background:#111827; margin:0; font-size:20px; text-align:center; font-weight:700; }
.sidebar .profile { padding:15px; display:flex; align-items:center; gap:10px; border-bottom:1px solid #374151; }
.sidebar .profile img { width:50px; height:50px; border-radius:50%; }
.sidebar .profile div small { display:block; font-size:0.8rem; color:#10b981; }
.sidebar a { color:#fff; display:flex; align-items:center; padding:12px 20px; text-decoration:none; }
.sidebar a:hover { background:#374151; border-radius:8px; }
.sidebar .profile img,
.navbar img {
    object-fit: cover;
    border: 2px solid #fff;
    transition: 0.3s;
}

.sidebar .profile img:hover,
.navbar img:hover {
    transform: scale(1.1);
}
/* Top Navbar */
.navbar { position: fixed; top: 0; left: 250px; width: calc(100% - 250px); background: #fff; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.navbar .badge { font-size: 0.6rem; padding: 0.25em 0.4em; }

/* Notification Dropdown */
.dropdown-menu { max-height: 300px; overflow-y: auto; }

/* Main Content */
.main { margin-left:250px; padding:100px 30px 30px; }

/* Cards */
.card { border-radius:15px; box-shadow:0 6px 25px rgba(0,0,0,0.08); background:#012147; color:white; position:relative; overflow:hidden; padding-top:50px; }
.card-icon { font-size:2.5rem; margin-top:10px; }
.card .card-profile { position:absolute; top:-25px; right:15px; width:60px; height:60px; border-radius:50%; border:3px solid white; object-fit:cover; }
.card .card-email { font-size:0.8rem; color:#a5b4fc; }
/* 🔔 Notification Bell */
#notifDropdown {
    position: relative;
    transition: 0.3s;
}

#notifDropdown:hover {
    transform: scale(1.1);
}

/* 🔴 Badge */
#notifDropdown .badge {
    font-size: 10px;
    padding: 5px 6px;
}

/* 📦 Dropdown Box */
.dropdown-menu {
    width: 320px;
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    padding: 0;
    overflow: hidden;
}

/* 🔝 Header */
.dropdown-menu::before {
    content: "Notifications";
    display: block;
    padding: 10px;
    font-weight: 600;
    background: #012147;
    color: #fff;
    text-align: center;
}

/* 📋 List Item */
.dropdown-item {
    padding: 12px;
    transition: 0.3s;
    border-bottom: 1px solid #eee;
}

.dropdown-item:last-child {
    border-bottom: none;
}

/* 🖱 Hover Effect */
.dropdown-item:hover {
    background: #f1f5f9;
}

/* 📝 Title */
.dropdown-item strong {
    color: #012147;
    font-size: 14px;
}

/* 💬 Message */
.dropdown-item small {
    color: #555;
    font-size: 12px;
}

/* ✅ Tick Button */
.mark-read-btn {
    border-radius: 50%;
    width: 28px;
    height: 28px;
    padding: 0;
    font-size: 14px;
    line-height: 1;
}

.mark-read-btn:hover {
    background: #012147;
    color: #fff;
}

/* ❌ Empty State */
.dropdown-menu .text-muted {
    font-size: 13px;
    padding: 15px;
}
/* Calendar */
.calendar-card{ background:#fff; padding:35px; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,0.08); width:100%; }
.calendar-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; }
#monthYear{ font-size:28px; font-weight:700; color:#1e293b; }
.calendar-grid{ display:grid; grid-template-columns: repeat(7, 1fr); gap:8px; text-align:center; }
.calendar-day-name{ font-weight:600; font-size:18px; color:#64748b; padding:12px 0; }
.calendar-day{ padding:15px; font-size:20px; font-weight:500; border-radius:15px; cursor:pointer; background:#f8fafc; transition:0.2s; }
.calendar-day:hover{ background:#e2e8f0; transform:scale(1.05); }
.today{ background:#3b82f6 !important; color:white; font-weight:700; }

/* Responsive */
@media (max-width:768px){ .sidebar{width:200px;} .main{margin-left:200px;padding-top:90px;} .calendar-day{padding:18px;font-size:16px;} #monthYear{font-size:22px;} .navbar { left: 200px; width: calc(100% - 200px); } }
@media (max-width:576px){ .sidebar{position:relative;width:100%;min-height:auto;} .main{margin-left:0;padding-top:140px;} .calendar-day{padding:15px;font-size:15px;} .navbar { left: 0; width: 100%; } }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3>TT METRO CAMPUS</h3>
    <div class="profile">
        <img src="{{ auth('admin')->user()->profile_image ?? asset('images/logo.png.jpeg') }}">
        <div>
            <div>{{ auth('admin')->user()->name ?? 'Admin' }}</div>
            <small>{{ auth('admin')->user()->email ?? 'admin@example.com' }}</small>
        </div>
    </div>
<a href="{{ route('admin.students.index') }}">
    <i class="bi bi-people me-2"></i> Students
</a>

<a href="{{ route('admin.faculties.index') }}">
    <i class="bi bi-person-badge me-2"></i> Faculty
</a>

<a href="{{ route('admin.courses.index') }}">
    <i class="bi bi-journal-bookmark me-2"></i> Courses
</a>

<a href="{{ route('admin.levels.index') }}">
    <i class="bi bi-bar-chart-steps me-2"></i> Levels
</a>

<a href="{{ route('admin.semesters.index') }}">
    <i class="bi bi-calendar3 me-2"></i> Semesters
</a>

<a href="{{ route('admin.subjects.index') }}">
    <i class="bi bi-book me-2"></i> Subjects
</a>



<a href="{{ route('admin.subjects.notes.index', ['subject' => 1]) }}">
    <i class="bi bi-file-earmark-text me-2"></i> Notes
</a>
<a href="{{ route('admin.enrollments.index') }}">
    <i class="bi bi-card-checklist me-2"></i> Enrollments
</a>
<a href="{{ route('admin.assignments.index') }}">
    <i class="bi bi-journal-text me-2"></i> Assignments
</a>
<a href="{{ route('admin.assignments.submissions', $assignment->id ?? 1) }}">
    <i class="bi bi-clipboard-check me-2"></i> Submissions
</a>
<a href="{{ route('admin.marks.index') }}">
    <i class="bi bi-clipboard-data me-2"></i> Marks
</a>
</div>
 

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-light px-4">
<div class="container-fluid">
<h5 class="mb-0 me-4">Welcome, {{ auth('admin')->user()->name ?? 'Admin' }} 👋</h5>

<div class="d-flex align-items-center ms-auto gap-3">

    <!-- Notifications -->
    <div class="dropdown">
        <a href="#" class="position-relative text-dark fs-4" id="notifDropdown" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>

            <!-- 🔴 Dynamic Count -->
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ auth('admin')->user()->unreadNotifications->count() }}
            </span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end p-2" style="width:300px;">

            {{-- ✅ No Notification --}}
            @if(auth('admin')->user()->unreadNotifications->count() > 0)
                <li class="text-end p-2">
                    <button class="btn btn-sm btn-link text-decoration-none" id="markAllReadBtn">
                        Mark all as read
                    </button>
                </li>
                @endif
            @if(auth('admin')->user()->unreadNotifications->count() == 0)
                <li class="text-center text-muted p-2">
                    No new notifications
                </li>
            @endif

            {{-- ✅ Notification List (Latest First) --}}
            @foreach(auth('admin')->user()->unreadNotifications->sortByDesc('created_at') as $notif)
            <li class="dropdown-item d-flex justify-content-between align-items-start">

                <div>
                    <strong>{{ $notif->data['title'] }}</strong><br>
                    <small>{{ $notif->data['message'] }}</small>
                </div>

                <!-- ✅ Mark as Read -->
                <button class="btn btn-sm btn-outline-secondary mark-read-btn" data-id="{{ $notif->id }}">
                    ✓
                </button>

            </li>
            @endforeach

        </ul>
    </div>

    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ auth('admin')->user()->photo 
    ? asset('uploads/admin/' . auth('admin')->user()->photo) 
    : asset('images/admin_avatar.png') }}" 
    width="40" height="40" class="rounded-circle">
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
           <li>
    <a class="dropdown-item" href="{{ route('admin.profile') }}">
        <i class="bi bi-person me-2"></i> Profile
    </a>
</li>

<li>
    <a class="dropdown-item" href="{{ route('admin.settings') }}">
        <i class="bi bi-gear me-2"></i> Settings
    </a>
</li>
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
</div>
</nav>

<!-- Main Content -->
<div class="main">
<h2 class="mb-4">Admin Dashboard</h2>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <i class="bi bi-people card-icon mt-3"></i>
            <h5 class="mt-2">Total Students</h5>
            <h2>{{ \App\Models\Student::count() }}</h2>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <i class="bi bi-person-badge card-icon mt-3"></i>
            <h5 class="mt-2">Total Faculty</h5>
            <h2>{{ \App\Models\Faculty::count() }}</h2>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <i class="bi bi-journal-bookmark card-icon mt-3"></i>
            <h5 class="mt-2">Total Courses</h5>
            <h2>{{ \App\Models\Course::count() }}</h2>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <i class="bi bi-file-earmark-text card-icon mt-3"></i>
            <h5 class="mt-2">Total Enrollments</h5>
            <h2>{{ \App\Models\Enrollment::count() }}</h2>
        </div>
    </div>
</div>

<!-- Calendar -->
<h4 class="mb-3">Calendar</h4>
<div class="calendar-card">
    <div class="calendar-header">
        <button id="prevMonth" class="btn btn-outline-primary btn-lg">←</button>
        <div id="monthYear"></div>
        <button id="nextMonth" class="btn btn-outline-primary btn-lg">→</button>
    </div>
    <div class="calendar-grid" id="calendarDays"></div>
</div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
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

        const days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
        days.forEach(day => {
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

    document.getElementById("prevMonth").onclick = ()=>{
        currentDate.setMonth(currentDate.getMonth()-1);
        renderCalendar(currentDate);
    };

    document.getElementById("nextMonth").onclick = ()=>{
        currentDate.setMonth(currentDate.getMonth()+1);
        renderCalendar(currentDate);
    };

    renderCalendar(currentDate);
});

document.addEventListener("DOMContentLoaded", function () {

    // 🧠 Get elements
    const notifCount = document.querySelector("#notifDropdown .badge");
    const notifList = document.querySelector(".dropdown-menu");

    // 🔔 Mark as Read (individual)
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

                    // ❌ remove item
                    item.remove();

                    // 🔢 update count
                    let count = parseInt(notifCount.innerText);
                    notifCount.innerText = count > 0 ? count - 1 : 0;

                    // 🧠 if no notifications
                    if (notifList.querySelectorAll("li").length === 0) {
                        notifList.innerHTML = `
                            <li class="text-center text-muted p-3">
                                No new notifications
                            </li>
                        `;
                        notifCount.innerText = 0;
                    }
                }

            });

        });
    });

    // ✅ Mark all as Read
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

                if (data.success) {
                    notifList.innerHTML = `
                        <li class="text-center text-muted p-3">
                            No new notifications
                        </li>
                    `;
                    notifCount.innerText = 0;
                }

            });

        });
    }

});

</script>

</body>
</html>