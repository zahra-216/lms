<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Video</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width:600px;">
    <a href="{{ route('lecturer.subject.videos', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h3>Edit Lecture Video</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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

        <button class="btn btn-primary w-100">Update Video</button>
    </form>
</div>

<script>
function toggleFields() {
    const type = document.getElementById('videoType').value;
    document.getElementById('linkField').style.display = type === 'link' ? 'block' : 'none';
    document.getElementById('fileField').style.display = type === 'file' ? 'block' : 'none';
}
</script>
</body>
</html>