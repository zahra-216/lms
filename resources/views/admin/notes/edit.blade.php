@extends('layouts.admin')
@section('title')
Edit Note - {{ $note->title }}
@endsection
@push('styles')
<style>
textarea { border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px;
}
.btn-download { background:#10b981; color:#fff; border:none; padding:8px 16px; border-radius:8px; text-decoration:none; font-size:13px; font-weight:600;
}
.btn-download:hover { background:#059669; color:#fff;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Note</h3>
        <small>{{ $note->title }}</small>
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
        <form action="{{ route('admin.subjects.notes.update', [$subject->id, $note->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{{ old('title',$note->title) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description',$note->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                    @foreach(['document','video','link','text','image'] as $type)
                        <option value="{{ $type }}" {{ $note->type==$type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
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
                    <a href="{{ route('admin.subjects.notes.download', [$subject->id, $note->id]) }}" class="btn-download">
                        <i class="bi bi-download"></i> Download Existing File
                    </a>
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Or URL</label>
                <input type="text" name="url" value="{{ old('url',$note->url) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Order</label>
                <input type="number" name="order" value="{{ old('order',$note->order) }}" class="form-control">
            </div>

            <div class="mb-4">
                <label class="form-label">Published</label>
                <select name="is_published" class="form-select">
                    <option value="1" {{ $note->is_published ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ !$note->is_published ? 'selected' : '' }}>No</option>
                </select>
            </div>

            <button type="submit" class="btn-navy">Update Note</button>
        </form>
    </div>
</div>
@endsection
