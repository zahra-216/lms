<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Note - {{ $subject->name }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:600px; margin:auto; }

    .back-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover { background:#012147; color:#fff; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:24px 28px; margin-bottom:26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h3 { margin:0; font-weight:700; font-size:20px; }
    .page-header small { opacity:0.85; }

    .card-box { background:#fff; border-radius:16px; padding:28px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }

    .form-label { font-weight:600; color:#012147; font-size:14px; margin-bottom:6px; }
    .form-control, .form-select, textarea { border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px; }
    .form-control:focus, .form-select:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }

    .btn-navy { width:100%; background:#012147; color:#fff; border:none; padding:12px; border-radius:12px; font-weight:600; }
    .btn-navy:hover { background:#0b2d5a; }

    @media (max-width:480px){ .card-box{ padding:20px; } }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.dashboard') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h3><i class="bi bi-file-earmark-plus"></i> Add Note</h3>
        <small>{{ $subject->name }}</small>
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
        <form action="{{ route('admin.subjects.notes.store', $subject->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" class="form-control" required placeholder="Enter note title">
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Enter note description">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" id="noteType" required>
                    <option value="document">Document</option>
                    <option value="video">Video</option>
                    <option value="link">Link</option>
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                </select>
            </div>

            <div class="mb-3 file-inputs">
                <label class="form-label">File Upload</label>
                <input type="file" name="file_path" class="form-control" id="fileInput">
            </div>

            <div class="mb-3 file-inputs">
                <label class="form-label">Or URL</label>
                <input type="text" name="url" value="{{ old('url') }}" class="form-control" placeholder="Enter URL if any" id="urlInput">
            </div>

            <div class="mb-3">
                <label class="form-label">Order</label>
                <input type="number" name="order" value="{{ old('order',0) }}" class="form-control" placeholder="Order number">
            </div>

            <div class="mb-4">
                <label class="form-label">Published</label>
                <select name="is_published" class="form-select">
                    <option value="1" selected>Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <button type="submit" class="btn-navy">Add Note</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const noteType = document.getElementById('noteType');
    const fileInput = document.getElementById('fileInput');
    const urlInput = document.getElementById('urlInput');

    function toggleInputs() {
        const type = noteType.value;
        if(type === 'video' || type === 'link'){
            fileInput.disabled = true;
            fileInput.closest('.mb-3').style.display = 'none';
            urlInput.disabled = false;
            urlInput.closest('.mb-3').style.display = 'block';
        } else if(type === 'document' || type === 'image'){
            fileInput.disabled = false;
            fileInput.closest('.mb-3').style.display = 'block';
            urlInput.disabled = true;
            urlInput.closest('.mb-3').style.display = 'none';
        } else {
            fileInput.disabled = true;
            fileInput.closest('.mb-3').style.display = 'none';
            urlInput.disabled = true;
            urlInput.closest('.mb-3').style.display = 'none';
        }
    }

    toggleInputs();
    noteType.addEventListener('change', toggleInputs);
});
</script>
</body>
</html>