@extends('layouts.guest')
@section('title', 'Admin sign in')
@section('icon', 'shield-lock')
@section('heading', 'Admin sign in')
@section('subheading', 'Sign in to manage students, lecturers, courses and records.')

@section('content')
<form method="POST" action="{{ route('admin.login.submit') }}">
    @csrf
    <div class="mb-3 field-icon">
        <i class="bi bi-envelope"></i>
        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email address" autocomplete="username" required autofocus>
    </div>
    <div class="mb-3 field-icon">
        <i class="bi bi-lock"></i>
        <input type="password" name="password" id="adminPassword" class="form-control has-eye" placeholder="Password" autocomplete="current-password" required>
        <i class="bi bi-eye toggle-eye" data-toggle-password="adminPassword"></i>
    </div>
    <button type="submit" class="btn-navy"><i class="bi bi-box-arrow-in-right"></i> Sign in</button>
</form>
@endsection

@section('footer')
    Forgot your password? <a href="{{ route('admin.password.request') }}">Reset it here</a>
@endsection
