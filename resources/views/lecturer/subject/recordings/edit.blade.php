@extends('layouts.lecturer')
@section('title')
Edit Recording
@endsection
@section('back', route('lecturer.subject.recordings', $subject->id))
@push('styles')
<style>
.hint { font-size:12px; color:#6b7280; margin-top:6px;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Recording</h3>
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
        <form action="{{ route('lecturer.subject.recordings.update', [$subject->id, $recording->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ $recording->title }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">YouTube Link or Video ID</label>
                <input type="text" name="youtube_video_id" class="form-control" value="{{ $recording->youtube_video_id }}" required>
                <div class="hint">Paste the full YouTube link or just the video ID — either works.</div>
            </div>
            <button type="submit" class="btn-navy">Update Recording</button>
        </form>
    </div>
</div>
@endsection
