<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Note</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width:600px;">
    <a href="{{ route('lecturer.subject.notes', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h3>Add Note — {{ $subject->name }}</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('lecturer.notes.store', $subject->id) }}" method="POST" enctype="multipart/form-data">
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
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
                <option value="pdf">PDF</option>
                <option value="doc">Document</option>
                <option value="link">Link</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">File (optional)</label>
            <input type="file" name="file_path" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">URL (optional, if no file)</label>
            <input type="text" name="url" class="form-control" placeholder="https://...">
        </div>

        <button class="btn btn-primary w-100">Save Note</button>
    </form>
</div>
</body>
</html>