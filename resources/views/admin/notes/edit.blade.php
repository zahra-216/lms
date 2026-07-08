<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Note - {{ $note->title }}</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { font-family: 'Inter', sans-serif; background: #f4f6f9; }
h1 { text-align: center; margin: 30px 0; color: #021634; }
.card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.form-label { font-weight: 600; }
input, select, textarea { border-radius: 6px; }
textarea { min-height: 100px; }
.btn-primary { background: #021634; border: none; }
.btn-primary:hover { background: #012147; }
.errors { color: #dc3545; margin-bottom: 20px; }
</style>
</head>
<body>

<div class="container my-5">
    <div class="card p-4">
        <h1>Edit Note - {{ $note->title }}</h1>

        @if($errors->any())
        <div class="errors">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.subjects.notes.update', [$subject->id, $note->id]) }}" 
              method="POST" 
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" 
                       value="{{ old('title',$note->title) }}" 
                       class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" 
                          class="form-control">{{ old('description',$note->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                    @foreach(['document','video','link','text','image'] as $type)
                        <option value="{{ $type }}" 
                            {{ $note->type==$type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Upload New File</label>
                <input type="file" name="file_path" class="form-control">
            </div>

            @if($note->file_path)
            <div class="mb-3">
                <label class="form-label">Current File</label><br>
                <a href="{{ route('notes.download',$note->id) }}" 
                   class="btn btn-success btn-sm">
                   Download Existing File
                </a>
            </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Or URL</label>
                <input type="text" name="url" 
                       value="{{ old('url',$note->url) }}" 
                       class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Order</label>
                <input type="number" name="order" 
                       value="{{ old('order',$note->order) }}" 
                       class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Published</label>
                <select name="is_published" class="form-select">
                    <option value="1" {{ $note->is_published ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ !$note->is_published ? 'selected' : '' }}>No</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Update Note
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>