@extends('layouts.admin')
@section('title')
Add Note - {{ $subject->name }}
@endsection
@push('styles')
<style>
textarea { border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px;
}
</style>
@endpush
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
@endsection
@push('scripts')
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
@endpush
