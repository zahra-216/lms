<!DOCTYPE html>
<html>
<head>
    <title>My Courses</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>

        body{
            margin:0;
            font-family:'Segoe UI', sans-serif;
            background:linear-gradient(120deg,#eef2f7,#f8fafc);
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
            padding:160px 25px 80px;
            transition:0.3s;
        }

        .main.shift{
            margin-left:260px;
        }

        /* COURSE CARD */
        .course-card{
            background:white;
            border-radius:15px;
            padding:20px;
            box-shadow:0 10px 25px rgba(0,0,0,0.06);
            border-left:5px solid #3b82f6;
            margin-bottom:20px;
        }

        /* SEMESTER CARD */
        .semester-card{
            background:white;
            border-radius:15px;
            padding:18px;
            box-shadow:0 8px 20px rgba(0,0,0,0.05);
            transition:0.3s;
        }

        .semester-card:hover{
            transform:translateY(-4px);
        }

        .semester-title{
            font-weight:700;
            color:#0f172a;
            margin-bottom:10px;
        }

        /* SUBJECT */
        .subject-box{
            background:#f1f5f9;
            padding:10px 12px;
            border-radius:10px;
            margin-top:8px;
            cursor:pointer;
            transition:0.3s;
        }

        .subject-box:hover{
            background:#3b82f6;
            color:white;
            transform:scale(1.02);
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
/* SHIFT */
footer.shift{
    margin-left:260px;
}
/* RESPONSIVE */
@media (max-width:992px){
    .sidebar{width:220px;}
    .main.shift, footer.shift{margin-left:220px;}
}

@media (max-width:768px){

    .sidebar{
        width:80%;
        top:0;
        height:100vh;
        left:-100%;
    }

    .main.shift, footer.shift{
        margin-left:0 !important;
    }

    .footer-grid{
        grid-template-columns:1fr;
        text-align:center;
    }

    .footer-logo{
        justify-content:center;
    }

    .main{
        padding:150px 15px 80px;
    }}

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
    data-bs-toggle="dropdown">

             <ul class="dropdown-menu dropdown-menu-end">

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

    <!-- STUDENT CARD -->
    <div class="course-card">
        <h4>🎓 My Courses</h4>
        <p><b>Student:</b> {{ $student->name }}</p>
        <p><b>Course:</b> {{ $student->course->name ?? '' }}</p>
        <p><b>Level:</b> {{ $student->level->name ?? '' }}</p>
    </div>

    <!-- SEMESTERS -->
    <div class="row">

        @foreach($semesters as $semester)
        <div class="col-md-6 mb-3">
            <div class="semester-card">
                <div class="semester-title">📘 {{ $semester->name }}</div>
                <div id="subjectBox{{ $semester->id }}">Loading...</div>
            </div>
        </div>
        @endforeach

    </div>

</div>

<!-- FOOTER -->
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
    document.getElementById("main").classList.toggle("shift");
     document.getElementById("footer").classList.toggle("shift");
}

// load subjects
function loadSubjects(semesterId){

    fetch('/student/semester/' + semesterId + '/subjects')
    .then(res => res.json())
    .then(data => {

        let box = document.getElementById('subjectBox' + semesterId);

        let html = '';

        if(!data.subjects || data.subjects.length === 0){
            html = "<p class='text-muted'>No subjects</p>";
        } else {
            data.subjects.forEach(sub => {
                html += `
                    <div class="subject-box"
                        onclick="openSubject(${sub.id})">
                        ${sub.code ?? ''} ${sub.name}
                    </div>
                `;
            });
        }

        box.innerHTML = html;
    });
}

function openSubject(id){
    window.location.href = '/student/subject/' + id + '/notes';
}

document.addEventListener("DOMContentLoaded", function(){
    @foreach($semesters as $semester)
        loadSubjects({{ $semester->id }});
    @endforeach
});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>