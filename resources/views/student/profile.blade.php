@extends('layouts.student')
@section('title')
My Profile
@endsection
@section('back', route('dashboard'))

@push('styles')
<style>
    .avatar-wrap { position:relative; width:128px; height:128px; margin:0 auto 12px; display:block; cursor:pointer; }
    .avatar-wrap img, .avatar-wrap .avatar-fallback { width:128px; height:128px; border-radius:50%; object-fit:cover; border:4px solid rgba(255,255,255,.6); background:#fff; }
    .avatar-fallback { display:grid; place-items:center; font-size:42px; font-weight:800; color:var(--brand-700); }
    .camera-badge { position:absolute; right:4px; bottom:4px; width:36px; height:36px; border-radius:50%; background:var(--brand-700); color:#fff; display:grid; place-items:center; border:3px solid #fff; }
    .info-box { display:flex; align-items:center; gap:14px; padding:16px; border:1px solid var(--line); border-radius:14px; background:var(--surface); height:100%; }
    .info-box > i { width:44px; height:44px; border-radius:12px; background:var(--sky-soft); color:var(--brand-700); display:grid; place-items:center; font-size:20px; flex-shrink:0; }
    .info-box small { color:var(--muted); font-weight:600; }
    .info-box h6 { margin:2px 0 0; font-size:15px; font-weight:700; color:var(--navy-900); word-break:break-word; }
</style>
@endpush

@section('content')
@php
    $initials = collect(preg_split('/\s+/', trim($student->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $online = $student->last_seen_at && \Carbon\Carbon::parse($student->last_seen_at)->diffInMinutes() < 5;
@endphp
<div class="container">
    <div class="page-header flex-column text-center justify-content-center py-4">
        <form id="photoForm" action="{{ route('student.photo.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="photoInput" class="avatar-wrap" title="Change photo">
                @if ($student->photo)
                    <img id="previewImage" src="{{ asset('storage/'.$student->photo) }}" alt="{{ $student->name }}">
                @else
                    <span id="previewFallback" class="avatar-fallback">{{ $initials }}</span>
                    <img id="previewImage" src="" alt="" hidden>
                @endif
                <span class="camera-badge"><i class="bi bi-camera-fill"></i></span>
            </label>
            <input type="file" name="photo" id="photoInput" accept="image/png, image/jpeg" hidden>
            <small class="d-block">Click the photo to change it (JPG or PNG, up to 2 MB)</small>
        </form>
        <h2 class="mt-3 mb-0">{{ $student->name }}</h2>
        <small>{{ $student->registration_no }}</small>
        <div class="mt-2"><span class="badge {{ $online ? 'bg-success' : 'bg-secondary' }}">{{ $online ? 'Online' : 'Offline' }}</span></div>
    </div>

    <div class="card-box">
        <div class="row g-3">
            @foreach ([
                ['envelope', 'Email', $student->email ?? 'N/A'],
                ['diagram-3', 'Branch', $student->branch ?? 'N/A'],
                ['book', 'Course', $student->course->name ?? 'N/A'],
                ['layers', 'Level', $student->level->name ?? 'N/A'],
                ['clock', 'Last seen', $student->last_seen_at ? \Carbon\Carbon::parse($student->last_seen_at)->diffForHumans() : 'Offline'],
                ['calendar', 'Joined', optional($student->created_at)->format('d M Y') ?? 'N/A'],
            ] as [$icon, $label, $value])
                <div class="col-md-6">
                    <div class="info-box"><i class="bi bi-{{ $icon }}"></i><div><small>{{ $label }}</small><h6>{{ $value }}</h6></div></div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('student.profile.edit') }}" class="btn-navy"><i class="bi bi-pencil"></i> Edit profile</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('photoInput').addEventListener('change', function () {
    var file = this.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) { alert('Please choose a photo smaller than 2 MB.'); this.value = ''; return; }
    var img = document.getElementById('previewImage'), fb = document.getElementById('previewFallback');
    img.src = URL.createObjectURL(file); img.hidden = false; if (fb) fb.hidden = true;
    document.getElementById('photoForm').submit();
});
</script>
@endpush
