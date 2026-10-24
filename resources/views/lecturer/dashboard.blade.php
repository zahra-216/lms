@extends('layouts.lecturer')
@section('title')
Dashboard
@endsection
@section('no_back', '1')

@push('styles')
<style>
    .faculty-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:22px; }
    .faculty-card { display:block; background:#fff; border:1px solid var(--line); border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow-card); text-decoration:none; color:inherit; height:100%; transition:transform .2s, box-shadow .2s; }
    .faculty-card:hover { transform:translateY(-4px); box-shadow:var(--shadow-lift); color:inherit; }
    .faculty-card-img { height:150px; background:var(--sky-soft); overflow:hidden; }
    .faculty-card-img img { width:100%; height:100%; object-fit:cover; display:block; }
    .faculty-card-body { padding:18px 20px 20px; }
    .faculty-card-body h3 { margin:0 0 6px; font-size:17px; font-weight:800; }
    .faculty-card-body p { margin:0 0 12px; font-size:13.5px; color:var(--muted); display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .faculty-card-cta { font-size:13.5px; font-weight:700; color:var(--brand-700); display:inline-flex; align-items:center; gap:6px; }
</style>
@endpush

@section('content')
@php
    $unreadChats = $lecturer->chatMessages()->where('sender_type', 'student')->where('is_read', false)->count();
    $unreadNotes = $lecturer->unreadNotifications->count();
@endphp
<div class="container">
    <div class="page-header">
        <div>
            <h2>Hello, {{ $lecturer->name }}!</h2>
            <small>Pick a faculty below to open its courses and start your teaching activities.</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('lecturer.lecture-records.index') }}" class="add-btn"><i class="bi bi-journal-text"></i> Lecture records</a>
            <a href="{{ route('lecturer.my.payments') }}" class="add-btn"><i class="bi bi-wallet2"></i> My payments</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('lecturer.chat.index') }}" class="stat-card d-block text-decoration-none">
                <div class="stat-icon"><i class="bi bi-chat-dots"></i></div>
                <div class="stat-value">{{ $unreadChats }}</div>
                <div class="stat-label">Unread student messages</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('lecturer.notifications.index') }}" class="stat-card d-block text-decoration-none">
                <div class="stat-icon"><i class="bi bi-bell"></i></div>
                <div class="stat-value">{{ $unreadNotes }}</div>
                <div class="stat-label">New notifications</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-building"></i></div>
                <div class="stat-value">{{ $faculties->count() }}</div>
                <div class="stat-label">Faculties</div>
            </div>
        </div>
    </div>

    <h3 class="section-title">All faculties</h3>
    <div class="faculty-grid">
        @foreach ($faculties as $faculty)
            @php
                $rel = 'storage/faculty/'.$faculty->image;
                $imageUrl = ($faculty->image && file_exists(public_path($rel))) ? asset($rel) : asset('images/ttmc.jpeg');
            @endphp
            <a href="{{ route('faculty.courses', ['facultyId' => $faculty->id]) }}" class="faculty-card">
                <div class="faculty-card-img"><img src="{{ $imageUrl }}" alt="{{ $faculty->name }}"></div>
                <div class="faculty-card-body">
                    <h3>{{ $faculty->name }}</h3>
                    <p>{{ $faculty->description ?? 'Browse the courses offered by this faculty.' }}</p>
                    <span class="faculty-card-cta">View courses <i class="bi bi-arrow-right"></i></span>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
