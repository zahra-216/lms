@extends('layouts.student')
@section('title')
Chat with Lecturers
@endsection
@push('scripts')
<script>
document.getElementById('lecturerSearch')?.addEventListener('input', function () {
    var q = this.value.trim().toLowerCase(), shown = 0;
    document.querySelectorAll('#contactList .contact-item').forEach(function (a) {
        var hit = a.querySelector('.contact-name').textContent.toLowerCase().includes(q);
        a.style.display = hit ? '' : 'none';
        if (hit) shown++;
    });
    var none = document.getElementById('noLecturer');
    if (!none) { none = document.createElement('div'); none.id = 'noLecturer'; none.className = 'text-center text-muted p-4'; none.textContent = 'No lecturer found.'; document.getElementById('contactList').appendChild(none); }
    none.style.display = shown ? 'none' : '';
});
</script>
@endpush
@push('styles')
<style>
.contact-list { background:#fff; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.06); overflow:hidden;
}
.contact-item {
        display:flex; align-items:center; gap:14px; padding:16px 18px;
        text-decoration:none; color:#012147; border-bottom:1px solid #eef1f6;
}
.contact-item:last-child { border-bottom:none;
}
.contact-item:hover { background:#f8fafc;
}
.avatar {
        width:46px; height:46px; border-radius:50%; background:#012147; color:#fff;
        display:flex; align-items:center; justify-content:center; font-weight:700; flex-shrink:0;
}
.contact-name { font-weight:600; font-size:15px;
}
.contact-preview { font-size:13px; color:#64748b; max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
}
.contact-meta { margin-left:auto; text-align:right;
}
.contact-time { font-size:11.5px; color:#94a3b8;
}
.unread-badge {
        background:#012147; color:#fff; font-size:11px; font-weight:700;
        border-radius:20px; padding:2px 8px; display:inline-block; margin-top:4px;
}
</style>
@endpush
@section('content')
<div class="container">
    <div class="page-header">
        <h3><i class="bi bi-chat-dots"></i> Chat</h3>
        <small>Message any lecturer directly</small>
    </div>

    @if($lecturers->count() > 0)
    <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="search" id="lecturerSearch" class="form-control" placeholder="Search for a lecturer by name..." autocomplete="off">
    </div>
    <div class="contact-list" id="contactList">
        @foreach($lecturers as $lecturer)
        <a href="{{ route('student.chat.show', $lecturer->id) }}" class="contact-item">
            <div class="avatar">{{ strtoupper(substr($lecturer->name, 0, 1)) }}</div>
            <div>
                <div class="contact-name">{{ $lecturer->name }}</div>
                <div class="contact-preview">
                    @if($lecturer->last_message)
                        {{ $lecturer->last_message->sender_type === 'student' ? 'You: ' : '' }}{{ Str::limit($lecturer->last_message->message, 40) }}
                    @else
                        <span class="text-muted">No messages yet - say hi</span>
                    @endif
                </div>
            </div>
            <div class="contact-meta">
                @if($lecturer->last_message)
                    <div class="contact-time">{{ $lecturer->last_message->created_at->format('M d, H:i') }}</div>
                @endif
                @if($lecturer->unread_count > 0)
                    <div class="unread-badge">{{ $lecturer->unread_count }}</div>
                @endif
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="empty-box">
        <i class="bi bi-person-x"></i>
        <p class="mt-3 mb-0">No lecturers found</p>
    </div>
    @endif
</div>
@endsection
