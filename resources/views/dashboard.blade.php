<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        .notif-wrapper{ position:relative; display:inline-flex; align-items:center; }
        .notif-wrapper .icon-btn{
            font-size:22px; color:white; cursor:pointer; transition:0.2s;
            display:flex; align-items:center; justify-content:center;
            width:40px; height:40px; border-radius:50%;
        }
        .notif-wrapper .icon-btn:hover{ background:rgba(255,255,255,0.1); color:#60a5fa; }
        .notif-badge{
            position:absolute; top:2px; right:2px; background:#ef4444; color:#fff;
            font-size:11px; font-weight:bold; width:18px; height:18px; border-radius:50%;
            display:flex; align-items:center; justify-content:center; line-height:1;
            box-shadow:0 2px 6px rgba(0,0,0,0.2); animation:pulse 1.5s infinite;
        }
        @keyframes pulse{ 0%{transform:scale(1);} 50%{transform:scale(1.2);} 100%{transform:scale(1);} }
        .icon-btn{ font-size:22px; cursor:pointer; }

        /* HEADER */
        .header{
            height:70px; background:white; display:flex; align-items:center;
            padding-left:15px; position:fixed; top:55px; left:0; right:0;
            z-index:900; box-shadow:0 2px 8px rgba(0,0,0,0.05);
        }
        .logo-area{ display:flex; align-items:center; gap:10px; }
        .logo-area img{ width:50px; height:50px; border-radius:8px; }

        /* MAIN */
        .main{ margin-left:0; padding:140px 20px 20px; transition:0.3s; }

        /* WELCOME BANNER */
        .welcome-banner{
            background:linear-gradient(120deg,#012147,#1e3a6e);
            color:#fff;
            border-radius:18px;
            padding:30px 34px;
            margin-bottom:26px;
            box-shadow:0 10px 30px rgba(1,33,71,0.25);
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:16px;
        }
        .welcome-banner h4{ margin:0 0 8px; font-weight:700; }
        .welcome-pills{ display:flex; gap:10px; flex-wrap:wrap; }
        .welcome-pill{
            background:rgba(255,255,255,0.15);
            padding:6px 14px;
            border-radius:20px;
            font-size:13px;
        }
        .welcome-icon{
            font-size:48px;
            opacity:0.85;
        }

        /* QUICK LINK TILES */
        .quick-tiles{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));
            gap:16px;
            margin-bottom:26px;
        }
        .quick-tile{
            background:#fff;
            border-radius:14px;
            padding:20px;
            box-shadow:0 6px 18px rgba(0,0,0,0.06);
            text-decoration:none;
            color:#012147;
            display:flex;
            align-items:center;
            gap:14px;
            transition:0.2s;
        }
        .quick-tile:hover{ transform:translateY(-3px); box-shadow:0 10px 24px rgba(0,0,0,0.1); color:#012147; }
        .quick-tile i{
            font-size:26px;
            width:48px; height:48px;
            border-radius:12px;
            display:flex; align-items:center; justify-content:center;
            color:#fff;
        }
        .tile-grades i{ background:#3b82f6; }
        .tile-payments i{ background:#10b981; }
        .tile-profile i{ background:#f59e0b; }
        .quick-tile span{ font-weight:600; }

        .card-box{
            background:white; padding:20px; border-radius:14px;
            box-shadow:0 6px 20px rgba(0,0,0,0.06);
        }
        .section-title{
            font-weight:700; color:#012147; margin-bottom:16px;
            display:flex; align-items:center; gap:8px;
        }

        /* SEMESTER ACCORDION */
        .accordion-item{ border-radius:12px !important; overflow:hidden; border:none !important; }
        .accordion-button{
            font-weight:600; color:#012147 !important;
            background:#f1f5f9 !important;
        }
        .accordion-button:not(.collapsed){ background:#012147 !important; color:#fff !important; }
        .accordion-button:focus{ box-shadow:none; }

        .upcoming-class-item{
            display:flex;
            align-items:center;
            gap:14px;
            padding:14px 16px;
            border-radius:12px;
            background:#f8fafc;
            transition:0.2s;
        }
        .upcoming-class-item:hover{
            background:#eef2f9;
        }
        .upcoming-class-icon{
            width:42px; height:42px;
            border-radius:10px;
            background:linear-gradient(135deg,#012147,#1e3a6e);
            color:#fff;
            display:flex; align-items:center; justify-content:center;
            font-size:18px;
            flex-shrink:0;
        }
        .upcoming-class-title{
            font-weight:600;
            color:#012147;
            font-size:15px;
        }
        .upcoming-class-time{
            font-size:13px;
            color:#64748b;
            margin-top:2px;
        }
        .upcoming-class-time i{
            margin-right:4px;
        }
        .upcoming-class-day{
            background:#e0e7ff;
            color:#1e3a6e;
            font-size:12px;
            font-weight:700;
            padding:5px 12px;
            border-radius:20px;
            white-space:nowrap;
        }

        /* Calendar */
        #calendar{ text-align:center; font-size:14px; }
        #calendar table{ width:100%; border-collapse:collapse; }
        #calendar th{ color:#b22222; font-weight:bold; padding:5px 0; }
        #calendar td{ padding:8px; border:1px solid #ddd; }
        #calendar .today{ background:#b22222; color:white; border-radius:50%; font-weight:bold; }

        /* FOOTER */
        footer{ background:#012147; color:#e2e8f0; margin-top:60px; }
        .footer-container{ padding:60px 25px; max-width:1200px; margin:auto; }
        .footer-grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:30px; }
        .footer-box h5{ font-size:16px; margin-bottom:15px; color:#ffffff; border-left:4px solid #3b82f6; padding-left:10px; }
        .footer-box a{ display:block; color:#cbd5e1; text-decoration:none; margin-bottom:10px; }
        .footer-box a:hover{ color:#60a5fa; padding-left:6px; }
        .footer-box p{ margin-bottom:10px; color:#cbd5e1; }
        .footer-logo{ display:flex; align-items:center; gap:12px; margin-bottom:15px; }
        .footer-logo img{ width:50px; height:50px; border-radius:10px; }
        .footer-logo h4{ margin:0; color:white; }
        .footer-desc{ font-size:14px; color:#94a3b8; line-height:1.6; }
        .footer-bottom{ background:#050a14; text-align:center; padding:15px; font-size:14px; color:#94a3b8; border-top:1px solid #1f2937; }

        @media (max-width: 992px) { .main.shift { margin-left: 220px; } }
        @media (max-width: 768px) {
            .topbar { padding: 0 10px; font-size: 14px; }
            .header { padding-left: 10px; height: 60px; }
            .logo-area img { width: 38px; height: 38px; }
            .main.shift { margin-left: 0 !important; }
            .main { padding: 140px 10px 20px; }
            .footer-grid { grid-template-columns: 1fr; }
            .welcome-banner{ flex-direction:column; align-items:flex-start; }
        }
        @media (max-width: 480px) {
            .topbar b { font-size: 14px; }
            .topbar-name { display: none; }
            #calendar td { padding: 4px 2px; font-size: 12px; }
            #calendar th { font-size: 12px; }
        }
    </style>
</head>

<body>

<!-- TOPBAR -->
<div class="topbar">
    <div class="left-top"><b>LMS Portal</b></div>

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
                        <button class="btn btn-sm btn-outline-secondary mark-read-btn ms-2" data-id="{{ $notif->id }}">✓</button>
                    </li>
                @empty
                    <li class="p-2 text-muted text-center">No notifications</li>
                @endforelse
            </ul>
        </div>
        <a href="{{ route('student.chat.index') }}" class="text-white text-decoration-none position-relative d-inline-flex" style="width:40px;height:40px;">
            <i class="bi bi-chat icon-btn" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;"></i>
            @php
                $unreadChatCount = \App\Models\ChatMessage::where('student_id', $student->id)
                    ->where('sender_type', 'lecturer')
                    ->where('is_read', false)
                    ->count();
            @endphp
            @if($unreadChatCount > 0)
                <span class="notif-badge">{{ $unreadChatCount }}</span>
            @endif
        </a>

        <div class="small text-white topbar-name">
          {{ $student->name }} ({{ $student->registration_no }})
        </div>

        <div class="dropdown">
            <img
                src="{{ $student->photo ? asset('storage/'.$student->photo) : asset('images/user.png') }}"
                class="topbar-profile dropdown-toggle"
                data-bs-toggle="dropdown">

            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a></li>
                <li><a class="dropdown-item" href="{{ route('student.profile') }}">
                    <i class="bi bi-person"></i> Profile
                </a></li>
                <li><a class="dropdown-item" href="{{ route('student.my.payments') }}">
                    <i class="bi bi-wallet2"></i> My Payments
                </a></li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">Logout</button>
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

<!-- MAIN -->
<div class="main">

    <!-- WELCOME BANNER -->
    <div class="welcome-banner">
        <div>
            <h4>Welcome Back, {{ $student->name }}! 👋</h4>
            <div class="welcome-pills">
                <span class="welcome-pill"><i class="bi bi-mortarboard"></i> {{ $course->name ?? 'N/A' }}</span>
                <span class="welcome-pill"><i class="bi bi-bar-chart-steps"></i> {{ $level->name ?? 'N/A' }}</span>
            </div>
        </div>
        <i class="bi bi-person-workspace welcome-icon"></i>
    </div>

    <!-- QUICK TILES -->
    <div class="quick-tiles">
        <a href="{{ route('student.grades') }}" class="quick-tile tile-grades">
            <i class="bi bi-clipboard-data"></i>
            <span>My Grades</span>
        </a>
        <a href="{{ route('student.my.payments') }}" class="quick-tile tile-payments">
            <i class="bi bi-wallet2"></i>
            <span>My Payments</span>
        </a>
        <a href="{{ route('student.profile') }}" class="quick-tile tile-profile">
            <i class="bi bi-person-circle"></i>
            <span>Profile</span>
        </a>
    </div>

    <!-- SEMESTER + CALENDAR -->
    <div class="row">

        <!-- SEMESTERS -->
        <div class="col-md-8 mb-4">
            <div class="card-box">
                <div class="section-title"><i class="bi bi-journal-bookmark"></i> Semesters</div>

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
                    <p class="text-muted">No semesters found</p>
                @endif
            </div>

            <div class="card-box mt-4">
                <div class="section-title"><i class="bi bi-calendar-week"></i> Upcoming Classes</div>

                @if($upcomingClasses->count() > 0)
                    <div class="d-flex flex-column gap-2">
                        @foreach($upcomingClasses as $class)
                            <div class="upcoming-class-item">
                                <div class="upcoming-class-icon">
                                    <i class="bi bi-book"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="upcoming-class-title">{{ $class->subject->name ?? 'N/A' }}</div>
                                    <div class="upcoming-class-time">
                                        <i class="bi bi-clock"></i>
                                        {{ \Carbon\Carbon::parse($class->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($class->end_time)->format('h:i A') }}
                                    </div>
                                </div>
                                <span class="upcoming-class-day">{{ $class->day }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No upcoming classes</p>
                @endif
            </div>

            <div class="card-box mt-4">
                <div class="section-title"><i class="bi bi-clipboard-check"></i> Pending Assignments</div>

            @if($pendingAssignments->count() > 0)
                <div class="list-group">
                    @foreach($pendingAssignments as $a)
                        <a href="{{ route('student.subject.portal.assignments', $a->subject_id) }}"
                        class="list-group-item d-flex justify-content-between align-items-center text-decoration-none">
                            <div>
                                <b class="text-dark">{{ $a->subject->name ?? 'N/A' }}</b><br>
                                <span class="text-dark">{{ $a->title }}</span>
                            </div>
                            @if($a->due_date->isPast())
                                <span class="badge bg-danger">Overdue</span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    {{ $a->due_date->diffForHumans(now(), true) }} left
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">No pending assignments</p>
            @endif
            </div>
        </div>



        <!-- SUBJECT LOGIN MODAL -->
        <div class="modal fade" id="subjectModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5>Enter Subject Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        <div class="col-md-4 mb-4">
            <div class="card-box">
                <div class="section-title"><i class="bi bi-calendar3"></i> Calendar</div>
                <div id="calendar"></div>
            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <footer id="footer">
        <div class="footer-container">
            <div class="footer-grid">
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

                <div class="footer-box">
                    <h5>Quick Links</h5>
                    <a href="https://techlinktechnology.com/"><i class="bi bi-globe"></i> www.techlinktechnology.com</a>
                    <a href="https://ttmetrocampus.com/"><i class="bi bi-globe"></i> ttmetrocampus.com</a>
                </div>

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

</div>

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
            .then(data => { if (data.success) item.remove(); });
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
            .then(data => { if (data.success) location.reload(); });
        });
    }
});

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
                        <div><b>${sub.code ?? ''}</b> ${sub.name}</div>
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

function openSubject(subjectId, unlocked){
    if (unlocked) {
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
        body: JSON.stringify({ subject_id: subject_id, code: code })
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

function generateCalendar(){
    let calendar = document.getElementById("calendar");
    let date = new Date();
    let month = date.getMonth();
    let year = date.getFullYear();
    let firstDay = new Date(year, month, 1).getDay();
    let daysInMonth = new Date(year, month+1, 0).getDate();
    let today = date.getDate();
    let months = ["January","February","March","April","May","June","July","August","September","October","November","December"];

    let html = `<h6>${months[month]} ${year}</h6><table>
    <tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr><tr>`;

    let day = 1;
    for(let i=0;i<6;i++){
        for(let j=0;j<7;j++){
            if(i===0 && j<firstDay){ html += "<td></td>"; }
            else if(day > daysInMonth){ html += "<td></td>"; }
            else{
                html += day === today ? `<td class="today">${day}</td>` : `<td>${day}</td>`;
                day++;
            }
        }
        html += "</tr><tr>";
    }
    html += "</tr></table>";
    calendar.innerHTML = html;
}

generateCalendar();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>