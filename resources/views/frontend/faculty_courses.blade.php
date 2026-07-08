<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $faculty->name }} - Courses</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{ font-family:'Segoe UI', sans-serif; background:#f4f7ff; margin:0; overflow-x:hidden; }
.sidebar{ position:fixed; top:0; left:-260px; width:260px; height:100%; background:#012147; color:white; padding:20px; transition:0.3s; z-index:3000; padding-top:100px; }
.sidebar.active{ left:0; }
.sidebar a{ display:block; color:white; text-decoration:none; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.2); }
.close-btn{ font-size:28px; cursor:pointer; position:absolute; top:10px; right:10px; }
.overlay{ position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); opacity:0; visibility:hidden; transition:0.3s; z-index:2500; }
.overlay.active{ opacity:1; visibility:visible; }
.page-content{ transition:0.3s; margin-left:0; padding-top:120px; }
.page-content.shifted{ margin-left:260px; }
.topbar{ position:fixed; top:0; left:0; width:100%; background:#012147; color:white; padding:8px 20px; font-size:14px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; z-index:4000; transition:0.3s; }
.topbar-left{ display:flex; align-items:center; gap:20px; flex-wrap:wrap; }
.menu-icon{ font-size:26px; cursor:pointer; }
.topbar-right a{ color:white; text-decoration:none; margin-left:8px; }
.topbar.shifted{ left:260px; width:calc(100% - 260px); }
.header{ position:fixed; top:50px; left:0; width:100%; background:#f3f3f3; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; z-index:3500; transition:0.3s; }
.header.shifted{ left:260px; width:calc(100% - 260px); }
.logo-area{ display:flex; align-items:center; gap:12px; }
.logo-area img{ width:70px; }
.campus-name{ font-family:'Georgia', serif; font-size:24px; font-weight:bold; color:#012147; }
.lms-name{ font-family:'Georgia', serif; font-size:18px; color:#ff9900; }
.dashboard-btn{ color:#ff7a00; font-size:18px; text-decoration:none; }
.hero{ color:white; padding:100px 20px; text-align:center; background:linear-gradient(rgba(1,33,71,0.7),rgba(1,33,71,0.9)), url('{{ asset("images/ttmc.jpeg") }}') center/cover no-repeat; }
.hero h1{ font-size:42px; font-weight:bold; margin-bottom:15px; }
#searchInput{ margin-bottom:20px; padding:8px 12px; width:100%; border-radius:6px; border:1px solid #ddd; }
.course-item .card{ height:250px; position: relative; border-radius:10px; overflow: hidden; color:white; transition: transform 0.3s, box-shadow 0.3s; }
.course-item .card:hover{ transform: scale(1.05); box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
.course-item .card .card-body{ position: absolute; bottom: 0; left: 0; right:0; background: rgba(0,0,0,0.5); padding: 10px; }
#calendar{ text-align:center; font-size:14px; }
#calendar table{ width:100%; border-collapse:collapse; }
#calendar th{ color:#b22222; font-weight:bold; padding:5px 0; }
#calendar td{ padding:8px; border:1px solid #ddd; }
#calendar .today{ background:#b22222; color:white; border-radius:50%; }
footer{ background:#012147; color:white; margin-top:60px; }
.footer-container{ padding:50px 20px; }
.footer-title{ font-size:20px; font-weight:bold; margin-bottom:15px; border-bottom:2px solid #ff9900; display:inline-block; padding-bottom:5px; }
footer a{ color:#ddd; text-decoration:none; display:block; margin-bottom:8px; }
footer a:hover{ color:#ff9900; }
.footer-bottom{ background:#00152b; text-align:center; padding:12px; margin-top:20px; }
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <span class="close-btn" onclick="closeMenu()"><i class="bi bi-x"></i></span>
    <h4>Menu</h4>
    <a href="/">Home</a>
</div>
<div class="overlay" id="overlay" onclick="closeMenu()"></div>

<div class="page-content" id="pageContent">

<!-- TOPBAR -->
<div class="topbar">
    <div class="topbar-left">
        <i class="bi bi-list menu-icon" onclick="openMenu()"></i>
        <span><i class="bi bi-telephone"></i> Call : 011 4319 996 | 077 2270 348</span>
        <span><i class="bi bi-envelope"></i> Email : Info.ttmcml@gmail.com</span>
    </div>
    <div class="topbar-right">You are not logged in. <a href="#">(Log in)</a></div>
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

<!-- HERO -->
<section class="hero">
    <div class="container">
        <h1>{{ $faculty->name }} - Courses</h1>
        <p>All courses for this faculty.</p>
    </div>
</section>

<!-- COURSES -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-9">
            <h2>Available Courses</h2>
            <input type="text" id="searchInput" placeholder="Search courses by name or code...">
            @if($faculty->courses->count())
            <div class="row mt-4" id="coursesContainer">
                @foreach($faculty->courses as $course)
                @php
                    $imgPath = $course->image && file_exists(public_path('storage/courses/'.$course->image)) 
                               ? 'storage/courses/'.$course->image 
                               : 'https://via.placeholder.com/300x250';
                @endphp
                <div class="col-md-3 mb-3 course-item">
                    @if($course->level)
                        <a href="{{ route('faculty.courses.level', ['facultyId'=>$faculty->id, 'level'=>strtolower($course->level)]) }}" style="text-decoration:none;">
                            <div class="card" style="background-image: url('{{ asset($imgPath) }}'); background-size: cover; background-position: center;">
                                <div class="card-body">
                                    <h5>{{ $course->code }} - {{ $course->name }}</h5>
                                    <span class="badge bg-success">{{ ucfirst($course->status) }}</span>
                                    <span class="badge bg-warning text-dark">{{ strtoupper($course->level) }}</span>
                                </div>
                            </div>
                        </a>
                    @else
                        <div class="card" style="background-image: url('{{ asset($imgPath) }}'); background-size: cover; background-position: center;">
                            <div class="card-body">
                                <h5>{{ $course->code }} - {{ $course->name }}</h5>
                                <span class="badge bg-success">{{ ucfirst($course->status) }}</span>
                            </div>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p>No courses available for this faculty.</p>
            @endif
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header" style="color:#b22222;font-weight:bold;">Calendar</div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-container">
        <div class="row text-md-start text-center">
            <div class="col-md-4">
                <img src="{{ asset('images/logo.jpeg') }}" width="80">
                <div class="mt-3 campus-name text-white">TT Metro Campus</div>
                <div class="lms-name">Learning Management System</div>
            </div>
            <div class="col-md-4">
                <div class="footer-title">Quick Links</div>
                <a href="#"><i class="bi bi-globe"></i> www.techlinktechnology.com</a>
                <a href="#"><i class="bi bi-globe"></i> ttmetrocampus.com</a>
            </div>
            <div class="col-md-4">
                <div class="footer-title">Contact Us</div>
                <p><i class="bi bi-geo-alt"></i> TT METRO CAMPUS No 11 A1,Galle Road,Mount Lavinia, Sri Lanka</p>
                <p><i class="bi bi-telephone"></i> 011 4319 996 | 077 2270 348</p>
                <p><i class="bi bi-envelope"></i> Info.ttmcml@gmail.com</p>
            </div>
        </div>
    </div>
    <div class="footer-bottom">© 2026 TT Metro Campus LMS</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
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

// Calendar
function generateCalendar(){
    const today=new Date();
    const month=today.getMonth();
    const year=today.getFullYear();
    const firstDay=new Date(year,month,1).getDay();
    const lastDate=new Date(year,month+1,0).getDate();
    const monthNames=["January","February","March","April","May","June","July","August","September","October","November","December"];
    let html=`<table class="table table-sm"><thead>
    <tr><th colspan="7">${monthNames[month]} ${year}</th></tr>
    <tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th></tr>
    </thead><tbody><tr>`;
    let dayCount=0;
    for(let i=1;i<=firstDay;i++){ html+="<td></td>"; dayCount++; }
    for(let d=1;d<=lastDate;d++){
        dayCount++;
        html += (d===today.getDate() ? `<td class="today">${d}</td>` : `<td>${d}</td>`);
        if(dayCount%7===0) html+="</tr><tr>";
    }
    html+="</tr></tbody></table>";
    document.getElementById("calendar").innerHTML=html;
}
generateCalendar();

// Search filter
document.getElementById('searchInput').addEventListener('keyup', function(){
    const filter = this.value.toLowerCase();
    document.querySelectorAll('.course-item').forEach(function(card){
        const text = card.innerText.toLowerCase();
        card.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>
</body>
</html>






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
    font-family:'Segoe UI',sans-serif;
    background:#f4f7ff;
    margin:0;
    overflow-x:hidden;
}



/* ================= SIDEBAR ================= */

.sidebar{

    position:fixed;
    top:0;
    left:-260px;

    width:260px;
    height:100vh;

    background:#012147;
    color:white;

    padding:20px;

    transition:.3s;

    z-index:5000;

}


.sidebar.active{

    left:0;

}


.sidebar a{

    display:block;

    color:white;

    text-decoration:none;

    padding:12px 0;

    border-bottom:1px solid rgba(255,255,255,.2);

}


.close-btn{

    font-size:28px;

    cursor:pointer;

    float:right;

}




/* ================= OVERLAY ================= */


.overlay{

    position:fixed;

    top:0;
    left:0;

    width:100%;
    height:100%;

    background:rgba(0,0,0,.4);

    opacity:0;

    visibility:hidden;

    transition:.3s;

    z-index:4000;

}


.overlay.active{

    opacity:1;

    visibility:visible;

}





/* ================= PAGE ================= */


.page-content{

    margin-left:0;

    transition:.3s;

}


.page-content.shifted{

    margin-left:260px;

}





/* ================= TOPBAR ================= */


.topbar{

    position:fixed;

    top:0;

    left:0;

    width:100%;


    background:#012147;

    color:white;

    padding:10px 20px;


    display:flex;

    justify-content:space-between;

    align-items:center;


    z-index:4500;


    transition:.3s;

}



.topbar-left{

    display:flex;

    align-items:center;

    gap:20px;

    flex-wrap:wrap;

}



.menu-icon{

    font-size:26px;

    cursor:pointer;

}



.topbar-right a{

    color:white;

    text-decoration:none;

}



.topbar.shifted{

    left:260px;

    width:calc(100% - 260px);

}





/* ================= HEADER ================= */


.header{

    position:fixed;

    top:50px;

    left:0;

    width:100%;


    background:#f3f3f3;


    padding:15px 30px;


    display:flex;

    justify-content:space-between;

    align-items:center;


    z-index:3500;


    transition:.3s;

}




.header.shifted{

    left:260px;

    width:calc(100% - 260px);

}



.logo-area{

    display:flex;

    align-items:center;

    gap:12px;

}



.logo-area img{

    width:70px;

    height:70px;

    object-fit:contain;

}




.campus-name{

    font-family:Georgia;

    font-size:24px;

    font-weight:bold;

    color:#012147;

}




.lms-name{

    font-family:Georgia;

    font-size:18px;

    color:#012147;

}





.dashboard-btn{


    background:#012147;

    color:white;


    padding:8px 18px;


    border-radius:8px;


    text-decoration:none;


    display:flex;

    align-items:center;

    gap:5px;


}





.dashboard-btn:hover{

    background:#0d6efd;

    color:white;

}




/* ================= HERO ================= */


.hero{


    color:white;


    padding:200px 20px;


    text-align:center;



    background:


    linear-gradient(
    rgba(1,33,71,.7),
    rgba(1,33,71,.9)
    ),


    url("{{ asset('images/ttmc.jpeg') }}")

    center/cover no-repeat;


}




.hero h1{

    font-size:42px;

    font-weight:bold;

}



.hero p{

    font-size:18px;

}





/* ================= FACULTY ================= */


.faculty-card{


    display:flex;


    flex-direction:column;


    align-items:center;


    justify-content:center;



    background:white;



    border-radius:12px;



    padding:20px;



    height:220px;



    box-shadow:0 5px 15px rgba(0,0,0,.08);



}




.faculty-card img{


    width:100px;

    height:100px;


    border-radius:50%;


    object-fit:cover;


    border:3px solid #012147;


}





.swiper-slide{


    width:230px!important;


    display:flex;


    justify-content:center;


}






/* ================= FOOTER ================= */


footer{


background:#012147;

color:white;

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



.footer-box a{


display:block;

color:#cbd5e1;

text-decoration:none;

margin:10px 0;


}



.footer-logo{


display:flex;

align-items:center;

gap:12px;


}



.footer-logo img{


width:50px;

height:50px;


}




.footer-bottom{


background:#050a14;

text-align:center;

padding:15px;


}





/* ================= TABLET ================= */


@media(max-width:992px){


.sidebar{

width:220px;

left:-220px;

}



.page-content.shifted{

margin-left:220px;

}



.topbar.shifted,
.header.shifted{

left:220px;

width:calc(100% - 220px);

}


}





/* ================= MOBILE ================= */


@media(max-width:768px){



.topbar{


flex-direction:column;


align-items:flex-start;


gap:8px;


padding:8px 12px;


}




.topbar-left{


flex-direction:column;


gap:5px;


}




.sidebar{


width:80%;


left:-100%;


}



.page-content.shifted{


margin-left:0!important;


}



.topbar.shifted,
.header.shifted{


left:0;


width:100%;


}




/* HEADER */


.header{


top:95px;


padding:12px;


flex-direction:column;


gap:12px;


}



.logo-area{


width:100%;


justify-content:center;


}



.logo-area img{


width:45px;


height:45px;


}



.campus-name{


font-size:16px;


}



.lms-name{


font-size:12px;


}



.dashboard-btn{


width:90%;


justify-content:center;


}




/* HERO */


.hero{


padding:240px 15px 80px;


}



.hero h1{


font-size:28px;


}



.hero p{


font-size:15px;


}




/* FACULTY */


.swiper-slide{


width:100%!important;


}



.faculty-card{


width:90%;


height:200px;


margin:auto;


}




.footer-grid{


grid-template-columns:1fr;


}



.footer-container{


padding:35px 15px;


}



}






/* ================= SMALL MOBILE ================= */


@media(max-width:768px){


/* TOPBAR FIX */

.topbar{

height:auto;
padding:8px 12px;

}


.topbar-left{

width:100%;

display:flex;

flex-direction:row;

justify-content:space-between;

font-size:13px;

}


.menu-icon{

font-size:24px;

}



/* hide login space reduce */

.topbar-right{

font-size:13px;

}




/* HEADER FIX */


.header{

top:110px;

background:#f3f3f3;

height:auto;

padding:12px;


}



.logo-area{

display:flex;

}


.logo-area img{

display:block;

width:45px;

height:45px;

}



.campus-name{

font-size:17px;

}



.lms-name{

font-size:12px;

}



.dashboard-btn{

margin-top:5px;

width:80%;

}



/* HERO */


.hero{

padding-top:260px;

padding-bottom:70px;

}



.hero h1{

font-size:26px;

}



.hero p{

font-size:14px;

}




/* FACULTY */


.swiper-slide{

width:100%!important;

}


.faculty-card{

width:90%;

margin:auto;

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
        © 2026 TT Metro Campus LMS | All Rights Reserved
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