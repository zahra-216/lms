<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject->name }} - Lecture Videos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
    }

    .container { max-width:1000px; margin:auto; }

    .back-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .back-btn:hover{ background:#012147; color:#fff; }

    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:26px 30px; margin:18px 0 26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }

    .card-box{
        background:#fff; padding:20px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:26px;
    }

    table.videos-table{ border-collapse:separate; border-spacing:0; }
    table.videos-table thead th{
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap; text-align:left;
    }
    table.videos-table thead th:first-child{ border-top-left-radius:10px; }
    table.videos-table thead th:last-child{ border-top-right-radius:10px; }
    table.videos-table tbody td{ vertical-align:middle; padding:10px; }
    table.videos-table tbody tr:nth-child(even){ background:#f8fafc; }
    table.videos-table tbody tr:hover{ background:#eef2f9; }

    .watch-link{
        display:inline-flex; align-items:center; gap:6px;
        color:#012147; font-weight:600; text-decoration:none;
        padding:6px 14px; border-radius:20px; background:#eef2f9; font-size:13px;
    }
    .watch-link:hover{ background:#012147; color:#fff; }

    .empty-state{ text-align:center; color:#94a3b8; padding:20px 0; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('student.subject.portal.show', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <h2><i class="bi bi-camera-video"></i> {{ $subject->code }} - {{ $subject->name }} — Lecture Videos</h2>
    </div>

    <div class="card-box">
    @if($subject->videos && $subject->videos->count())
        <div class="table-responsive">
        <table class="table videos-table align-middle mb-0">
            <thead>
                <tr><th>Title</th><th>Watch</th></tr>
            </thead>
            <tbody>
                @foreach($subject->videos as $video)
                    @if($video->is_published)
                    <tr>
                        <td>{{ $video->title }}</td>
                        <td>
                            @if($video->type === 'file' && $video->video_path)
                                <a href="{{ asset('storage/' . $video->video_path) }}" target="_blank" class="watch-link">
                                    <i class="bi bi-play-circle"></i> Watch
                                </a>
                            @elseif($video->video_url)
                                <a href="{{ $video->video_url }}" target="_blank" class="watch-link">
                                    <i class="bi bi-play-circle"></i> Watch
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        </div>
    @else
        <div class="empty-state">No lecture videos uploaded yet for this subject.</div>
    @endif
    </div>
</div>
</body>
</html>