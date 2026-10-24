@extends('layouts.admin')
@section('title')
Edit Course
@endsection
@section('back', route('admin.courses.index'))
@push('styles')
<style>
textarea { border-radius:10px; border:1px solid #e2e8f0; padding:11px 14px; width:100%; margin-bottom:18px;
}
textarea { resize:vertical; min-height:100px;
}
.preview { max-height:120px; border-radius:8px; margin-bottom:18px; display:block;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Course</h3>
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
        <form method="POST" action="{{ route('admin.courses.update', $course->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label class="form-label">Course Code</label>
            <input type="text" name="code" class="form-control" value="{{ old('code',$course->code) }}" required>

            <label class="form-label">Course Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name',$course->name) }}" required>

            <label class="form-label">Faculty</label>
            <select name="faculty_id" class="form-select" required>
                <option value="">Select Faculty</option>
                @foreach($faculties as $faculty)
                    <option value="{{ $faculty->id }}" {{ (old('faculty_id',$course->faculty_id)==$faculty->id)?'selected':'' }}>
                        {{ $faculty->name }}
                    </option>
                @endforeach
            </select>

            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" placeholder="Description">{{ old('description',$course->description) }}</textarea>

            <label class="form-label">Course Image (optional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            @if($course->image)
                <img src="{{ asset('storage/courses/'.$course->image) }}" class="preview" alt="{{ $course->name }}">
            @endif

            <button type="submit" class="btn-navy">Update Course</button>
        </form>
    </div>
</div>
@endsection
