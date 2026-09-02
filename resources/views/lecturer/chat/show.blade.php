<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Chat with {{ $student->name }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { background:#e9edf5; font-family:'Segoe UI', sans-serif; margin:0; height:100vh; display:flex; flex-direction:column; }
    .chat-header{
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        padding:14px 20px; display:flex; align-items:center; gap:14px;
        box-shadow:0 4px 12px rgba(1,33,71,0.2); flex-shrink:0;
    }
    .chat-header a{ color:#fff; font-size:20px; text-decoration:none; }
    .chat-header .avatar{
        width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,0.15);
        display:flex; align-items:center; justify-content:center; font-weight:700;
    }
    .chat-header .name{ font-weight:700; font-size:16px; }
    .chat-body{ flex:1; overflow-y:auto; padding:20px; display:flex; flex-direction:column; gap:4px; }
    .bubble-row{ display:flex; margin-bottom:6px; }
    .bubble-row.from-lecturer{ justify-content:flex-end; }
    .bubble-row.from-student{ justify-content:flex-start; }
    .bubble{
        max-width:75%; padding:10px 14px; border-radius:14px; font-size:14.5px; line-height:1.4;
        box-shadow:0 2px 6px rgba(0,0,0,0.06); word-wrap:break-word;
    }
    .bubble.lecturer{ background:#012147; color:#fff; border-bottom-right-radius:4px; }
    .bubble.student{ background:#fff; color:#1e293b; border-bottom-left-radius:4px; }
    .bubble .time{ display:block; font-size:10.5px; opacity:0.7; margin-top:4px; text-align:right; }
    .chat-input{
        background:#fff; padding:12px 16px; display:flex; gap:10px; align-items:center;
        border-top:1px solid #e2e8f0; flex-shrink:0;
    }
    .chat-input input{
        flex:1; border:1px solid #e2e8f0; border-radius:24px; padding:11px 18px; font-size:14.5px; outline:none;
    }
    .chat-input input:focus{ border-color:#012147; }
    .send-btn{
        background:#012147; color:#fff; border:none; width:44px; height:44px; border-radius:50%;
        display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:18px;
    }
    .send-btn:hover{ background:#1e3a6e; }
    .empty-chat{ text-align:center; color:#94a3b8; margin-top:40px; font-size:13.5px; }
</style>
</head>
<body>

<div class="chat-header">
    <a href="{{ route('lecturer.chat.index') }}"><i class="bi bi-arrow-left"></i></a>
    <div class="avatar">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
    <div class="name">{{ $student->name }}</div>
</div>

<div class="chat-body" id="chatBody">
    @forelse($messages as $msg)
        <div class="bubble-row from-{{ $msg->sender_type }}">
            <div class="bubble {{ $msg->sender_type }}">
                {{ $msg->message }}
                <span class="time">{{ $msg->created_at->format('H:i') }}</span>
            </div>
        </div>
    @empty
        <div class="empty-chat">No messages yet.</div>
    @endforelse
</div>

<form id="chatForm" class="chat-input">
    @csrf
    <input type="text" id="messageInput" placeholder="Type a message..." autocomplete="off" required>
    <button type="submit" class="send-btn"><i class="bi bi-send-fill"></i></button>
</form>

<script>
const chatBody = document.getElementById('chatBody');
const chatForm = document.getElementById('chatForm');
const messageInput = document.getElementById('messageInput');
const csrfToken = document.querySelector('input[name="_token"]').value;
const storeUrl = @json(route('lecturer.chat.store', $student->id));
const pollUrl = @json(route('lecturer.chat.poll', $student->id));

let lastId = {{ $messages->last()->id ?? 0 }};

function scrollToBottom() { chatBody.scrollTop = chatBody.scrollHeight; }
scrollToBottom();

function appendMessage(msg) {
    const row = document.createElement('div');
    row.className = 'bubble-row from-' + msg.sender_type;
    row.innerHTML = `<div class="bubble ${msg.sender_type}">${msg.message.replace(/</g,'&lt;')}<span class="time">${msg.time}</span></div>`;
    chatBody.appendChild(row);
    lastId = msg.id;
    scrollToBottom();
}

chatForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const text = messageInput.value.trim();
    if (!text) return;
    messageInput.value = '';

    fetch(storeUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ message: text })
    })
    .then(res => res.json())
    .then(msg => appendMessage(msg));
});

setInterval(function () {
    fetch(pollUrl + '?after=' + lastId, { headers: { 'Accept': 'application/json' } })
        .then(res => res.json())
        .then(messages => messages.forEach(appendMessage));
}, 3000);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>