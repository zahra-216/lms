@extends('layouts.lecturer')
@section('title')
Edit Video
@endsection
@push('styles')
<style>
.form-check-input { width:18px; height:18px; cursor:pointer;
}
.form-check-label { cursor:pointer;
}
</style>
@endpush
@section('content')
<div class="container">
    <a href="{{ route('lecturer.subject.videos', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Lecture Video</h3>
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
        <form action="{{ route('lecturer.videos.update', [$subject->id, $video->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ $video->title }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control">{{ $video->description }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Video Source</label>
                <select name="type" id="videoType" class="form-select" required onchange="toggleFields()">
                    <option value="link" {{ $video->type == 'link' ? 'selected' : '' }}>Link</option>
                    <option value="file" {{ $video->type == 'file' ? 'selected' : '' }}>Upload MP4 File</option>
                </select>
            </div>

            <div class="mb-3" id="linkField" style="display:{{ $video->type == 'link' ? 'block' : 'none' }};">
                <label class="form-label">Video URL</label>
                <input type="text" name="video_url" class="form-control" value="{{ $video->video_url }}">
            </div>

            <div class="mb-3" id="fileField" style="display:{{ $video->type == 'file' ? 'block' : 'none' }};">
                <label class="form-label">Replace Video File (optional)</label>
                <input type="file" name="video_file" class="form-control" accept="video/*">
                @if($video->video_path)
                    <small class="text-muted">Current: {{ basename($video->video_path) }}</small>
                @endif
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="is_published" value="1" class="form-check-input" id="isPublished" {{ $video->is_published ? 'checked' : '' }}>
                <label class="form-check-label" for="isPublished">Published</label>
            </div>

            <button class="btn btn-navy w-100">Update Video</button>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function toggleFields() {
    const type = document.getElementById('videoType').value;
    document.getElementById('linkField').style.display = type === 'link' ? 'block' : 'none';
    document.getElementById('fileField').style.display = type === 'file' ? 'block' : 'none';
}
</script>
@endpush
