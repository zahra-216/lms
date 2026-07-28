<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $faculty->name }} - Courses</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
*{ box-sizing:border-box; }
body{ font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; overflow-x:hidden; }

/* Sidebar */
.sidebar{ position:fixed; top:0; left:-260px; width:260px; height:100%; background:#012147; color:white; padding:20px; transition:0.3s; z-index:5000; padding-top:100px; box-shadow: 4px 0 20px rgba(0,0,0,0.2); }
.sidebar.active{ left:0; }
.sidebar h4{ font-weight:700; margin-bottom:20px; }
.sidebar a{ display:flex; align-items:center; gap:8px; color:rgba(255,255,255,0.85); text-decoration:none; padding:12px 8px; border-radius:10px; transition:0.2s; margin-bottom:4px; }
.sidebar a:hover{ background:rgba(255,255,255,0.1); color:#fff; }
.close-btn{ font-size:28px; cursor:pointer; position:absolute; top:14px; right:16px; color:#fff; }
.overlay{ position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.45); opacity:0; visibility:hidden; transition:0.3s; z-index:2500; }
.overlay.active{ opacity:1; visibility:visible; }

/* Page content */
.page-content{ transition:0.3s; margin-left:0; padding-top:90px; }
.page-content.shifted{ margin-left:260px; }

/* Topbar */
.topbar{ position:fixed; top:0; left:0; width:100%; background:#012147; color:white; padding:14px 24px; display:flex; justify-content:space-between; align-items:center; z-index:4000; box-shadow:0 2px 12px rgba(0,0,0,0.15); }
.topbar h1{ font-size:19px; margin:0; font-weight:700; }
.topbar .actions{ display:flex; align-items:center; gap:12px; font-size:14px; }
.logout-btn{ background:#ef4444; border:none; color:#fff; padding:9px 16px; border-radius:10px; cursor:pointer; font-size:14px; transition:0.2s; }
.logout-btn:hover{ background:#dc2626; }
.menu-icon{ font-size:26px; cursor:pointer; }
.topbar.shifted{ left:260px; width:calc(100% - 260px); }

/* Header */
.header{ position:fixed; top:53px; left:0; width:100%; background:#fff; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; z-index:3500; transition:0.3s; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
.header.shifted{ left:260px; width:calc(100% - 260px); }
.logo-area{ display:flex; align-items:center; gap:12px; }
.logo-area img{ width:56px; border-radius:10px; }
.campus-name{ font-family:'Georgia', serif; font-size:22px; font-weight:bold; color:#012147; }
.lms-name{ font-family:'Georgia', serif; font-size:15px; color:#64748b; }

/* Hero */
.hero{ color:white; padding:120px 20px 70px; text-align:center; background:linear-gradient(120deg, rgba(1,33,71,.92), rgba(30,58,110,.88)), url('{{ asset("images/ttmc.jpeg") }}') center/cover no-repeat; }
.hero h1{ font-size:38px; font-weight:700; margin-bottom:10px; }
.hero p{ font-size:16px; opacity:0.9; }

/* Search */
#searchInput{ margin-bottom:20px; padding:12px 16px; width:100%; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 4px 12px rgba(0,0,0,0.04); }
#searchInput:focus{ outline:none; border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }

/* Course cards */
.course-item .card{ cursor:pointer; height:230px; position: relative; border-radius:16px; overflow: hidden; color:white; border:none; transition: transform 0.3s, box-shadow 0.3s; }
.course-item .card:hover{ transform: translateY(-6px); box-shadow: 0 14px 32px rgba(0,0,0,0.25); }
.course-item .card::before{ content:''; position:absolute; inset:0; background:linear-gradient(180deg, rgba(1,33,71,0) 40%, rgba(1,33,71,0.85) 100%); }
.course-item .card .card-body{ position: absolute; bottom: 0; left:0; right:0; padding: 16px; z-index:2; }
.course-item .card .card-body h5{ font-size:15px; font-weight:700; margin-bottom:8px; }
.course-item .card .badge{ border-radius:20px; padding:6px 12px; font-weight:600; }

/* Section headings */
.section-heading{ font-size:22px; font-weight:700; color:#012147; margin-bottom:18px; display:flex; align-items:center; gap:10px; }

/* Faculty structure accordion */
.structure-card{ border-radius:16px; border:none; box-shadow:0 6px 20px rgba(0,0,0,0.06); padding:22px; margin-bottom:20px; }
.structure-header{ cursor:pointer; }
.structure-header h4{ font-size:17px; color:#012147; font-weight:700; margin-bottom:4px; }
.structure-header .badge{ border-radius:20px; padding:6px 12px; }
.structure-header i{ transition:transform 0.2s; color:#012147; }

.level-block h5{ font-size:15px; font-weight:700; color:#012147; margin-bottom:10px; display:flex; align-items:center; gap:8px; }
.level-block h5::before{ content:''; width:6px; height:6px; border-radius:50%; background:#3b82f6; }

.semester-btn{
    border-radius:10px !important; border:1px solid #e2e8f0 !important; color:#012147 !important;
    background:#f8fafc !important; font-weight:600; padding:10px 14px !important;
}
.semester-btn:hover{ background:#eef2f9 !important; }

.subject-list .list-group-item{
    border:none; border-bottom:1px solid #f1f5f9; padding:12px 16px;
}
.subject-list .list-group-item:hover{ background:#f8fafc; }
.subject-list a{ color:#012147; font-weight:500; }

/* Calendar card */
.calendar-card{ border-radius:16px; border:none; box-shadow:0 6px 20px rgba(0,0,0,0.06); overflow:hidden; }
.calendar-card .card-header{ background:#012147; color:#fff; font-weight:700; border:none; padding:14px 18px; }
#calendar{ text-align:center; font-size:14px; }
#calendar table{ width:100%; border-collapse:collapse; }
#calendar th{ color:#b22222; font-weight:bold; padding:5px 0; }
#calendar td{ padding:8px; border:1px solid #f1f5f9; border-radius:6px; }
#calendar .today{ background:#012147; color:white; border-radius:50%; font-weight:bold; }

/* Modal */
#levelModal{ display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.55); justify-content:center; align-items:center; z-index:6000; }
#levelModalContent{ background:white; padding:26px; border-radius:16px; max-width:400px; width:90%; text-align:center; position:relative; box-shadow:0 20px 50px rgba(0,0,0,0.3); }
#levelModalContent span{ position:absolute; top:14px; right:18px; cursor:pointer; font-size:22px; color:#64748b; }
#modalCourseName{ color:#012147; font-weight:700; margin-bottom:10px; }
#modalCourseLevels a{ display:block; font-size:16px; font-weight:600; text-decoration:none; color:#012147; cursor:pointer; padding:10px 14px; border-radius:10px; transition: 0.2s; width:100%; }
#modalCourseLevels a:hover{ background:#f1f5f9; }

/* Footer */
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
footer.shifted{ margin-left:260px; transition:0.3s; }

/* ===== RESPONSIVE ===== */
@media (max-width: 992px) {
    .sidebar{ width: 220px; left: -220px; }
}

@media (max-width: 768px) {
    .topbar{ padding: 8px 14px; min-height:45px; }
    .topbar h1{ font-size:16px; }
    .topbar .actions span{ display:none; }
    .header{ top:53px; padding:10px 14px; height:75px; }
    .logo-area img{ width:42px; }
    .campus-name{ font-size:16px; }
    .lms-name{ font-size:11px; }
    .page-content.shifted, .topbar.shifted, .header.shifted, footer.shifted{ margin-left:0 !important; left:0 !important; width:100% !important; }
    .hero{ padding:150px 16px 60px; }
    .hero h1{ font-size:26px; }
    .hero p{ font-size:14px; }
    .course-item .card{ height:190px; }
    .footer-grid{ grid-template-columns:1fr; text-align:center; }
    .footer-box h5{ border-left:none; border-bottom:2px solid #3b82f6; padding-bottom:8px; }
    .sidebar{ width:80%; max-width:280px; left:-100%; }
    .sidebar.active{ left:0; }
    .structure-header{
        flex-direction:column;
        align-items:flex-start !important;
        gap:10px;
    }
    .structure-header .d-flex.align-items-center.gap-2{
        align-self:flex-end;
    }
    .course-item{
        flex: 0 0 50%;
        max-width: 50%;
    }
    .semester-btn span{
        white-space:normal;
        word-break:break-word;
    }
}

@media (max-width: 480px) {
    .header{ height:70px; }
    .lms-name{ display:none; }
    .hero h1{ font-size:22px; }
    .hero p{ font-size:13px; }
    .sidebar{ width:100%; }
    .footer-desc{ font-size:13px; }
    .course-item{ flex: 0 0 100%; max-width: 100%; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <span class="close-btn" onclick="closeMenu()"><i class="bi bi-x"></i></span>
    <h4>Menu</h4>
    <a href="/">
        <i class="bi bi-house-door-fill"></i> Home
    </a>
    <a href="/student-grade">
        <i class="bi bi-mortarboard-fill"></i> Grade & GPA Scale
    </a>
</div>
<div class="overlay" id="overlay" onclick="closeMenu()"></div>

<div class="page-content" id="pageContent">

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
            <div class="section-heading"><i class="bi bi-collection"></i> Available Courses</div>
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
                    <div class="card" style="background-image: url('{{ asset($imgPath) }}'); background-size: cover; background-position: center;">
                        <div class="card-body">
                            <h5>{{ $course->code }} - {{ $course->name }}</h5>
                            <span class="badge bg-success">{{ ucfirst($course->status) }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-5">
                <div class="section-heading"><i class="bi bi-diagram-3"></i> Faculty Structure</div>
                @foreach($faculty->courses as $course)
                    @php $courseCollapseId = 'course-' . $course->id; @endphp
                    <div class="card structure-card">
                        <div class="d-flex justify-content-between align-items-center structure-header"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $courseCollapseId }}"
                            aria-expanded="false">
                            <div>
                                <h4>{{ $course->code }} - {{ $course->name }}</h4>
                                <small class="text-muted">{{ $course->description ?? 'No course description available.' }}</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary">{{ ucfirst($course->status) }}</span>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                        </div>

                        <div class="collapse mt-3" id="{{ $courseCollapseId }}">

                            @if($course->levels->count())
                                @foreach($course->levels as $level)
                                    @php
                                        $levelName = strtolower(trim($level->name));
                                        $semesterCount = match($levelName) {
                                            'hnd' => 4,
                                            'diploma' => 2,
                                            'degree' => 6,
                                            default => max($level->semesters->count(), 0),
                                        };
                                        $semesters = $level->semesters->values();
                                    @endphp
                                    <div class="level-block mb-4">
                                        <h5>{{ $level->name }}</h5>
                                        @if($semesterCount > 0)
                                            @for($i = 1; $i <= $semesterCount; $i++)
                                                @php
                                                    $semester = $semesters->get($i - 1);
                                                    $semesterName = $semester?->name ?? "Semester {$i}";
                                                    $collapseId = 'semester-' . $course->id . '-' . $level->id . '-' . $i;
                                                @endphp
                                                <div class="mb-2 ps-3">
                                                    <button class="btn semester-btn w-100 text-start d-flex justify-content-between align-items-center"
                                                            type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#{{ $collapseId }}"
                                                            aria-expanded="false">
                                                        <span>{{ $semesterName }}</span>
                                                        <i class="bi bi-chevron-down"></i>
                                                    </button>

                                                    <div class="collapse mt-2" id="{{ $collapseId }}">
                                                        @if($semester && $semester->subjects->count())
                                                            <ul class="list-group subject-list mb-2">
                                                                @foreach($semester->subjects as $subject)
                                                                    <li class="list-group-item py-2">
                                                                        <a href="{{ route('lecturer.subject.show', $subject->id) }}"
                                                                        class="text-decoration-none d-flex justify-content-between align-items-center">
                                                                            <span>{{ $subject->code }} - {{ $subject->name }}</span>
                                                                            <i class="bi bi-chevron-right"></i>
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <p class="text-muted ps-3">No subjects found for this semester.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endfor
                                        @else
                                            <p class="text-muted ps-3">No semesters available for this level.</p>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No levels configured for this course.</p>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <p>No courses available for this faculty.</p>
            @endif
        </div>

        <div class="col-md-3">
            <div class="card calendar-card">
                <div class="card-header"><i class="bi bi-calendar3"></i> Calendar</div>
                <div class="card-body">
                    <div id="calendar"></div>
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
                <a href="https://ttmetrocampus.com/"><i class="bi bi-globe"></i> www.ttmetrocampus.com</a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
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