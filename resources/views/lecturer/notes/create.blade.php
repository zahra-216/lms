@extends('layouts.lecturer')
@section('title')
Add Note
@endsection
@section('back', route('lecturer.subject.notes', $subject->id))
@section('content')
<div class="container">
    

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
                    <option value="document">Document</option>
                    <option value="video">Video</option>
                    <option value="link">Link</option>
                    <option value="text">Text</option>
                    <option value="image">Image</option>
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

            <button class="btn btn-navy w-100">Save Note</button>
        </form>
    </div>
</div>
@endsection
