@extends('layouts.guest')
@section('title')
Student sign in
@endsection
@section('icon', 'mortarboard')
@section('heading', 'Student sign in')
@section('subheading', 'Welcome back! Sign in with your registration number to open your classes.')

@section('content')
<form method="POST" action="{{ route('login.submit') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label" for="registration_no">Registration number</label>
        <div class="field-icon">
            <i class="bi bi-person-badge"></i>
            <input type="text" id="registration_no" name="registration_no" value="{{ old('registration_no') }}" class="form-control" placeholder="e.g. TTMC/ML/UG/CSE/25/006" autocomplete="username" required autofocus>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label" for="studentPassword">Password</label>
        <div class="field-icon">
            <i class="bi bi-lock"></i>
            <input type="password" id="studentPassword" name="password" class="form-control has-eye" placeholder="Enter your password" autocomplete="current-password" required>
            <i class="bi bi-eye toggle-eye" data-toggle-password="studentPassword"></i>
        </div>
    </div>
    <button type="submit" class="btn-navy"><i class="bi bi-box-arrow-in-right"></i> Sign in</button>
</form>
@endsection

@section('footer')
    Lecturer? <a href="{{ route('lecturer.login') }}">Sign in here</a>
@endsection
