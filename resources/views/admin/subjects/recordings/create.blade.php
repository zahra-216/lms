<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Recording | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    .container { max-width:560px; margin:auto; }
    .back-btn{ border:none; background:#fff; color:#012147; font-weight:600; padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06); text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px; }
    .back-btn:hover{ background:#012147; color:#fff; }
    .page-header{ background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff; border-radius:18px; padding:24px 28px; margin-bottom:26px; box-shadow:0 10px 30px rgba(1,33,71,0.25); }
    .page-header h3{ margin:0; font-weight:700; font-size:20px; }
    .card-box{ background:#fff; border-radius:16px; padding:28px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }
    .form-label{ font-weight:600; color:#012147; font-size:14px; margin-bottom:6px; }
    .form-control{ border-radius:10px; border:1px solid #e2e8f0; padding:11px 14px; }
    .form-control:focus{ border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }
    .hint{ font-size:12px; color:#6b7280; margin-top:6px; }
    .btn-navy{ width:100%; background:#012147; color:#fff; border:none; padding:12px; border-radius:12px; font-weight:600; }
    .btn-navy:hover{ background:#0b2d5a; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.subjects.recordings.index', $subject->id) }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h3><i class="bi bi-plus-circle"></i> Add Recording</h3>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-box">
        <form action="{{ route('admin.subjects.recordings.store', $subject->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Week 5 - Loops in Python" required>
            </div>
            <div class="mb-3">
                <label class="form-label">YouTube Link or Video ID</label>
                <input type="text" name="youtube_video_id" class="form-control" placeholder="https://youtube.com/watch?v=... or just the ID" required>
                <div class="hint">Paste the full YouTube link or just the video ID — either works.</div>
            </div>
            <button type="submit" class="btn-navy">Save Recording</button>
        </form>
    </div>
</div>
</body>
</html>