@extends('layouts.guest')
@section('title', 'Forgot password')
@section('icon', 'key')
@section('heading', 'Forgot your password?')
@section('subheading', "Enter your email address and we'll send you a link to choose a new one.")

@section('content')
<form method="POST" action="{{ route('admin.password.email') }}">
    @csrf
    <div class="mb-3 field-icon">
        <i class="bi bi-envelope"></i>
        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email address" required autofocus>
    </div>
    <button type="submit" class="btn-navy"><i class="bi bi-send"></i> Send reset link</button>
</form>
@endsection

@section('footer')
    <a href="{{ route('admin.login') }}"><i class="bi bi-arrow-left"></i> Back to sign in</a>
@endsection
