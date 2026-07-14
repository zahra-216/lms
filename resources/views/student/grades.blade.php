<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Subjects</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>

/* ================================
   RESET
================================ */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(120deg,#eef2f7,#f8fafc);
    overflow-x:hidden;

}


/* ================================
   TOP BAR
================================ */


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

    width:100%;

    z-index:3000;

}



.left-top{

    display:flex;

    align-items:center;

    gap:10px;

}



.icon-btn{

    font-size:22px;

    cursor:pointer;

}



.topbar-profile{


    width:42px;

    height:42px;


    border-radius:50%;


    object-fit:cover;


    border:2px solid white;


}




/* ================================
 HEADER
================================ */


.header{


    height:70px;


    background:white;


    position:fixed;


    top:60px;


    left:0;


    width:100%;


    display:flex;


    align-items:center;


    padding-left:20px;


    box-shadow:0 3px 15px rgba(0,0,0,.1);


    z-index:2500;


    transition:.3s;


}



.header.shift{


    margin-left:260px;

    width:calc(100% - 260px);


}



.logo-area{


    display:flex;


    align-items:center;


    gap:12px;


}



.logo-area img{


    width:45px;

    height:45px;


    border-radius:10px;


}



.campus-name{


    font-weight:700;

    font-size:18px;


}



.lms-name{


    font-size:13px;

    color:#64748b;


}





/* ================================
 SIDEBAR
================================ */


.sidebar{


    width:260px;


    height:100vh;


    background:#012147;


    position:fixed;


    top:130px;


    left:-260px;


    padding-top:15px;


    color:white;


    transition:.3s;


    z-index:4000;


}



.sidebar.show{


    left:0;


}



.sidebar-header{


    display:flex;


    justify-content:flex-end;


    padding:10px 15px;


}



.close-btn{


    cursor:pointer;

    font-size:22px;


}



.sidebar a{


    display:flex;


    align-items:center;


    gap:12px;


    padding:13px 20px;


    color:#dbeafe;


    text-decoration:none;


    transition:.3s;


}



.sidebar a:hover{


    background:rgba(255,255,255,.1);


    padding-left:30px;


    color:white;


}




/* ================================
 MAIN
================================ */


.main{


    padding:160px 25px 40px;


    transition:.3s;


}



.main.shift{


    margin-left:260px;


}




/* ================================
 CARD
================================ */


.course-card{


    background:white;


    padding:20px;


    border-radius:15px;


    border-left:5px solid #2563eb;


    box-shadow:0 10px 25px rgba(0,0,0,.08);


    margin-bottom:25px;


}




/* TABLE */


.table{


    background:white;


    border-radius:12px;


    overflow:hidden;


}




/* ================================
 FOOTER
================================ */


footer{


    background:#012147;


    color:#e2e8f0;


    transition:.3s;


}



footer.shift{


    margin-left:260px;


}



.footer-container{


    max-width:1200px;


    margin:auto;


    padding:50px 25px;


}




.footer-grid{


    display:grid;


    grid-template-columns:repeat(3,1fr);


    gap:30px;


}



.footer-box h5{


    color:white;


    border-left:4px solid #3b82f6;


    padding-left:10px;


    margin-bottom:15px;


}



.footer-box a{


    display:block;


    color:#cbd5e1;


    text-decoration:none;


    margin-bottom:10px;


}



.footer-box a:hover{


    color:white;


}



.footer-logo{


    display:flex;


    align-items:center;


    gap:12px;


}



.footer-logo img{


    width:50px;


    height:50px;


    border-radius:10px;


}



.footer-desc{


    color:#cbd5e1;


    line-height:1.6;


}



.footer-bottom{


    background:#050a14;


    padding:15px;


    text-align:center;


}



/* =================================
 TABLET VIEW
================================= */


@media(max-width:992px){


.sidebar{


    width:220px;


    left:-220px;


}



.main.shift{


    margin-left:220px;


}



.header.shift,


footer.shift{


    margin-left:220px;


    width:calc(100% - 220px);


}



.footer-grid{


    grid-template-columns:repeat(2,1fr);


}



}




/* =================================
 MOBILE VIEW
================================= */


@media(max-width:768px){



.topbar{


    height:55px;


    padding:0 10px;


}



.topbar .small{


    display:none;


}




.header{


    top:55px;


    height:70px;


    padding-left:12px;


}



.header.shift{


    margin-left:0;


    width:100%;


}




.logo-area img{


    width:40px;


    height:40px;


}




.sidebar{


    top:0;


    left:-100%;


    width:80%;


    max-width:280px;


    height:100vh;


}



.sidebar.show{


    left:0;


}



.main{


    padding:140px 10px 30px;


}



.main.shift{


    margin-left:0;


}



footer.shift{


    margin-left:0;


    width:100%;


}



.footer-grid{


    grid-template-columns:1fr;


    text-align:center;


}




.footer-box h5{


    border-left:none;


    border-bottom:2px solid #3b82f6;


    padding-bottom:8px;


}



}





/* =================================
 SMALL PHONE
================================= */


@media(max-width:480px){



.campus-name{


    font-size:14px;


}



.lms-name{


    display:none;


}



.sidebar{


    width:100%;


}



.course-card{


    padding:15px;


}



.table{


    font-size:14px;


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

    <!-- STUDENT INFO -->
    <div class="course-card">
        <h4>📚 My Subjects</h4>
        <p><b>Name:</b> {{ $student->name }}</p>
        <p><b>Course:</b> {{ $student->course->name ?? '' }}</p>
    </div>

    <div class="container mt-4">

<h3>📚 Courses I am Taking</h3>

<table class="table table-bordered table-hover mt-3">

<thead class="table-dark">
<tr>
    <th>Subject</th>
    <th>Final Grade</th>
</tr>
</thead>

<tbody>

@foreach($subjects as $subject)

@php
$total = 0;
$count = 0;

foreach($subject->assignments as $a){
    $m = $a->marks->first();
    if($m){
        $total += $m->marks;
        $count++;
    }
}

$avg = $count ? $total / $count : 0;

$grade = match(true){
    $avg >= 80 => 'A',
    $avg >= 60 => 'B',
    $avg >= 40 => 'C',
    default => 'F'
};
@endphp

<tr>

    <td>
        <a href="{{ route('student.subject.grades', $subject->id) }}"
           style="text-decoration:none; font-weight:600;">
            📘 {{ $subject->name }}
        </a>
    </td>

    <td>
        <span class="badge bg-primary">
            {{ $grade }}
        </span>
    </td>

</tr>

@endforeach

</tbody>
</table>

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
    document.getElementById('sidebar').classList.toggle('show');
    document.getElementById('main').classList.toggle('shift');
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>