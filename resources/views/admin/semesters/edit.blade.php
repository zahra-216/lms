@extends('layouts.admin')
@section('title')
Edit Semester
@endsection
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Semester</h3>
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
        <form method="POST" action="{{ route('admin.semesters.update', $semester->id) }}">
            @csrf
            @method('PUT')

            <label class="form-label">Course</label>
            <select name="course_id" class="form-select" required>
                <option value="">Select Course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ $semester->course_id == $course->id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>

            <label class="form-label">Semester Name</label>
            <input type="text" name="name" class="form-control" placeholder="Semester 1 / Semester 2" value="{{ $semester->name }}" required>

            <button type="submit" class="btn-navy">
                <i class="bi bi-check-circle"></i> Update Semester
            </button>
        </form>
    </div>
</div>
@endsection
