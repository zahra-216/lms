<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Messages</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){ body { padding:20px 12px; } }
    .container { max-width:800px; margin:auto; }
    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:24px 28px; margin-bottom:26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h3{ margin:0; font-weight:700; font-size:20px; }
    .page-header small{ opacity:0.85; }
    .contact-list{ background:#fff; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.06); overflow:hidden; }
    .contact-item{
        display:flex; align-items:center; gap:14px; padding:16px 18px;
        text-decoration:none; color:#012147; border-bottom:1px solid #eef1f6;
    }
    .contact-item:last-child{ border-bottom:none; }
    .contact-item:hover{ background:#f8fafc; }
    .avatar{
        width:46px; height:46px; border-radius:50%; background:#012147; color:#fff;
        display:flex; align-items:center; justify-content:center; font-weight:700; flex-shrink:0;
    }
    .contact-name{ font-weight:600; font-size:15px; }
    .contact-preview{ font-size:13px; color:#64748b; max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .contact-meta{ margin-left:auto; text-align:right; }
    .contact-time{ font-size:11.5px; color:#94a3b8; }
    .unread-badge{
        background:#012147; color:#fff; font-size:11px; font-weight:700;
        border-radius:20px; padding:2px 8px; display:inline-block; margin-top:4px;
    }
    .empty-box{ background:#fff; border-radius:14px; padding:60px 20px; text-align:center; box-shadow:0 6px 20px rgba(0,0,0,0.06); color:#64748b; }
    .empty-box i{ font-size:2.5rem; color:#cbd5e1; }
</style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h3><i class="bi bi-chat-dots"></i> Student Messages</h3>
        <small>Conversations with students</small>
    </div>

    @if($students->count() > 0)
    <div class="contact-list">
        @foreach($students as $student)
        <a href="{{ route('lecturer.chat.show', $student->id) }}" class="contact-item">
            <div class="avatar">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
            <div>
                <div class="contact-name">{{ $student->name }}</div>
                <div class="contact-preview">
                    @if($student->last_message)
                        {{ $student->last_message->sender_type === 'lecturer' ? 'You: ' : '' }}{{ Str::limit($student->last_message->message, 40) }}
                    @endif
                </div>
            </div>
            <div class="contact-meta">
                @if($student->last_message)
                    <div class="contact-time">{{ $student->last_message->created_at->format('M d, H:i') }}</div>
                @endif
                @if($student->unread_count > 0)
                    <div class="unread-badge">{{ $student->unread_count }}</div>
                @endif
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="empty-box">
        <i class="bi bi-chat-square-dots"></i>
        <p class="mt-3 mb-0">No conversations yet</p>
    </div>
    @endif
</div>
</body>
</html>