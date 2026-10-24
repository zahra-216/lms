@extends('layouts.admin')
@section('title')
Add Faculty
@endsection
@section('back', route('admin.faculties.index'))
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-person-badge"></i> Add Faculty</h3>
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
        <form action="{{ route('admin.faculties.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label class="form-label">Faculty Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter Faculty Name" required>

            <label class="form-label">Faculty Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">

            <button type="submit" class="btn-navy">Add Faculty</button>
        </form>
    </div>
</div>
@endsection
