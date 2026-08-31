<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject->name }} - Module</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }

    @media (max-width: 480px){
        body { padding:20px 12px; }
        .module-card { padding:25px 15px; }
    }

    .container { max-width:900px; margin:auto; }

    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:26px 30px; margin-bottom:30px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }
    .page-header small{ opacity:0.85; }

    .module-card {
        border-radius:15px;
        text-decoration:none;
        color:#012147;
        background:#fff;
        padding:35px 20px;
        text-align:center;
        display:block;
        transition:0.2s;
        box-shadow:0 6px 20px rgba(0,0,0,0.06);
    }
    .module-card:hover { transform:translateY(-5px); color:#012147; box-shadow:0 10px 26px rgba(0,0,0,0.1); }
    .module-card i {
        font-size:1.8rem; margin-bottom:12px; display:flex;
        align-items:center; justify-content:center;
        width:56px; height:56px; border-radius:14px; color:#fff;
        margin-left:auto; margin-right:auto;
    }
    .module-card span{ font-weight:600; display:block; }

    .icon-notes{ background:#3b82f6; }
    .icon-videos{ background:#8b5cf6; }
    .icon-assignments{ background:#f59e0b; }
    .icon-quizzes{ background:#ec4899; }
    .icon-grades{ background:#10b981; }
    .icon-attendance{ background:#ef4444; }
    .icon-timetable{ background:#06b6d4; }
</style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-mortarboard"></i> {{ $subject->code }} - {{ $subject->name }}</h2>
            <small>Choose a module below</small>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-3 col-6">
            <a href="{{ route('lecturer.subject.notes', $subject->id) }}" class="module-card">
                <i class="bi bi-file-earmark-text icon-notes"></i>
                <span>Notes</span>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('lecturer.subject.videos', $subject->id) }}" class="module-card">
                <i class="bi bi-camera-video icon-videos"></i>
                <span>Lecture Videos</span>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('lecturer.subject.assignments', $subject->id) }}" class="module-card">
                <i class="bi bi-journal-text icon-assignments"></i>
                <span>Assignments</span>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('lecturer.quizzes.index', ['subject' => $subject->id]) }}" class="module-card">
                <i class="bi bi-puzzle icon-quizzes"></i>
                <span>Quizzes</span>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('lecturer.subject.grades', $subject->id) }}" class="module-card">
                <i class="bi bi-clipboard-data icon-grades"></i>
                <span>Grades</span>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('lecturer.subject.timetable', $subject->id) }}" class="module-card">
                <i class="bi bi-calendar-week icon-timetable"></i>
                <span>Timetable</span>
            </a>
        </div>
    </div>
</div>
</body>
</html>