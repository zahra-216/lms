@extends('layouts.admin')
@section('title')
{{ $student->name }} - Chats
@endsection
@section('back', route('admin.chats.index'))
@push('styles')
<style>
.thread-list { background:#fff; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.06); overflow:hidden;
}
.thread-item { display:flex; align-items:center; gap:14px; padding:16px 18px; text-decoration:none; color:#012147; border-bottom:1px solid #eef1f6;
}
.thread-item:last-child { border-bottom:none;
}
.thread-item:hover { background:#f8fafc;
}
.avatar {
        width:44px; height:44px; border-radius:50%; background:#012147; color:#fff;
        display:flex; align-items:center; justify-content:center; font-weight:700; flex-shrink:0;
}
.pair-name { font-weight:600; font-size:14.5px;
}
.thread-preview { font-size:13px; color:#64748b; max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
}
.thread-meta { margin-left:auto; text-align:right; font-size:11.5px; color:#94a3b8;
}
.badge-unread { background:#fdeaea; color:#8a1f1f; font-size:11px; font-weight:700; border-radius:20px; padding:3px 10px;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-person-lines-fill"></i> {{ $student->name }}</h3>
        <small>{{ $student->registration_no }} &middot; conversations with lecturers</small>
    </div>

    @if($lecturers->count() > 0)
    <div class="thread-list">
        @foreach($lecturers as $lecturer)
        <a href="{{ route('admin.chats.show', [$student->id, $lecturer->id]) }}" class="thread-item">
            <div class="avatar">{{ strtoupper(substr($lecturer->name, 0, 1)) }}</div>
            <div>
                <div class="pair-name">{{ $lecturer->name }}</div>
                <div class="thread-preview">{{ $lecturer->last_message ? Str::limit($lecturer->last_message->message, 50) : '' }}</div>
            </div>
            <div class="thread-meta">
                @if($lecturer->last_message)
                    <div>{{ $lecturer->last_message->created_at->format('M d, H:i') }}</div>
                @endif
                @if($lecturer->unread_count > 0)
                    <span class="badge-unread">{{ $lecturer->unread_count }} unread</span>
                @endif
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="empty-box">
        <i class="bi bi-chat-square-dots"></i>
        <p class="mt-3 mb-0">No conversations found</p>
    </div>
    @endif
</div>
@endsection
