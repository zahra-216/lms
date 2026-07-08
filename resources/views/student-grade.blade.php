<!DOCTYPE html>
<html>
<head>
    <title>Grade Scale</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>

<style>
body{
    font-family:'Segoe UI', sans-serif;
    background:#f4f7ff;
    margin:0;
}
/* SIDEBAR */
.sidebar{ position:fixed; top:0; left:-260px; width:260px; height:100%; background:#012147; color:white; padding:20px; transition:0.3s; z-index:2000; }
.sidebar.active{ left:0; }
.sidebar a{ display:block; color:white; text-decoration:none; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.2); }
.close-btn{ font-size:28px; cursor:pointer; float:right; }
/* OVERLAY */
.overlay{ position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); opacity:0; visibility:hidden; transition:0.3s; z-index:1500; }
.overlay.active{ opacity:1; visibility:visible; }
/* PAGE CONTENT */
.page-content{ transition: margin-left 0.3s; margin-left:0; }
.page-content.shifted{ margin-left:260px; }
/* Topbar */
.topbar{ position:fixed; top:0; left:0; width:100%; background:#012147; color:white; padding:8px 20px; font-size:14px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; z-index:4000; transition:0.3s; }
.topbar-left{ display:flex; align-items:center; gap:20px; flex-wrap:wrap; }
.menu-icon{ font-size:26px; cursor:pointer; }
.topbar-right a{ color:white; text-decoration:none; margin-left:8px; }
.topbar.shifted{ left:260px; width:calc(100% - 260px); }

/* Header */
.header{ position:fixed; top:50px; left:0; width:100%; background:#f3f3f3; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; z-index:3500; transition:0.3s; }
.header.shifted{ left:260px; width:calc(100% - 260px); }
.logo-area{ display:flex; align-items:center; gap:12px; }
.logo-area img{ width:70px; }
.campus-name{ font-family:'Georgia', serif; font-size:24px; font-weight:bold; color:#012147; }
.lms-name{ font-family:'Georgia', serif; font-size:18px; color:#012147; }

/* FOOTER */
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
/* ===== CONTAINER ===== */
.container{
    margin-top:180px;
}

/* ===== TITLE ===== */
.container h3{
    font-weight:600;
    color:#1e293b;
}

/* ===== TABLE DESIGN ===== */
.table{
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
}

/* HEADER */
.table thead{
    background:linear-gradient(135deg,#2563eb,#1e3a8a);
    color:#fff;
}

.table thead th{
    padding:14px;
    font-size:15px;
    letter-spacing:0.5px;
}

/* BODY */
.table tbody td{
    padding:12px;
    font-size:14px;
    color:#334155;
}

/* ROW HOVER */
.table tbody tr:hover{
    background:#f1f5f9;
    transform:scale(1.01);
    transition:0.2s;
}

/* STRIPED EFFECT IMPROVE */
.table-striped tbody tr:nth-of-type(odd){
    background-color:#f8fafc;
}

/* BORDER */
.table-bordered th,
.table-bordered td{
    border:1px solid #e2e8f0;
}

/* ===== GRADE COLORS ===== */
.table tbody tr:nth-child(1),
.table tbody tr:nth-child(2){
    background:#dcfce7; /* A grade green */
}

.table tbody tr:nth-child(3),
.table tbody tr:nth-child(4){
    background:#e0f2fe; /* B grade blue */
}

.table tbody tr:nth-child(5),
.table tbody tr:nth-child(6){
    background:#fef9c3; /* C grade yellow */
}

.table tbody tr:nth-last-child(1),
.table tbody tr:nth-last-child(2){
    background:#fee2e2; /* Fail red */
}

/* ===== MOBILE ===== */
@media (max-width:768px){
    .table thead{
        display:none;
    }

    .table, .table tbody, .table tr, .table td{
        display:block;
        width:100%;
    }

    .table tr{
        margin-bottom:15px;
        background:#fff;
        border-radius:10px;
        padding:10px;
        box-shadow:0 4px 10px rgba(0,0,0,0.05);
    }

    .table td{
        text-align:right;
        padding-left:50%;
        position:relative;
    }

    .table td::before{
        content:attr(data-label);
        position:absolute;
        left:15px;
        font-weight:bold;
        color:#1e293b;
    }
}

    </style>
</head>

<body>
    <!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <span class="close-btn" onclick="closeMenu()"><i class="bi bi-x"></i></span>
    <h4 class="mt-4">Menu</h4>
   <a href="/">
    <i class="bi bi-house-door-fill me-1"></i> Home
</a>

<a href="/student-grade">
    <i class="bi bi-mortarboard-fill me-1"></i> Grade & GPA Scale
</a>
</div>

<div class="overlay" id="overlay" onclick="closeMenu()"></div>

<!-- PAGE CONTENT -->
<div class="page-content" id="pageContent">

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-left">
            <i class="bi bi-list menu-icon" onclick="openMenu()"></i>
            <span><i class="bi bi-telephone"></i> Call : 011 4319 996 | 077 2270 348</span>
            <span><i class="bi bi-envelope"></i> Email : Info.ttmcml@gmail.com</span>
        </div>
        <div class="topbar-right">
            You are not logged in.
            <a href="#">(Log in)</a>
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

<div class="container">

    <h3 class="mb-4">🎓 Common Grade & GPA Scale</h3>

    <table class="table table-bordered table-striped bg-white">

        <thead class="table-dark">
            <tr>
                <th>Marks (%)</th>
                <th>Grade</th>
                <th>GPA</th>
                <th>Classification</th>
            </tr>
        </thead>

        <tbody>

            <tr><td>85 - 100</td><td>A+</td><td>4.0</td><td>Distinction</td></tr>
            <tr><td>75 - 84</td><td>A</td><td>4.0</td><td>Distinction</td></tr>
            <tr><td>70 - 74</td><td>A-</td><td>3.7</td><td>Merit</td></tr>
            <tr><td>65 - 69</td><td>B+</td><td>3.3</td><td>Merit</td></tr>
            <tr><td>60 - 64</td><td>B</td><td>3.0</td><td>Merit</td></tr>
            <tr><td>55 - 59</td><td>B-</td><td>2.7</td><td>Pass</td></tr>
            <tr><td>50 - 54</td><td>C+</td><td>2.3</td><td>Pass</td></tr>
            <tr><td>45 - 49</td><td>C</td><td>2.0</td><td>Pass</td></tr>
            <tr><td>40 - 44</td><td>C-</td><td>1.7</td><td>Fail</td></tr>
            <tr><td>35 - 39</td><td>D+</td><td>1.3</td><td>Fail</td></tr>
            <tr><td>30 - 34</td><td>D</td><td>1.0</td><td>Fail</td></tr>
            <tr><td>0 - 29</td><td>F</td><td>0.0</td><td>Fail</td></tr>

        </tbody>

    </table>
   

</div> <!-- END PAGE CONTENT -->
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





</div>
<script>
// Sidebar toggle
function openMenu(){
    document.getElementById("sidebar").classList.add("active");
    document.getElementById("overlay").classList.add("active");
    document.getElementById("pageContent").classList.add("shifted");
    document.querySelector(".topbar").classList.add("shifted");
    document.querySelector(".header").classList.add("shifted");
}
function closeMenu(){
    document.getElementById("sidebar").classList.remove("active");
    document.getElementById("overlay").classList.remove("active");
    document.getElementById("pageContent").classList.remove("shifted");
    document.querySelector(".topbar").classList.remove("shifted");
    document.querySelector(".header").classList.remove("shifted");
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
</body>
</html>