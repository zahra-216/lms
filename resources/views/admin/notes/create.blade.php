<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Note - {{ $subject->name }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    background: #f0f2f5;
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px;
}

.card-form {
    background: #fff;
    border-radius: 12px;
    padding: 25px 20px;
    max-width: 500px;
    width: 100%;
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    transition: 0.3s;
}

.card-form:hover {
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}

.card-form h1 {
    font-size: 1.6rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 20px;
    color: #0d1b3d;
}

.errors {
    background: #ffe2e6;
    color: #b71c1c;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 0.9rem;
}

.form-label {
    font-weight: 600;
    margin-bottom: 4px;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #d1d5db;
    padding: 6px 10px;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.form-control:focus, .form-select:focus {
    border-color: #0d1b3d;
    box-shadow: 0 0 0 2px rgba(13,27,61,0.1);
}

.btn-modern {
    background: #0d1b3d;
    color: #fff;
    font-weight: 600;
    padding: 8px 20px;
    border-radius: 8px;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
    justify-content: center;
}

.btn-modern:hover {
    background: #061031;
    transform: translateY(-1px);
}

.mb-3 {
    margin-bottom: 12px !important;
}

@media (max-width: 576px) {
    .card-form {
        padding: 20px 15px;
    }
}
</style>
</head>
<body>

<div class="card-form">
    <h1><i class="fa fa-sticky-note me-1"></i>Add Note: {{ $subject->name }}</h1>

    @if($errors->any())
    <div class="errors">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li><i class="fa fa-exclamation-circle me-1"></i>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.subjects.notes.store', $subject->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" class="form-control" required placeholder="Enter note title">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" placeholder="Enter note description" rows="2">{{ old('description') }}</textarea>
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

        <div class="mb-3">
            <label class="form-label">Published</label>
            <select name="is_published" class="form-select">
                <option value="1" selected>Yes</option>
                <option value="0">No</option>
            </select>
        </div>

        <!-- Centered submit button -->
        <div class="text-center">
            <button type="submit" class="btn-modern"><i class="fa fa-rocket me-1"></i> Add Note</button>
        </div>

    </form>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>