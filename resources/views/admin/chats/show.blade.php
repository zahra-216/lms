<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $student->name }} ↔ {{ $lecturer->name }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { background:#e9edf5; font-family:'Segoe UI', sans-serif; margin:0; }
    .chat-header{
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        padding:16px 24px; display:flex; align-items:center; gap:14px;
        box-shadow:0 4px 12px rgba(1,33,71,0.2);
    }
    .chat-header a{ color:#fff; font-size:20px; text-decoration:none; }
    .chat-header .name{ font-weight:700; font-size:16px; }
    .chat-header .sub{ font-size:12px; opacity:0.8; }
    .chat-body{ max-width:800px; margin:20px auto; padding:20px; display:flex; flex-direction:column; gap:4px; }
    .bubble-row{ display:flex; margin-bottom:6px; }
    .bubble-row.from-lecturer{ justify-content:flex-end; }
    .bubble-row.from-student{ justify-content:flex-start; }
    .bubble{ max-width:65%; padding:10px 14px; border-radius:14px; font-size:14.5px; line-height:1.4; box-shadow:0 2px 6px rgba(0,0,0,0.06); word-wrap:break-word; }
    .bubble.lecturer{ background:#012147; color:#fff; border-bottom-right-radius:4px; }
    .bubble.student{ background:#fff; color:#1e293b; border-bottom-left-radius:4px; }
    .bubble .who{ display:block; font-size:10px; opacity:0.7; margin-bottom:2px; font-weight:700; text-transform:uppercase; }
    .bubble .time{ display:block; font-size:10.5px; opacity:0.7; margin-top:4px; text-align:right; }
    .readonly-note{ text-align:center; color:#94a3b8; font-size:12.5px; margin-top:10px; }
</style>
</head>
<body>

<div class="chat-header">
    <a href="{{ route('admin.chats.index') }}"><i class="bi bi-arrow-left"></i></a>
    <div>
        <div class="name">{{ $student->name }} ↔ {{ $lecturer->name }}</div>
        <div class="sub">Read-only conversation view</div>
    </div>
</div>

<div class="chat-body">
    @forelse($messages as $msg)
        <div class="bubble-row from-{{ $msg->sender_type }}">
            <div class="bubble {{ $msg->sender_type }}">
                <span class="who">{{ $msg->sender_type === 'student' ? $student->name : $lecturer->name }}</span>
                {{ $msg->message }}
                <span class="time">{{ $msg->created_at->format('M d, H:i') }}</span>
            </div>
        </div>
    @empty
        <p class="text-center text-muted">No messages</p>
    @endforelse
    <div class="readonly-note">Admin view — messages cannot be sent from here</div>
</div>
</body>
</html>