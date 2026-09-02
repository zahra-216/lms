<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>All Chats</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    .container { max-width:900px; margin:auto; }
    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff; border-radius:18px;
        padding:24px 28px; margin-bottom:26px; box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h3{ margin:0; font-weight:700; font-size:20px; }
    .thread-list{ background:#fff; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.06); overflow:hidden; }
    .thread-item{ display:flex; align-items:center; gap:14px; padding:16px 18px; text-decoration:none; color:#012147; border-bottom:1px solid #eef1f6; }
    .thread-item:last-child{ border-bottom:none; }
    .thread-item:hover{ background:#f8fafc; }
    .pair-name{ font-weight:600; font-size:14.5px; }
    .pair-sub{ font-size:12.5px; color:#64748b; }
    .thread-preview{ font-size:13px; color:#64748b; max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .thread-meta{ margin-left:auto; text-align:right; font-size:11.5px; color:#94a3b8; }
    .badge-count{ background:#e6f0ff; color:#0a3d91; font-size:11px; font-weight:600; border-radius:20px; padding:3px 10px; }
    .empty-box{ background:#fff; border-radius:14px; padding:60px 20px; text-align:center; box-shadow:0 6px 20px rgba(0,0,0,0.06); color:#64748b; }
    .empty-box i{ font-size:2.5rem; color:#cbd5e1; }
</style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h3><i class="bi bi-chat-square-text"></i> All Chats</h3>
        <small>Every student ↔ lecturer conversation</small>
    </div>

    @if($pairs->count() > 0)
    <div class="thread-list">
        @foreach($pairs as $pair)
        <a href="{{ route('admin.chats.show', [$pair->student->id, $pair->lecturer->id]) }}" class="thread-item">
            <div>
                <div class="pair-name">{{ $pair->student->name }} <i class="bi bi-arrow-left-right" style="font-size:11px;color:#94a3b8;"></i> {{ $pair->lecturer->name }}</div>
                <div class="pair-sub">Student ↔ Lecturer</div>
                <div class="thread-preview">{{ $pair->last_message ? Str::limit($pair->last_message->message, 50) : '' }}</div>
            </div>
            <div class="thread-meta">
                @if($pair->last_message)
                    <div>{{ $pair->last_message->created_at->format('M d, H:i') }}</div>
                @endif
                <span class="badge-count">{{ $pair->count }} msgs</span>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="empty-box">
        <i class="bi bi-chat-square-dots"></i>
        <p class="mt-3 mb-0">No chats yet</p>
    </div>
    @endif
</div>
</body>
</html>