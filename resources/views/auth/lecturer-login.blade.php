@extends('layouts.guest')
@section('title', 'Lecturer sign in')
@section('icon', 'person-workspace')
@section('heading', 'Lecturer sign in')
@section('subheading', 'Sign in with your lecturer username and password.')

@section('content')
<form method="POST" action="{{ route('lecturer.login.submit') }}">
    @csrf
    <div class="mb-3 field-icon">
        <i class="bi bi-person"></i>
        <input type="text" name="username" value="{{ old('username') }}" class="form-control" placeholder="Username" autocomplete="username" required autofocus>
    </div>
    <div class="mb-3 field-icon">
        <i class="bi bi-lock"></i>
        <input type="password" name="password" id="lecturerPassword" class="form-control has-eye" placeholder="Password" autocomplete="current-password" required>
        <i class="bi bi-eye toggle-eye" data-toggle-password="lecturerPassword"></i>
    </div>
    <button type="submit" class="btn-navy"><i class="bi bi-box-arrow-in-right"></i> Sign in</button>
</form>
@endsection
