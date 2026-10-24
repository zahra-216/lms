@extends('layouts.student')
@section('title')
Edit Profile
@endsection
@section('content')
<div class="container mt-5" style="max-width:500px;">
    <a href="{{ route('student.profile') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h3>Edit Profile</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('student.profile.update') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="{{ $student->name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $student->email }}">
        </div>

        <button class="btn btn-primary w-100">Save Changes</button>
    </form>
</div>
@endsection
