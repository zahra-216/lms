<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Assignments</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#f4f6fb;
}

/* TOPBAR */
.topbar{
    height:60px;
    background:linear-gradient(90deg,#1f2a44,#2c3e70);
    color:white;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 20px;
    position:fixed;
    top:0;
    left:0;
    right:0;
    z-index:1000;
    transition:0.3s;
}
.topbar-profile{
    width:40px;
    height:40px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #fff;
    cursor:pointer;
}
/* HEADER */
.header{
    height:70px;
    background:white;
    display:flex;
    align-items:center;
    padding-left:20px;
    position:fixed;
    top:60px;
    left:0;
    right:0;
    z-index:900;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
    transition:0.3s;
}

.logo-area{
    display:flex;
    align-items:center;
    gap:12px;
}

.logo-area img{
    width:45px;
    height:45px;
    border-radius:8px;
}

/* SIDEBAR */
.sidebar{
    width:260px;
    background:#012147;
    position:fixed;
    top:130px;
    left:-260px;
    bottom:0;
    padding-top:15px;
    box-shadow:4px 0 15px rgba(0,0,0,0.25);
    transition:0.3s;
    z-index:999;
    color:white;
}

/* OPEN */
.sidebar.show{
    left:0;
}

/* CLOSE BUTTON */
.sidebar-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:10px 15px;
    border-bottom:1px solid rgba(255,255,255,0.1);
    margin-bottom:10px;
}

.sidebar-header h5{
    margin:0;
    color:white;
    font-size:16px;
}

.close-btn{
    cursor:pointer;
    font-size:20px;
    color:white;
}

/* LINKS */
.sidebar a{
    display:flex;
    align-items:center;
    gap:10px;
    padding:12px 18px;
    text-decoration:none;
    color:#dbeafe;
    transition:0.3s;
    font-size:15px;
}

/* HOVER */
.sidebar a:hover{
    background:rgba(255,255,255,0.1);
    color:white;
    padding-left:25px;
    border-left:4px solid #60a5fa;
}

/* ACTIVE */
.sidebar a.active{
    background:rgba(255,255,255,0.15);
    border-left:4px solid #3b82f6;
    color:white;
}

/* MAIN */
.main{
     padding:170px 20px 120px;
    transition:0.3s;
}

.main.shift{
    margin-left:260px;
}

/* HEADER BOX */
.page-header{
    background: linear-gradient(135deg,#1e3a8a,#2563eb);
    color:white;
    padding:20px;
    border-radius:14px;
    margin-bottom:20px;
}

/* ASSIGNMENT CARD */
.assignment-card{
    background:white;
    border-radius:14px;
    padding:18px;
    margin-bottom:12px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 4px 10px rgba(0,0,0,0.06);
    border-left:5px solid #3b82f6;
    transition:0.3s;
}

.assignment-card:hover{
    transform:translateY(-3px);
}

/* BADGES */
.badge-active{
    background:#dcfce7;
    color:#166534;
    padding:6px 12px;
    border-radius:20px;
}

.badge-expired{
    background:#fee2e2;
    color:#991b1b;
    padding:6px 12px;
    border-radius:20px;
}
.calendar-box{
    background:white;
    padding:15px;
    border-radius:14px;
    box-shadow:0 4px 10px rgba(0,0,0,0.08);
    position:sticky;
    top:120px;
}

/* calendar grid */
#calendar{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    gap:6px;
}

/* normal day */
.day{
    width:38px;
    height:38px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:10px;
    cursor:pointer;
    background:#f1f5f9;
    transition:0.2s;
    font-size:14px;
}

/* hover */
.day:hover{
    background:#3b82f6;
    color:white;
}

/* 🔴 TODAY */
.today{
    background:#ef4444 !important;
    color:white;
    border-radius:50%;
    font-weight:bold;
    box-shadow:0 0 10px rgba(239,68,68,0.6);
}

/* today box */
.today-box{
    background:#e0f2fe;
    padding:10px;
    border-radius:10px;
}
.day.has-assignment {
    border: 2px solid #3b82f6;
    font-weight: bold;
}
/* FOOTER */
footer{
    background:#012147;
    color:#e2e8f0;
    margin-top:60px;
}

.footer-container{
    padding:60px 25px;
    max-width:1200px;
    margin:auto;
}

.footer-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:30px;
}

.footer-box h5{
    font-size:16px;
    margin-bottom:15px;
    color:#ffffff;
    border-left:4px solid #3b82f6;
    padding-left:10px;
}

.footer-box a{
    display:block;
    color:#cbd5e1;
    text-decoration:none;
    margin-bottom:10px;
}

.footer-box a:hover{
    color:#60a5fa;
    padding-left:6px;
}

.footer-box p{
    margin-bottom:10px;
    color:#cbd5e1;
}

.footer-logo{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:15px;
}

.footer-logo img{
    width:50px;
    height:50px;
    border-radius:10px;
}

.footer-logo h4{
    margin:0;
    color:white;
}

.footer-desc{
    font-size:14px;
    color:#94a3b8;
    line-height:1.6;
}

.footer-bottom{
    background:#050a14;
    text-align:center;
    padding:15px;
    font-size:14px;
    color:#94a3b8;
    border-top:1px solid #1f2937;
}
/* ===== RESPONSIVE FIX ===== */

/* Tablet */
@media (max-width: 992px) {
    .sidebar {
        width: 220px;
        left: -220px;
    }

    .main.shift {
        margin-left: 220px;
    }
}

/* Mobile */
@media (max-width: 768px) {

    .topbar {
        padding: 0 10px;
        font-size: 14px;
    }

    .header {
        padding-left: 10px;
        height: 60px;
    }

    .logo-area img {
        width: 38px;
        height: 38px;
    }

    /* Sidebar becomes full overlay */
    .sidebar {
        width: 80%;
        max-width: 280px;
        left: -100%;
        top: 0;
        height: 100vh;
        z-index: 2000;
    }

    .sidebar.show {
        left: 0;
    }

    /* MAIN remove shifting on mobile */
    .main.shift {
        margin-left: 0 !important;
    }

    .main {
        padding: 140px 10px 20px;
    }

    .footer-grid {
        grid-template-columns: 1fr;
    }
}

/* Small mobile */
@media (max-width: 480px) {

    .topbar b {
        font-size: 14px;
    }

    .subject-title {
        font-size: 18px;
    }

    .note-item {
        font-size: 14px;
        padding: 10px;
    }

    .sidebar {
        width: 100%;
    }
}
/* SHIFT FOR MAIN */
.main.shift{
    margin-left:260px;
    transition:0.3s;
}

/* ✅ HEADER SHIFT */
.header.shift{
    margin-left:260px;
    transition:0.3s;
}

/* ✅ FOOTER SHIFT */
footer.shift{
    margin-left:260px;
    transition:0.3s;
}

/* MOBILE FIX */
@media (max-width:768px){
    .main.shift,
    .header.shift,
    footer.shift{
        margin-left:0 !important;
    }
}
</style>
</head>

<body>

<!-- TOPBAR -->
<div class="topbar">

    <div class="left-top">
        <i class="bi bi-list icon-btn" onclick="toggleSidebar()"></i>
        <b>LMS Portal</b>
    </div>

    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-bell icon-btn"></i>
        <i class="bi bi-chat icon-btn"></i>

        <div class="small text-white">
            {{ $student->name }} ({{ $student->registration_no }})
        </div>

        <div class="dropdown">
           <img 
    src="{{ $student->photo 
        ? asset('storage/'.$student->photo) 
        : asset('images/user.png') }}"
    class="topbar-profile dropdown-toggle"
    data-bs-toggle="dropdown"
>

             <ul class="dropdown-menu dropdown-menu-end">

              <li>
    <a class="dropdown-item" href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
</li>
                <li><a class="dropdown-item" href="{{ route('student.profile') }}">
    <i class="bi bi-person"></i> Profile
</a></li>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="dropdown-item text-danger">
        Logout
    </button>
</form>

            </ul>
        </div>
    </div>
</div>

<!-- HEADER -->
<div class="header">
    <div class="logo-area">
        <img src="{{ asset('images/logo.png.jpeg') }}">
        <div>
            <div class="campus-name">TT Metro Campus</div>
            <div class="lms-name">Learning Management System</div>
        </div>
    </div>
</div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
        <!-- HEADER -->
    <div class="sidebar-header">
        
        <span class="close-btn" onclick="toggleSidebar()">✖</span>
    </div>

      <li>
    <a class="dropdown-item" href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
</li>
    <a href="{{ route('home') }}"><i class="bi bi-house"></i> Site Home</a>
    <a href="#"><i class="bi bi-calendar"></i> Calendar</a>
   <a href="{{ route('student.my.courses') }}">
    <i class="bi bi-book"></i> My Courses
</a>
</div>


<!-- MAIN -->
<div class="main" id="main">

<div class="row g-3">

    <!-- LEFT: ASSIGNMENTS -->
    <div class="col-lg-8">

        <div class="page-header">
            <h4 class="mb-0">
                <i class="bi bi-journal-text me-2"></i>
                {{ $subject->name }} - Assignments
            </h4>
        </div>

        @if($assignments->count() > 0)

          @foreach($assignments as $assignment)

<div class="assignment-card">

    <div>
          {{-- ✅ DOWNLOAD LINK --}}
       <a href="{{ asset('storage/' . $assignment->file_path) }}" download>
    <b>{{ $assignment->title }}</b>
</a>
<div class="text-danger small mt-1">
    ⏳ <span class="countdown"
          data-deadline="{{ $assignment->due_date }}">
    </span>
</div>
        <div class="text-muted small mt-1">
            🕒 {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y h:i A') }}
        </div>
    </div>

    <div>
        @if(now() > $assignment->due_date)
            <span class="badge-expired">Expired</span>
        @else
            <span class="badge-active">Active</span>
        @endif
    </div>

</div>

<div class="mb-4">

@if(now() <= $assignment->due_date)

<form action="{{ route('assignment.submit') }}" method="POST" enctype="multipart/form-data"
      class="p-3 border rounded bg-light">

    @csrf
    <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

    <input type="file" name="file" class="form-control mb-2" required>

    <button class="btn btn-primary btn-sm">Submit</button>
</form>

{{-- ✅ FIXED SUBMISSION --}}
@php
    $submission = $assignment->submissions->first();
@endphp

@if($submission)
    <div class="alert alert-success mt-2 p-2">
        ✅ Submitted <br>
        🕒 {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y h:i A') }}

        <br>
        <a href="{{ asset('storage/'.$submission->file) }}" target="_blank">
            📎 View File
        </a>
    </div>
@else
    <div class="text-danger small mt-2">❌ Not submitted</div>
@endif

@else
    <small class="text-danger">⛔ Deadline passed</small>
@endif

</div>

<hr>

@endforeach
        @else

            <div class="text-center text-muted mt-5">
                <i class="bi bi-inbox fs-1"></i>
                <p>No assignments found</p>
            </div>

        @endif

    </div>

    <!-- RIGHT: CALENDAR -->
    <div class="col-lg-4">

        <div class="calendar-box">

            <h5 class="mb-3">📅 Assignment Calendar</h5>

            <div id="calendar"></div>

            <hr>

            <div class="today-box">
                <b>Today:</b>
                <span id="todayDate"></span>
            </div>

            <div id="selectedDateBox" class="mt-3 text-muted small">
                Click a date to view details
            </div>

            <script>
                let assignments = @json($assignments);
            </script>

        </div>

    </div>

</div>

</div>
<!-- FOOTER (YOUR DESIGN) -->
<footer id="footer">
    <div class="footer-container">

        <div class="footer-grid">

            <!-- CAMPUS INFO -->
            <div class="footer-box">
                <div class="footer-logo">
                    <img src="{{ asset('images/logo.jpeg') }}">
                    <div>
                        <h4>TT Metro Campus</h4>
                        <small>Learning Management System</small>
                    </div>
                </div>

                <p class="footer-desc">
                    A modern LMS platform designed for students and faculty to access notes,
                    assignments, and learning resources easily.
                </p>
            </div>

            <!-- QUICK LINKS -->
            <div class="footer-box">
                <h5>Quick Links</h5>
                <a href="https://techlinktechnology.com/"><i class="bi bi-globe"></i> www.techlinktechnology.com</a>
                <a href="https://ttmetrocampus.com/"><i class="bi bi-globe"></i> ttmetrocampus.com</a>
            </div>

            <!-- CONTACT -->
            <div class="footer-box">
                <h5>Contact Us</h5>

                <p><i class="bi bi-geo-alt"></i> No 11 A1, Galle Road, Mount Lavinia</p>
                <p><i class="bi bi-telephone"></i> 011 4319 996 | 077 2270 348</p>
                <p><i class="bi bi-envelope"></i> Info.ttmcml@gmail.com</p>
            </div>

        </div>

    </div>

    <div class="footer-bottom">
        © 2026 TT Metro Campus LMS | All Rights Reserved
    </div>
</footer>
<!-- SCRIPT -->
<script>
function toggleSidebar(){

    document.getElementById("sidebar").classList.toggle("show");

    if(window.innerWidth > 768){
        document.getElementById("main").classList.toggle("shift");
        document.querySelector(".header").classList.toggle("shift");
        document.getElementById("footer").classList.toggle("shift");
    }
}

/* click outside close */
document.addEventListener("click", function(e){
    let sidebar = document.getElementById("sidebar");
    let btn = document.querySelector(".bi-list");

    if(window.innerWidth <= 768){
        if(!sidebar.contains(e.target) && !btn.contains(e.target)){
            sidebar.classList.remove("show");
        }
    }
});

const calendar = document.getElementById("calendar");
let today = new Date();

document.getElementById("todayDate").innerText = today.toDateString();

function loadCalendar() {

    let year = today.getFullYear();
    let month = today.getMonth();

    let firstDay = new Date(year, month, 1).getDay();
    let daysInMonth = new Date(year, month + 1, 0).getDate();

    calendar.innerHTML = "";

    // empty boxes
    for (let i = 0; i < firstDay; i++) {
        calendar.innerHTML += `<div></div>`;
    }

    for (let d = 1; d <= daysInMonth; d++) {

        let date = new Date(year, month, d);
        let dateStr = date.toISOString().split("T")[0];

        let div = document.createElement("div");
        div.classList.add("day");
        div.innerText = d;

        // 🔴 TODAY
        if (
            d === today.getDate() &&
            month === today.getMonth() &&
            year === today.getFullYear()
        ) {
            div.classList.add("today");
        }

        // 📌 assignment match check
        let dayAssignments = assignments.filter(a => {
            let ad = new Date(a.due_date).toISOString().split("T")[0];
            return ad === dateStr;
        });

        if (dayAssignments.length > 0) {
            div.style.border = "2px solid #3b82f6";
            div.style.borderRadius = "50%";
        }

        // CLICK EVENT
        div.onclick = function () {

            let html = `<b>${date.toDateString()}</b><br><br>`;

            if (dayAssignments.length === 0) {
                html += "No assignments";
            } else {
                dayAssignments.forEach(a => {
                    html += `
                        <div style="margin-bottom:8px;">
                            <b>${a.title}</b><br>
                            🕒 ${new Date(a.due_date).toLocaleTimeString()}
                        </div>
                    `;
                });
            }

            document.getElementById("selectedDateBox").innerHTML = html;
        };

        calendar.appendChild(div);
    }
}

loadCalendar();
function startCountdown() {

    const elements = document.querySelectorAll('.countdown');

    elements.forEach(el => {

        const deadline = new Date(el.getAttribute('data-deadline')).getTime();

        const timer = setInterval(() => {

            let now = new Date().getTime();
            let diff = deadline - now;

            if (diff <= 0) {
                el.innerHTML = "⛔ Expired";
                clearInterval(timer);
                return;
            }

            let days = Math.floor(diff / (1000 * 60 * 60 * 24));
            let hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((diff % (1000 * 60)) / 1000);

            el.innerHTML =
                `${days}d ${hours}h ${minutes}m ${seconds}s`;

        }, 1000);
    });
}

startCountdown();
</script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>