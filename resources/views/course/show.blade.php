<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $course->code }} - {{ $course->name }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{ font-family:'Segoe UI', sans-serif; background:#f4f7ff; margin:0; }
.topbar{ position:fixed; top:0; left:0; width:100%; background:#012147; color:white; padding:14px 24px; display:flex; justify-content:space-between; align-items:center; z-index:4000; }
.topbar h1{ font-size:20px; margin:0; font-weight:600; }
.topbar .actions{ display:flex; align-items:center; gap:12px; }
.logout-btn{ background:#ff4d4f; border:none; color:#fff; padding:10px 18px; border-radius:10px; cursor:pointer; }
.logout-btn:hover{ opacity:.92; }
.course-header{ margin-top:90px; background:#012147; color:white; padding:30px 20px; text-align:center; }
.course-header h1{ font-size:36px; margin-bottom:10px; }
.course-header span{ font-size:18px; background:#ff9900; color:#012147; padding:5px 10px; border-radius:5px; }
.course-details{ padding:30px 20px; }
.course-details img{ max-width:100%; border-radius:10px; margin-bottom:20px; }
.course-info span{ display:inline-block; margin-right:15px; font-weight:bold; }
.back-btn{ margin-top:20px; display:inline-block; text-decoration:none; color:#012147; background:#ff9900; padding:8px 15px; border-radius:6px; }
</style>
</head>
<body>
    <div class="topbar">
        <h1>{{ $course->code }} - {{ $course->name }}</h1>
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

<div class="course-header">
    <h1>{{ $course->code }} - {{ $course->name }}</h1>
    <span>{{ ucfirst($course->level) }}</span>
</div>

<div class="container course-details">
    <div class="course-info mb-3">
        <span>Status: {{ ucfirst($course->status) }}</span>
    </div>
    
    @if($course->image && file_exists(public_path('storage/courses/'.$course->image)))
        <img src="{{ asset('storage/courses/'.$course->image) }}" alt="{{ $course->name }}">
    @else
        <img src="https://via.placeholder.com/600x400" alt="{{ $course->name }}">
    @endif
    
    <p>{{ $course->description ?? 'No description available for this course.' }}</p>
    
    <a href="{{ url()->previous() }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back to Courses</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>