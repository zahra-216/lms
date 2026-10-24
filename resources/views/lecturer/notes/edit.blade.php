@extends('layouts.lecturer')
@section('title')
Edit Note
@endsection
@section('back', route('lecturer.subject.notes', $subject->id))
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
    

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Note</h3>
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
                    @foreach(['document','video','link','text','image'] as $t)
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

            <button class="btn btn-navy w-100">Update Note</button>
        </form>
    </div>
</div>
@endsection
