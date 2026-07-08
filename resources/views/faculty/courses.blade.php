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

/* Sidebar */
.sidebar{ position:fixed; top:0; left:-260px; width:260px; height:100%; background:#012147; color:white; padding:20px; transition:0.3s; z-index:3000; padding-top:100px; }
.sidebar.active{ left:0; }
.sidebar a{ display:block; color:white; text-decoration:none; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.2); }
.close-btn{ font-size:28px; cursor:pointer; position:absolute; top:10px; right:10px; }
.overlay{ position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); opacity:0; visibility:hidden; transition:0.3s; z-index:2500; }
.overlay.active{ opacity:1; visibility:visible; }

/* Page content */
.page-content{ transition:0.3s; margin-left:0; padding-top:120px; }
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

/* Hero */
.hero{ color:white; padding:100px 20px; text-align:center; background:linear-gradient(rgba(1,33,71,0.7),rgba(1,33,71,0.9)), url('{{ asset("images/ttmc.jpeg") }}') center/cover no-repeat; }
.hero h1{ font-size:42px; font-weight:bold; margin-bottom:15px; }

/* Courses */
#searchInput{ margin-bottom:20px; padding:8px 12px; width:100%; border-radius:6px; border:1px solid #ddd; }
.course-item .card{ cursor:pointer; height:250px; position: relative; border-radius:10px; overflow: hidden; color:white; transition: transform 0.3s, box-shadow 0.3s; }
.course-item .card:hover{ transform: scale(1.05); box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
.course-item .card .card-body{ position: absolute; bottom: 0; left:0; right:0; background: rgba(0,0,0,0.5); padding: 10px; }

/* Modal */
#levelModal{ display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:5000; }
#levelModalContent{ background:white; padding:20px; border-radius:10px; max-width:400px; width:90%; text-align:center; position:relative; }
#levelModalContent span{ position:absolute; top:10px; right:15px; cursor:pointer; font-size:20px; }
#modalCourseLevels a{ display:block; font-size:18px; font-weight:500; text-decoration:none; color:#ff9900; cursor:pointer; padding:4px 0; transition: color 0.2s; }
#modalCourseLevels a:hover{ color:#012147; }

/* Calendar */
#calendar{ text-align:center; font-size:14px; }
#calendar table{ width:100%; border-collapse:collapse; }
#calendar th{ color:#b22222; font-weight:bold; padding:5px 0; }
#calendar td{ padding:8px; border:1px solid #ddd; }
#calendar .today{ background:#b22222; color:white; border-radius:50%; font-weight:bold; }

/* Footer */
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
footer.shifted{
    margin-left:260px;
    transition:0.3s;
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

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <span class="close-btn" onclick="closeMenu()"><i class="bi bi-x"></i></span>
    <h4>Menu</h4>
    <a href="/">
    <i class="bi bi-house-door-fill me-1"></i> Home
</a>

<a href="/student-grade">
    <i class="bi bi-mortarboard-fill me-1"></i> Grade & GPA Scale
</a>
    
</div>
<div class="overlay" id="overlay" onclick="closeMenu()"></div>

<div class="page-content" id="pageContent">

<!-- Topbar -->
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

<!-- Header -->
<div class="header">
    <div class="logo-area">
        <img src="{{ asset('images/logo.png.jpeg') }}">
        <div>
            <div class="campus-name">TT Metro Campus</div>
            <div class="lms-name">Learning Management System</div>
        </div>
    </div>
</div>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <h1>{{ $faculty->name }} - Courses</h1>
        <p>All courses for this faculty.</p>
    </div>
</section>

<!-- Courses + Calendar -->
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
                    <div class="card" style="background-image: url('{{ asset($imgPath) }}'); background-size: cover; background-position: center;"
                         onclick="fetchLevels({{ $course->id }}, '{{ $course->name }}')">
                        <div class="card-body">
                            <h5>{{ $course->code }} - {{ $course->name }}</h5>
                            <span class="badge bg-success">{{ ucfirst($course->status) }}</span>
                        </div>
                    </div>
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

</div>

<!-- Level Modal -->
<div id="levelModal">
    <div id="levelModalContent">
        <span onclick="closeLevelModal()">&times;</span>
        <h4 id="modalCourseName"></h4>
        <div id="modalCourseLevels" class="d-flex flex-column align-items-start mt-3"></div>
    </div>
</div>

<!-- Footer -->
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle
function openMenu(){
    document.getElementById("sidebar").classList.add("active");
    document.getElementById("overlay").classList.add("active");
    document.getElementById("pageContent").classList.add("shifted");
    document.querySelector(".topbar").classList.add("shifted");
    document.querySelector(".header").classList.add("shifted");
    document.querySelector("footer").classList.add("shifted");
}
function closeMenu(){
    document.getElementById("sidebar").classList.remove("active");
    document.getElementById("overlay").classList.remove("active");
    document.getElementById("pageContent").classList.remove("shifted");
    document.querySelector(".topbar").classList.remove("shifted");
    document.querySelector(".header").classList.remove("shifted");
    document.querySelector("footer").classList.remove("shifted");
}

// Calendar
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
// Search filter
document.getElementById('searchInput').addEventListener('keyup', function(){
    const filter = this.value.toLowerCase();
    document.querySelectorAll('.course-item').forEach(function(card){
        const text = card.innerText.toLowerCase();
        card.style.display = text.includes(filter) ? '' : 'none';
    });
});
function fetchLevels(courseId, courseName){

    document.getElementById('modalCourseName').innerText = courseName;

    const levelsContainer=document.getElementById('modalCourseLevels');

    levelsContainer.innerHTML='';

    const levels=[
        {id:1,name:'Diploma'},
        {id:2,name:'HND'},
        {id:3,name:'Top-up'},
        {id:4,name:'Degree'}
    ];

    levels.forEach(level=>{

        let a=document.createElement('a');

        a.href="/login?course_id="+courseId+"&level_id="+level.id;

        a.innerHTML=level.name;

        levelsContainer.appendChild(a);

    });

    document.getElementById('levelModal').style.display='flex';

}

function closeLevelModal(){
    document.getElementById('levelModal').style.display='none';
}
</script>

</body>
</html>