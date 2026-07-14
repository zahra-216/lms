<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#f4f6fb;
}

/* ================= TOPBAR ================= */
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
}
.topbar-profile{
    width:40px;
    height:40px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #fff;
    cursor:pointer;
}

/* ================= HEADER ================= */
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

/* ================= SIDEBAR ================= */
.sidebar{
    width:260px;
    background:#012147;
    position:fixed;
    top:130px;
    left:-260px;
    bottom:0;
    padding-top:15px;
    transition:0.3s ease;
    z-index:999;
    color:white;
    overflow-y:auto;
}

.sidebar.show{
    left:0;
}

.sidebar a{
    display:block;
    padding:12px 18px;
    color:#dbeafe;
    text-decoration:none;
}

.sidebar a:hover{
    background:rgba(255,255,255,0.1);
}

/* ================= MAIN ================= */
.main{
    padding:150px 20px 20px;
    transition:0.3s ease;
}

.main.shift{
    margin-left:260px;
}

/.profile-card{
    border-radius:20px;
    overflow:hidden;
}

/* TOP HEADER */
.profile-top{
    background: #012147;
    padding:40px 20px;
}

/* PROFILE IMAGE */
.profile-img-wrapper{
    position:relative;
    display:inline-block;
    cursor:pointer;
}

.profile-img{
    width:130px;
    height:130px;
    border-radius:50%;
    border:5px solid white;
    object-fit:cover;
}

/* CAMERA ICON */
.camera-badge{
    position:absolute;
    bottom:10px;
    right:10px;
    background:#fff;
    color:#0d6efd;
    width:32px;
    height:32px;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:14px;
}

/* INFO BOX */
.info-box{
    display:flex;
    align-items:center;
    gap:12px;
    background:#f8fafc;
    padding:15px;
    border-radius:12px;
    transition:0.3s;
}

.info-box i{
    font-size:22px;
    color:#0d6efd;
}

.info-box small{
    color:#6b7280;
}

.info-box h6{
    margin:0;
    font-weight:600;
}

.info-box:hover{
    transform:translateY(-3px);
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
}
/* ================= FOOTER ================= */
footer{
    background:#012147;
    color:#e2e8f0;
    margin-top:60px;
    transition:0.3s ease;
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

/* FOOTER SHIFT */
footer.shift{
    margin-left:260px;
}

/* ================= RESPONSIVE ================= */
@media (max-width:992px){
    .sidebar{
        width:220px;
    }

    .main.shift,
    footer.shift{
        margin-left:220px;
    }
}

@media (max-width:768px){

    .sidebar{
        width:80%;
        top:0;
        height:100vh;
        left:-100%;
    }

    .main.shift,
    footer.shift{
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
          {{ $student->name ?? '' }} ({{ $student->registration_no ?? '' }})
        </div>

        <div class="dropdown">
            <img
    src="{{ $student->photo ? asset('storage/'.$student->photo) : asset('images/user.png') }}"
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
    <a href="#"><i class="bi bi-folder"></i> Private Files</a>
   <a href="{{ route('student.my.courses') }}">
    <i class="bi bi-book"></i> My Courses
</a>
</div>

<div class="main" id="main">

    <!-- PROFILE CARD -->
    <div class="container py-4">

        <div class="card profile-card shadow-lg border-0">

            <!-- TOP HEADER -->
            <div class="profile-top text-center text-white">

                <form action="{{ route('student.photo.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <label for="photoInput" class="profile-img-wrapper">

                        <img id="previewImage"
                             src="{{ $student->photo ? asset('storage/'.$student->photo) : asset('images/user.png') }}"
                             class="profile-img">

                        <span class="camera-badge">
                            <i class="bi bi-camera-fill"></i>
                        </span>

                    </label>

                    <input type="file" name="photo" id="photoInput" hidden>

                    <button type="submit" class="btn btn-light btn-sm mt-2 px-3">
                        Upload Photo
                    </button>
                </form>

                <h4 class="mt-3 mb-0">{{ $student->name }}</h4>
                <small>{{ $student->registration_no }}</small>

                @if($student->last_seen_at && \Carbon\Carbon::parse($student->last_seen_at)->diffInMinutes() < 5)
                    <span class="badge bg-success mt-2">Online</span>
                @else
                    <span class="badge bg-secondary mt-2">Offline</span>
                @endif

            </div>

            <!-- BODY -->
            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">
                        <div class="info-box">
                            <i class="bi bi-envelope"></i>
                            <div>
                                <small>Email</small>
                                <h6>{{ $student->email ?? 'N/A' }}</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box">
                            <i class="bi bi-diagram-3"></i>
                            <div>
                                <small>Branch</small>
                                <h6>{{ $student->branch }}</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box">
                            <i class="bi bi-book"></i>
                            <div>
                                <small>Course</small>
                                <h6>{{ $student->course->name ?? 'N/A' }}</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box">
                            <i class="bi bi-layers"></i>
                            <div>
                                <small>Level</small>
                                <h6>{{ $student->level->name ?? 'N/A' }}</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box">
                            <i class="bi bi-clock"></i>
                            <div>
                                <small>Last Seen</small>
                                <h6>
                                    {{ $student->last_seen_at 
                                        ? \Carbon\Carbon::parse($student->last_seen_at)->diffForHumans() 
                                        : 'Offline' }}
                                </h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box">
                            <i class="bi bi-calendar"></i>
                            <div>
                                <small>Joined</small>
                                <h6>{{ optional($student->created_at)->format('d M Y') }}</h6>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('student.profile.edit') }}" class="btn btn-primary px-4">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </a>
                    <a href="{{ route('student.password.edit') }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-key"></i> Change Password
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
<!-- FOOTER (YOUR DESIGN) -->
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

<script>
function toggleSidebar(){
    document.getElementById("sidebar").classList.toggle("show");
    document.getElementById("main").classList.toggle("shift");
    document.getElementById("footer").classList.toggle("shift");
}


document.getElementById('photoInput').addEventListener('change', function(e){
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('previewImage').src = reader.result;
    }
    reader.readAsDataURL(e.target.files[0]);
});

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 