<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
     <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body{
            margin:0;
            font-family: 'Segoe UI', sans-serif;
            background:#f4f6fb;
        }

        /* TOP BAR */
        .topbar{
            height:55px;
            background:#1f2a44;
            color:white;
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:0 15px;
            position:fixed;
            top:0;
            left:0;
            right:0;
            z-index:1000;
        }
  .topbar-profile{
    width:40px;
    height:40px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #fff;
    cursor:pointer;
}
/* ===== NOTIFICATION WRAPPER ===== */
.notif-wrapper{
    position:relative;
    display:inline-flex;
    align-items:center;
}

/* ===== BELL ICON ===== */
.notif-wrapper .icon-btn{
    font-size:22px;
    color:white;
    cursor:pointer;
    transition:0.2s;
    display:flex;
    align-items:center;
    justify-content:center;
    width:40px;
    height:40px;
    border-radius:50%;
}

.notif-wrapper .icon-btn:hover{
    background:rgba(255,255,255,0.1);
    color:#60a5fa;
}

/* ===== BADGE ===== */
.notif-badge{
    position:absolute;
    top:2px;
    right:2px;
    background:#ef4444;
    color:#fff;
    font-size:11px;
    font-weight:bold;
    width:18px;
    height:18px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    line-height:1;
    box-shadow:0 2px 6px rgba(0,0,0,0.2);
}

/* ===== OPTIONAL: SMALL ANIMATION ===== */
.notif-badge{
    animation:pulse 1.5s infinite;
}

@keyframes pulse{
    0%{ transform:scale(1); }
    50%{ transform:scale(1.2); }
    100%{ transform:scale(1); }
}

        .icon-btn{
            font-size:22px;
            cursor:pointer;
        }

        /* HEADER */
        .header{
            height:70px;
            background:white;
            display:flex;
            align-items:center;
            padding-left:15px;
            position:fixed;
            top:55px;
            left:0;
            right:0;
            z-index:900;
            box-shadow:0 2px 8px rgba(0,0,0,0.05);
        }

        .logo-area{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .logo-area img{
            width:50px;
            height:50px;
            border-radius:8px;
        }

        /* SIDEBAR */
        .sidebar{
            width:230px;
            background:white;
            position:fixed;
            top:125px;
            left:-230px; /* hidden */
            bottom:0;
            padding-top:10px;
            box-shadow:2px 0 10px rgba(0,0,0,0.05);
            transition:0.3s;
            z-index:999;
        }

        .sidebar.show{
            left:0;
        }

        .sidebar a{
            display:block;
            padding:12px 18px;
            text-decoration:none;
            color:#333;
        }

        .sidebar a:hover{
            background:#f0f4ff;
            color:#0d6efd;
        }

        /* MAIN */
        .main{
            margin-left:0;
            padding:140px 20px 20px;
            transition:0.3s;
        }

        .main.shift{
            margin-left:230px;
        }

        .card-box{
            background:white;
            padding:20px;
            border-radius:10px;
            box-shadow:0 3px 10px rgba(0,0,0,0.05);
        }

        /* Calendar */
#calendar{ text-align:center; font-size:14px; }
#calendar table{ width:100%; border-collapse:collapse; }
#calendar th{ color:#b22222; font-weight:bold; padding:5px 0; }
#calendar td{ padding:8px; border:1px solid #ddd; }
#calendar .today{ background:#b22222; color:white; border-radius:50%; font-weight:bold;
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
       <div class="dropdown notif-wrapper">
    <a class="icon-btn position-relative" data-bs-toggle="dropdown">
        <i class="bi bi-bell"></i>
        @if($student->unreadNotifications->count() > 0)
            <span class="notif-badge">{{ $student->unreadNotifications->count() }}</span>
        @endif
    </a>

    <ul class="dropdown-menu dropdown-menu-end" style="width:300px;">

        @if($student->unreadNotifications->count() > 0)
            <li class="text-end p-2">
                <button class="btn btn-sm btn-link text-decoration-none" id="markAllReadBtn">
                    Mark all as read
                </button>
            </li>
        @endif

        @forelse($student->unreadNotifications->sortByDesc('created_at') as $notif)
            <li class="dropdown-item d-flex justify-content-between align-items-start">
                <a href="{{ $notif->data['link'] ?? '#' }}" class="text-decoration-none text-dark flex-grow-1">
                    <strong>{{ $notif->data['title'] ?? '' }}</strong><br>
                    <small>{{ $notif->data['message'] ?? '' }}</small>
                </a>
                <button class="btn btn-sm btn-outline-secondary mark-read-btn ms-2" data-id="{{ $notif->id }}">
                    ✓
                </button>
            </li>
        @empty
            <li class="p-2 text-muted text-center">No notifications</li>
        @endforelse

    </ul>
</div>
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
></i>

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

    <div class="card-box mb-4">
        <h4>Welcome Back, {{ $student->name }}! 👋</h4>
         <p>Course: <b>{{ $course->name ?? 'N/A' }}</b></p>

        <!-- ✅ LEVEL NAME (NO ID) -->
        <p>Level: <b>{{ $level->name ?? 'N/A' }}</b></p>
    </div>
<!-- SEMESTER + CALENDAR -->
    
    <div class="row">

        <!-- SEMESTERS -->
        <div class="col-md-8">

            <h5 class="mb-3">Semesters</h5>

            @if($semesters->count() > 0)

            <div class="accordion" id="semesterAccordion">

                @foreach($semesters as $semester)

                <div class="accordion-item mb-2 shadow-sm">

                    <h2 class="accordion-header">

                        <button class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#sem{{ $semester->id }}">

                            📘 {{ $semester->name }}

                        </button>

                    </h2>

                    <div id="sem{{ $semester->id }}"
                         class="accordion-collapse collapse"
                         data-bs-parent="#semesterAccordion">

                        <div class="accordion-body">

                            <button class="btn btn-primary btn-sm mb-2"
                                onclick="loadSubjects({{ $semester->id }})">
                                Load Subjects
                            </button>

                            <div id="subjectBox{{ $semester->id }}"></div>

                        </div>
                    </div>

                </div>

                @endforeach

            </div>

            @else
                <p>No semesters found</p>
            @endif

        </div>
        <!-- SUBJECT LOGIN MODAL -->
<div class="modal fade" id="subjectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
    <h5>Enter Subject Code</h5>

    <button type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
</div>

      <div class="modal-body">

        <input type="hidden" id="subject_id">

        <label>Subject Code</label>
        <input type="text" id="subject_code" class="form-control">

        <small class="text-danger" id="errorMsg"></small>

      </div>

      <div class="modal-footer">
        <button class="btn btn-primary" onclick="checkSubject()">Submit</button>
      </div>

    </div>
  </div>
</div>
        <!-- RIGHT: CALENDAR -->
        <div class="col-md-4">

            <h5 class="mb-3">Calendar</h5>

            <div class="card-box">
                <div id="calendar"></div>
            </div>
            
<div class="card-box mt-4" id="onlinePanel">
    <h5>Online Users (Last 5 Minutes)</h5>

    <div id="onlineList">
        @forelse($onlineUsers as $user)
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <span style="color:green;">●</span>
                    <b>{{ $user->name }}</b><br>
                    <small>{{ $user->registration_no }}</small>
                </div>
            </div>
        @empty
            <p>No users online</p>
        @endforelse
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

<script>

document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".mark-read-btn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();

            let id = this.getAttribute("data-id");
            let item = this.closest("li");

            fetch("/student/notification/read/" + id, {
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
                }
            });
        });
    });

    const markAllBtn = document.getElementById("markAllReadBtn");
    if (markAllBtn) {
        markAllBtn.addEventListener("click", function () {
            fetch("/student/notification/read-all", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });
    }

});

// ================= SIDEBAR =================
function toggleSidebar(){

    document.getElementById("sidebar").classList.toggle("show");

    if (window.innerWidth > 768) {
        document.getElementById("main").classList.toggle("shift");
        document.getElementById("footer").classList.toggle("shift");
    }
}

// ================= SUBJECT LOAD =================
function loadSubjects(semesterId){

    fetch(`/student/semester/${semesterId}/subjects`)
    .then(res => res.json())
    .then(data => {

        let box = document.getElementById('subjectBox' + semesterId);

        let html = `<h6 class="text-primary mb-2">📚 ${data.semester}</h6>`;

        if(!data.subjects || data.subjects.length === 0){
            html += "<p>No subjects found</p>";
        } else {

            html += `<div class="list-group">`;

            data.subjects.forEach(sub => {

                html += `
                    <div class="list-group-item d-flex justify-content-between"
                        onclick="openSubject(${sub.id}, ${sub.unlocked ? 'true' : 'false'})"
                        style="cursor:pointer">

                        <div>
                            <b>${sub.code ?? ''}</b> ${sub.name}
                        </div>

                        <span class="badge bg-success">${sub.credits ?? 0}</span>

                    </div>
                `;
            });

            html += `</div>`;
        }

        box.innerHTML = html;

    })
    .catch(() => alert("Subjects load error ❌"));
}

// ================= SUBJECT MODAL =================
function openSubject(subjectId, unlocked){

    if (unlocked) {
        // already verified before — skip the modal entirely
        window.location.href = '/student/subject/' + subjectId + '/show';
        return;
    }

    document.getElementById('subject_id').value = subjectId;
    var myModal = new bootstrap.Modal(document.getElementById('subjectModal'));
    myModal.show();
}

function checkSubject(){

    let subject_id = document.getElementById('subject_id').value;
    let code = document.getElementById('subject_code').value;

    fetch('/student/verify-subject', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            subject_id: subject_id,
            code: code
        })
    })
    .then(res => res.json())
    .then(data => {

        if(data.status){
            let modal = bootstrap.Modal.getInstance(document.getElementById('subjectModal'));
            modal.hide();

            window.location.href = '/student/subject/' + data.subject_id + '/show';
        } else {
            document.getElementById('errorMsg').innerText = data.message;
        }

    })
    .catch(() => alert("Server error ❌"));
}

// ================= CALENDAR =================
function generateCalendar(){

    let calendar = document.getElementById("calendar");

    let date = new Date();
    let month = date.getMonth();
    let year = date.getFullYear();

    let firstDay = new Date(year, month, 1).getDay();
    let daysInMonth = new Date(year, month+1, 0).getDate();
    let today = date.getDate();

    let months = ["January","February","March","April","May","June",
                  "July","August","September","October","November","December"];

    let html = `<h6>${months[month]} ${year}</h6><table>
    <tr>
        <th>Sun</th><th>Mon</th><th>Tue</th>
        <th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
    </tr><tr>`;

    let day = 1;

    for(let i=0;i<6;i++){
        for(let j=0;j<7;j++){

            if(i===0 && j<firstDay){
                html += "<td></td>";
            }
            else if(day > daysInMonth){
                html += "<td></td>";
            }
            else{
                html += day === today
                    ? `<td class="today">${day}</td>`
                    : `<td>${day}</td>`;
                day++;
            }
        }
        html += "</tr><tr>";
    }

    html += "</tr></table>";

    calendar.innerHTML = html;
}

generateCalendar();

// ================= ONLINE USERS =================
function loadOnlineUsers(){

    fetch('/online-users')
    .then(res => res.json())
    .then(data => {

        let html = '';

        if(data.length === 0){
            html = "<p>No users online</p>";
        }

        data.forEach(user => {
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span style="color:green;">●</span>
                        <b>${user.name}</b><br>
                        <small>${user.registration_no}</small>
                    </div>
                </div>
            `;
        });

        document.getElementById('onlineList').innerHTML = html;
    });
}

// auto refresh
setInterval(loadOnlineUsers, 5000);

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>


