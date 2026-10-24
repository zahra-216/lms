@extends('layouts.admin')
@section('title')
All Chats
@endsection
@push('styles')
<style>
.search-box {
        background:#fff; border-radius:14px; padding:14px 18px; margin-bottom:20px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); display:flex; gap:10px; align-items:center;
}
.search-box i { color:#94a3b8; font-size:18px;
}
.search-box input { border:none; outline:none; flex:1; font-size:14.5px;
}
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
.pair-sub { font-size:12px; color:#64748b;
}
.thread-preview { font-size:13px; color:#64748b; max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
}
.thread-meta { margin-left:auto; text-align:right; font-size:11.5px; color:#94a3b8;
}
.badge-count { background:#e6f0ff; color:#0a3d91; font-size:11px; font-weight:600; border-radius:20px; padding:3px 10px; margin-left:6px;
}
.badge-unread { background:#fdeaea; color:#8a1f1f; font-size:11px; font-weight:700; border-radius:20px; padding:3px 10px; margin-left:6px;
}
</style>
@endpush
@section('content')
<div class="container">
    <div class="page-header">
        <h3><i class="bi bi-chat-square-text"></i> All Chats</h3>
        <small>Select a student to view their conversations</small>
    </div>

    <form method="GET" class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by student name or registration no...">
    </form>

    @if($students->count() > 0)
    <div class="thread-list">
        @foreach($students as $student)
        <a href="{{ route('admin.chats.student', $student->id) }}" class="thread-item">
            <div class="avatar">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
            <div>
                <div class="pair-name">{{ $student->name }}</div>
                <div class="pair-sub">{{ $student->registration_no }} &middot; {{ $student->thread_count }} lecturer{{ $student->thread_count == 1 ? '' : 's' }}</div>
                <div class="thread-preview">{{ $student->last_message ? Str::limit($student->last_message->message, 50) : '' }}</div>
            </div>
            <div class="thread-meta">
                @if($student->last_message)
                    <div>{{ $student->last_message->created_at->format('M d, H:i') }}</div>
                @endif
                @if($student->unread_count > 0)
                    <span class="badge-unread">{{ $student->unread_count }} unread</span>
                @endif
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="empty-box">
        <i class="bi bi-chat-square-dots"></i>
        <p class="mt-3 mb-0">No matching students found</p>
    </div>
    @endif
</div>
@endsection
