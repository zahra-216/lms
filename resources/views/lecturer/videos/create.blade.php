<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Video</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width:600px;">
    <a href="{{ route('lecturer.subject.videos', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h3>Add Lecture Video — {{ $subject->name }}</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('lecturer.videos.store', $subject->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Video Source</label>
            <select name="type" id="videoType" class="form-select" required onchange="toggleFields()">
                <option value="link">Link (YouTube / Google Drive / etc.)</option>
                <option value="file">Upload MP4 File</option>
            </select>
        </div>

        <div class="mb-3" id="linkField">
            <label class="form-label">Video URL</label>
            <input type="text" name="video_url" class="form-control" placeholder="https://youtube.com/...">
        </div>

        <div class="mb-3" id="fileField" style="display:none;">
            <label class="form-label">Video File (mp4, mov, avi, mkv — max 100MB)</label>
            <input type="file" name="video_file" class="form-control" accept="video/*">
        </div>

        <button class="btn btn-primary w-100">Save Video</button>
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