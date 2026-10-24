@extends('layouts.admin')
@section('title')
Add Recording
@endsection
@section('back', route('admin.subjects.recordings.index', $subject->id))
@push('styles')
<style>
.hint { font-size:12px; color:#6b7280; margin-top:6px;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-plus-circle"></i> Add Recording</h3>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-box">
        <form action="{{ route('admin.subjects.recordings.store', $subject->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Week 5 - Loops in Python" required>
            </div>
            <div class="mb-3">
                <label class="form-label">YouTube Link or Video ID</label>
                <input type="text" name="youtube_video_id" class="form-control" placeholder="https://youtube.com/watch?v=... or just the ID" required>
                <div class="hint">Paste the full YouTube link or just the video ID — either works.</div>
            </div>
            <button type="submit" class="btn-navy">Save Recording</button>
        </form>
    </div>
</div>
@endsection
