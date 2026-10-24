@extends('layouts.admin')
@section('title')
{{ $subject->name }} - Attendance
@endsection
@section('back', route('admin.attendance.index'))
@push('styles')
<style>
.action-card {
        background:#fff; border-radius:16px; padding:30px; text-align:center;
        box-shadow:0 8px 26px rgba(0,0,0,0.06); text-decoration:none; color:#012147;
        display:flex; flex-direction:column; align-items:center; gap:12px; transition:.15s;
        height:100%;
}
.action-card:hover { background:#012147; color:#fff; transform:translateY(-2px);
}
.action-card i { font-size:38px;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h2><i class="bi bi-calendar-check"></i> {{ $subject->code }} - {{ $subject->name }}</h2>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <a href="{{ route('admin.attendance.mark', $subject->id) }}" class="action-card">
                <i class="bi bi-clipboard2-check"></i>
                <div class="fw-bold fs-5">Mark Attendance</div>
                <div class="text-muted small" style="color:inherit; opacity:.75;">Mark today's or a past date's attendance</div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('admin.attendance.history', $subject->id) }}" class="action-card">
                <i class="bi bi-clock-history"></i>
                <div class="fw-bold fs-5">View History</div>
                <div class="text-muted small" style="color:inherit; opacity:.75;">Browse and download monthly reports</div>
            </a>
        </div>
    </div>
</div>
@endsection
