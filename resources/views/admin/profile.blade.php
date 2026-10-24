@extends('layouts.admin')
@section('title')
Admin Profile
@endsection
@push('styles')
<style>
.card-box h2 { text-align:center; color:#012147; font-weight:700; margin-bottom:20px;
}
.logo { display:block; margin:0 auto 20px; width:120px;
}
</style>
@endpush
@section('content')
<div class="card-box">
    <h2>Admin Profile</h2>
    <img src="{{ asset('images/logo.png.jpeg') }}" class="logo" alt="Logo">

    
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
        @csrf

        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="{{ $admin->name }}" required>

        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ $admin->email }}" required>

        <label class="form-label">Profile Image</label>
        <input type="file" name="photo" class="form-control">

        <button type="submit" class="btn-navy">Update Profile</button>
    </form>
</div>
@endsection
