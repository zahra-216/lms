<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Note</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width:600px;">
    <a href="{{ route('lecturer.subject.notes', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h3>Edit Note</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('lecturer.notes.update', [$subject->id, $note->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ $note->title }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ $note->description }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
                @foreach(['pdf','doc','link','other'] as $t)
                    <option value="{{ $t }}" {{ $note->type == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Replace File (optional)</label>
            <input type="file" name="file_path" class="form-control">
            @if($note->file_path)
                <small class="text-muted">Current: {{ basename($note->file_path) }}</small>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">URL</label>
            <input type="text" name="url" class="form-control" value="{{ $note->url }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Order</label>
            <input type="number" name="order" class="form-control" value="{{ $note->order }}">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="is_published" value="1" class="form-check-input" id="isPublished" {{ $note->is_published ? 'checked' : '' }}>
            <label class="form-check-label" for="isPublished">Published</label>
        </div>

        <button class="btn btn-primary w-100">Update Note</button>
    </form>
</div>
</body>
</html>