@extends('layouts.admin')
@section('title')
Edit Faculty
@endsection
@section('back', route('admin.faculties.index'))
@push('styles')
<style>
.preview-img { width:100px; height:100px; border-radius:50%; object-fit:cover; border:3px solid #012147; margin:0 auto 20px; display:block;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Faculty</h3>
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
        @php
            $img = $faculty->image
                ? asset('storage/faculty/'.$faculty->image)
                : 'https://via.placeholder.com/100';
        @endphp
        <img src="{{ $img }}" class="preview-img">

        <form action="{{ route('admin.faculties.update', $faculty->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label class="form-label">Faculty Name</label>
            <input type="text" name="name" class="form-control" value="{{ $faculty->name }}" required>

            <label class="form-label">Faculty Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">

            <button type="submit" class="btn-navy">Update Faculty</button>
        </form>
    </div>
</div>
@endsection
