<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Module</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6f9; font-family:'Segoe UI', sans-serif; padding:40px; }
    .container { max-width:900px; margin:auto; }
    h2 { color:#012147; margin-bottom:30px; }
    .module-card {
        border-radius:15px;
        text-decoration:none;
        color:#fff;
        background:#012147;
        padding:35px 20px;
        text-align:center;
        display:block;
        transition:0.2s;
        box-shadow:0 6px 20px rgba(0,0,0,0.1);
    }
    .module-card:hover { transform:translateY(-5px); color:#fff; background:#021634; }
    .module-card i { font-size:2.2rem; margin-bottom:10px; display:block; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('student.my.courses') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2>{{ $subject->code }} - {{ $subject->name }}</h2>

    <div class="row g-4">
        <div class="col-md-3 col-6">
            <a href="{{ route('student.subject.portal.notes', $subject->id) }}" class="module-card">
                <i class="bi bi-file-earmark-text"></i> Notes
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('student.subject.portal.videos', $subject->id) }}" class="module-card">
                <i class="bi bi-camera-video"></i> Lecture Videos
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('student.subject.portal.assignments', $subject->id) }}" class="module-card">
                <i class="bi bi-journal-text"></i> Assignments
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="{{ route('student.subject.portal.grades', $subject->id) }}" class="module-card">
                <i class="bi bi-clipboard-data"></i> Grades
            </a>
        </div>
    </div>
</div>
</body>
</html>