<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Video</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }

    @media (max-width:576px){
        body { padding:20px 12px; }
    }

    .container { max-width:600px; margin:auto; }

    .back-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .back-btn:hover{ background:#012147; color:#fff; }

    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:24px 28px; margin:18px 0 26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h3{ margin:0; font-weight:700; font-size:20px; }

    .card-box{
        background:#fff; padding:26px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06);
    }

    .form-label{ font-weight:600; color:#012147; font-size:14px; }
    .form-control, .form-select{ border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px; }
    .form-control:focus, .form-select:focus{ border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }

    .form-check-input{ width:18px; height:18px; cursor:pointer; }
    .form-check-label{ cursor:pointer; }

    .btn-navy{ background:#012147; color:#fff; border:none; padding:12px; font-weight:600; border-radius:10px; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }
</style>
</head>
<body>
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

<script>
function toggleFields() {
    const type = document.getElementById('videoType').value;
    document.getElementById('linkField').style.display = type === 'link' ? 'block' : 'none';
    document.getElementById('fileField').style.display = type === 'file' ? 'block' : 'none';
}
</script>
</body>
</html>