<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Notes</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

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
    margin-left:0;
    padding:150px 20px 20px;
    transition:0.3s;
}

.main.shift{
    margin-left:240px;
}

/* SHIFT EFFECT */
.shift{
    margin-left:240px !important;
}

/* CARD */
.card-box{
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 3px 12px rgba(0,0,0,0.06);
    margin-bottom:15px;
}

/* SUBJECT */
.subject-title{
    font-size:24px;
    font-weight:bold;
    color:#012147;
}

.badge-custom{
    background:#012147;
    color:white;
    padding:6px 12px;
    border-radius:6px;
    display:inline-block;
    margin-top:8px;
}

/* NOTES */
.note-item{
    display:flex;
    align-items:center;
    padding:12px;
    background:#f8faff;
    border-radius:8px;
    margin-bottom:8px;
    border-left:4px solid #012147;
    transition:0.2s;
}

.note-item:hover{
    background:#012147;
    color:white;
}
/* ================= ASSIGNMENT CARD ================= */
.assignment-btn{
    background: linear-gradient(135deg,#3b82f6,#1d4ed8);
    color:white;
    padding:10px 18px;
    border-radius:12px;
    font-weight:600;
    display:inline-flex;
    align-items:center;
    gap:8px;
    transition:0.3s;
    box-shadow:0 6px 15px rgba(59,130,246,0.3);
    text-decoration:none;
}

.assignment-btn:hover{
    transform: translateY(-3px);
    box-shadow:0 10px 20px rgba(59,130,246,0.4);
    color:white;
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
.subject-card{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:15px;
}

.subject-top{
    width:100%;
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:15px;
}

/* Left side (title + code) */
.subject-title{
    font-size:24px;
    font-weight:700;
    color:#012147;
}

.badge-custom{
    display:inline-block;
    margin-top:6px;
    background:#012147;
    color:#fff;
    padding:5px 12px;
    border-radius:6px;
    font-size:13px;
}

/* Button */
.assignment-btn{
    background:linear-gradient(135deg,#3b82f6,#1d4ed8);
    color:white;
    padding:10px 16px;
    border-radius:10px;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:6px;
    text-decoration:none;
    transition:0.3s;
}

.assignment-btn:hover{
    transform:translateY(-2px);
}
@media(max-width:768px){
    .subject-top{
        flex-direction:column;
        align-items:flex-start;
    }

    .assignment-btn{
        width:100%;
        justify-content:center;
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
    <li>
    <a class="dropdown-item" href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
</li>
                <li><a class="dropdown-item" href="{{ route('student.profile') }}">
    <i class="bi bi-person"></i> Profile
</a></li>
                <li>
    <a class="dropdown-item" href="{{ route('student.grades') }}">
        <i class="bi bi-bar-chart"></i> Grades
    </a>
</li>
         
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

    <div class="card-box subject-card">

    <div class="subject-top">
        <div>
            <div class="subject-title">{{ $subject->name }}</div>
            <div class="badge-custom">{{ $subject->code }}</div>
        </div>

        <!-- BUTTON aligned right -->
        <a href="{{ route('student.subject.assignments', $subject->id) }}"
           class="btn assignment-btn">
            <i class="bi bi-journal-text"></i>
            View Assignments
        </a>
    </div>

</div>
   
    <div class="card-box">
        <h5>📚 Notes</h5>

        @if($notes->count() > 0)
            @foreach($notes as $note)
                <a href="{{ route('student.note.download', $note->id) }}" class="text-decoration-none text-dark">

                    <div class="note-item">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        <b>{{ $note->title }}</b>
                    </div>

                </a>
            @endforeach
        @else
            <p class="text-danger">No notes found</p>
        @endif
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

    if (window.innerWidth > 768) {
        document.getElementById("main").classList.toggle("shift");
        document.getElementById("footer").classList.toggle("shift");
    }

    document.addEventListener("click", function(e){
    let sidebar = document.getElementById("sidebar");
    let btn = document.querySelector(".bi-list");

    if(window.innerWidth <= 768){
        if(!sidebar.contains(e.target) && !btn.contains(e.target)){
            sidebar.classList.remove("show");
        }
    }
});
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>