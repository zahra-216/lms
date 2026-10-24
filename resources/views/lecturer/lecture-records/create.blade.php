@extends('layouts.lecturer')
@section('title')
Add Lecture Record
@endsection
@section('back', route('lecturer.lecture-records.index'))
@push('styles')
<style>
.form-control[readonly] { background:#f1f5f9; color:#64748b;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-journal-plus"></i> Add Lecture Record</h3>
        <small>Fill in the content you covered</small>
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
        <form action="{{ route('lecturer.lecture-records.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="form-label">Lecturer Name</label>
                <input type="text" class="form-control" value="{{ auth()->guard('lecturer')->user()->name }}" readonly>
            </div>

            <div class="mb-4">
                <label class="form-label">Content Covered</label>
                <textarea name="content_covered" class="form-control" rows="4" required>{{ old('content_covered') }}</textarea>
            </div>

            <button class="btn btn-navy w-100">Save Record</button>
        </form>
    </div>
</div>
@endsection
