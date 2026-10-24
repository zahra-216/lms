@extends('layouts.guest')
@section('title', 'Reset password')
@section('icon', 'shield-lock')
@section('heading', 'Choose a new password')
@section('subheading', 'Use at least 8 characters.')

@section('content')
<form method="POST" action="{{ url('/admin/reset-password') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-3 field-icon">
        <i class="bi bi-lock"></i>
        <input type="password" name="password" id="resetPassword" class="form-control has-eye" placeholder="New password" autocomplete="new-password" required autofocus>
        <i class="bi bi-eye toggle-eye" data-toggle-password="resetPassword"></i>
    </div>
    <div class="mb-3 field-icon">
        <i class="bi bi-lock-fill"></i>
        <input type="password" name="password_confirmation" id="resetConfirmPassword" class="form-control has-eye" placeholder="Confirm new password" autocomplete="new-password" required>
        <i class="bi bi-eye toggle-eye" data-toggle-password="resetConfirmPassword"></i>
    </div>
    <button type="submit" class="btn-navy"><i class="bi bi-check2-circle"></i> Reset password</button>
</form>
@endsection
