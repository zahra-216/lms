<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TTMC LMS</title>

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
/* HERO */
.hero{ color:white; padding:200px 20px; text-align:center; background:linear-gradient(rgba(1,33,71,0.7),rgba(1,33,71,0.9)), url("{{ asset('images/ttmc.jpeg') }}") center/cover no-repeat; }
.hero h1{ font-size:42px; font-weight:bold; margin-bottom:15px; }
.hero p{ font-size:18px; }
/* FACULTY */
.faculty-card{ display:flex; flex-direction:column; align-items:center; justify-content:center; background:white; border-radius:12px; padding:20px; height:220px; box-shadow:0 5px 15px rgba(0,0,0,0.08); cursor:pointer; transition:0.3s; }
.faculty-card:hover{ transform:translateY(-5px); box-shadow:0 10px 20px rgba(0,0,0,0.15);}
.faculty-card img{ width:100px; height:100px; border-radius:50%; object-fit:cover; margin-bottom:10px; border:3px solid #012147; }
.swiper-slide{ display:flex; justify-content:center; width:230px !important; }
.swiper-wrapper{ transition-timing-function:linear !important; }
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

/* ==============================
   FINAL MOBILE RESPONSIVE FIX
============================== */

*{
    box-sizing:border-box;
}

body{
    overflow-x:hidden;
}


/* MOBILE */
@media(max-width:768px){


/* TOP BAR */

.topbar{

    position:fixed;
    height:auto;
    min-height:45px;
    padding:8px 10px;
    flex-direction:row;

}


.topbar-left{

    gap:8px;

}


.topbar-left span{

    display:none;

}


.menu-icon{

    font-size:28px;

}


/* LOGIN AREA */

.topbar-right{

    font-size:12px;

}



/* HEADER */

.header{

    top:45px;
    height:75px;
    padding:10px 12px;

}



.logo-area{

    gap:8px;

}


.logo-area img{

    width:45px;
    height:45px;

}



.campus-name{

    font-size:16px;

}


.lms-name{

    font-size:11px;

}


/* DASHBOARD BUTTON */

.dashboard-btn{

    font-size:12px;

    padding:6px 8px;

}



/* REMOVE SHIFT */

.page-content.shifted{

    margin-left:0 !important;

}


.topbar.shifted{

    left:0 !important;

    width:100% !important;

}


.header.shifted{

    left:0 !important;

    width:100% !important;

}



/* SIDEBAR */

.sidebar{
 position:fixed;
 top:0;
 left:-260px;
 width:260px;
 height:100%;
 background:#012147;
 color:white;
 padding:20px;
 transition:0.3s;
 z-index:5000;
}



.sidebar.active{

    left:0;

}



/* HERO */

.hero{

    padding:160px 15px 80px;

}



.hero h1{

    font-size:30px;

}


.hero p{

    font-size:15px;

}



/* FACULTY */

.faculty-card{

    height:210px;

}


.swiper-slide{

    width:230px !important;

}



/* FOOTER */

.footer-container{

    padding:40px 15px;

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


/* SMALL PHONE */

@media(max-width:480px){


.header{

    height:70px;

}


.campus-name{

    font-size:14px;

}


.lms-name{

    display:none;

}



.dashboard-btn{

    display:none;

}



.hero h1{

    font-size:25px;

}


.hero p{

    font-size:14px;

}



.sidebar{

    width:100%;

}


.footer-desc{

    font-size:13px;

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

@if(session()->has('student_id'))

    Welcome {{ session('student_name') }}

    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
        @csrf
        <button class="btn btn-link text-white p-0">Logout</button>
    </form>

@else

    You are not logged in.
    <a href="{{ route('login') }}">(Log in)</a>

@endif

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
        <a href="{{ route('admin.login') }}" class="dashboard-btn">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </div>

    <!-- HERO -->
    <section class="hero">
        <div class="container">
            <h1>Start Your Learning Journey</h1>
            <p>Find courses, modules, and notes easily with TTMC LMS</p>
        </div>
    </section>

    <!-- FACULTY -->
    <div class="container mt-5">
        <h3 class="text-center mb-4" style="color:#012147;">Our Faculty</h3>
        <div class="swiper facultySwiper">
            <div class="swiper-wrapper">
                @foreach($faculties as $faculty)
                @php
                    $imgPath = 'storage/faculty/'.$faculty->image;
                    $img = ($faculty->image && file_exists(public_path($imgPath))) ? asset($imgPath) : 'https://picsum.photos/200';
                @endphp
                <div class="swiper-slide">
                    <!-- Make the card clickable -->
                    <a href="{{ route('faculty.courses', $faculty->id) }}" style="text-decoration:none;">
                        <div class="faculty-card">
                            <img src="{{ $img }}" alt="{{ $faculty->name }}">
                            <h5 style="color:#012147;">{{ $faculty->name }}</h5>
                        </div>
                    </a>
                </div>
                @endforeach

                <!-- Duplicate for continuous scroll -->
                @foreach($faculties as $faculty)
                @php
                    $imgPath = 'storage/faculty/'.$faculty->image;
                    $img = ($faculty->image && file_exists(public_path($imgPath))) ? asset($imgPath) : 'https://picsum.photos/200';
                @endphp
                <div class="swiper-slide">
                    <a href="{{ route('faculty.courses', $faculty->id) }}" style="text-decoration:none;">
                        <div class="faculty-card">
                            <img src="{{ $img }}" alt="{{ $faculty->name }}">
                            <h5 style="color:#012147;">{{ $faculty->name }}</h5>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
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
                <a href="https://ttmetrocampus.com/"><i class="bi bi-globe"></i> www.ttmetrocampus.com</a>
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
        © 2026 TT Metro Campus LMS | 2026
    </div>
</footer>

</div> <!-- END PAGE CONTENT -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

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
// Continuous faculty carousel
var swiper = new Swiper(".facultySwiper", {
    slidesPerView: 'auto',
    spaceBetween: 20,
    loop: true,
    speed: 6000,
    autoplay: { delay: 0, disableOnInteraction: false },
    allowTouchMove: false
});
</script>

</body>
</html>