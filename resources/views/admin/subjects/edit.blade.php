@extends('layouts.admin')
@section('title')
Edit subject
@endsection
@section('back', route('admin.faculties.courses', optional($subject->course)->faculty_id ?? 0))

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-book"></i> Edit subject</h2>
            <small>{{ $subject->course->name ?? '' }} &middot; {{ $subject->level->name ?? '' }} &middot; {{ $subject->semester->name ?? '' }}</small>
        </div>
    </div>

    <div class="card-box">
        <form method="POST" action="{{ route('admin.subjects.update', $subject->id) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-3">
                <label class="form-label" for="code">Code</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code', $subject->code) }}" required>
            </div>
            <div class="col-md-7">
                <label class="form-label" for="name">Subject name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $subject->name) }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="credits">Credits</label>
                <input type="number" id="credits" name="credits" class="form-control" min="0" value="{{ old('credits', $subject->credits) }}">
            </div>
            <div class="col-12">
                <button type="submit" class="btn-navy"><i class="bi bi-check2-circle"></i> Save changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
