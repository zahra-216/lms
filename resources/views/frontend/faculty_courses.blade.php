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
.page-content{ transition:0.3s; margin-left:0; padding-top:90px; }
.page-content.shifted{ margin-left:260px; }
.topbar{ position:fixed; top:0; left:0; width:100%; background:#012147; color:white; padding:14px 24px; display:flex; justify-content:space-between; align-items:center; z-index:4000; }
.topbar h1{ font-size:20px; margin:0; font-weight:600; }
.topbar .actions{ display:flex; align-items:center; gap:12px; }
.logout-btn{ background:#ff4d4f; border:none; color:#fff; padding:10px 16px; border-radius:10px; cursor:pointer; }
.logout-btn:hover{ opacity:.9; }
.menu-icon{ font-size:26px; cursor:pointer; }
.topbar-right a{ color:white; text-decoration:none; }
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
    <div style="display:flex; align-items:center; gap:12px;">
        <i class="bi bi-list menu-icon" onclick="openMenu()"></i>
        <h1>{{ $faculty->name }} Faculty</h1>
    </div>
    <div class="actions">
        @if(auth('lecturer')->check())
            <span>Welcome, {{ auth('lecturer')->user()->name }}</span>
            <form action="{{ route('lecturer.logout') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        @else
            <a href="{{ route('lecturer.login') }}" class="logout-btn">Lecturer Login</a>
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
    <div class="footer-bottom">Â© 2026 TT Metro Campus LMS</div>
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

