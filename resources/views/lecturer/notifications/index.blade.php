@extends('layouts.lecturer')
@section('title')
Notifications
@endsection
@push('styles')
<style>
.header-btns { display:flex; gap:10px; flex-wrap:wrap;
}
.btn-back {
        background:#fff; color:#012147; font-weight:600; padding:10px 20px;
        border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;
        border:none; transition:0.2s;
}
.btn-back:hover { background:#e2e8f0; color:#012147;
}
.notif-card {
        background:#fff; border-radius:16px; padding:18px 22px; margin-bottom:14px;
        box-shadow:0 8px 26px rgba(0,0,0,0.06); border-left:5px solid #e2e8f0;
        display:flex; gap:16px; align-items:flex-start; justify-content:space-between;
}
.notif-card.unread { border-left-color:#012147; background:#f8fafc;
}
.notif-card h5 { margin:0 0 6px; font-size:16px; font-weight:700; color:#012147;
}
.notif-card.read h5 { font-weight:600; color:#475569;
}
.notif-card p { margin:0 0 8px; font-size:14px; line-height:1.55; color:#334155; white-space:pre-line;
}
.notif-card .time { font-size:12px; color:#64748b;
}
.notif-link { font-size:13px; font-weight:600; color:#012147; text-decoration:none; margin-left:12px;
}
.notif-link:hover { text-decoration:underline;
}
.mark-btn {
        background:#eef2f9; color:#012147; border:none; border-radius:8px;
        padding:6px 12px; font-size:12px; font-weight:600; white-space:nowrap; transition:0.2s;
}
.mark-btn:hover { background:#012147; color:#fff;
}
.empty-note { text-align:center; color:#6b7280; padding:40px 20px; background:#fff; border-radius:16px;
}
@media (max-width:576px) {
.notif-card { flex-direction:column;
}
}
</style>
@endpush
@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-bell"></i> Notifications</h2>
            <small>{{ $lecturer->unreadNotifications->count() }} unread</small>
        </div>
        <div class="header-btns">
            @if($lecturer->unreadNotifications->count())
                
            @endif
            
        </div>
    </div>

    @forelse($notifications as $note)
        <div class="notif-card {{ $note->read_at ? 'read' : 'unread' }}">
            <div>
                <h5>{{ $note->data['title'] ?? 'Notification' }}</h5>
                <p>{{ $note->data['message'] ?? '' }}</p>
                <span class="time">
                    <i class="bi bi-clock"></i> {{ $note->created_at->diffForHumans() }}
                </span>
                @if(isset($note->data['subject_id']))
                    <a href="{{ route('lecturer.subject.timetable', $note->data['subject_id']) }}" class="notif-link">
                        View timetable <i class="bi bi-arrow-right"></i>
                    </a>
                @endif
            </div>
            @unless($note->read_at)
                <button type="button" class="mark-btn" onclick="markRead(this, '{{ $note->id }}')">
                    <i class="bi bi-check2"></i> Mark as read
                </button>
            @endunless
        </div>
    @empty
        <div class="empty-note">You have no notifications yet.</div>
    @endforelse

    <div class="mt-3">
        {{ $notifications->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
@push('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function markRead(btn, id) {
    fetch('/lecturer/notification/read/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    }).then(() => window.location.reload());
}

function markAllRead() {
    fetch('{{ route('lecturer.notification.readAll') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    }).then(() => window.location.reload());
}
</script>
@endpush
