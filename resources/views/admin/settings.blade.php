@extends('layouts.admin')
@section('title')
Settings
@endsection
@push('styles')
<style>
@keyframes fadeIn { from{opacity:0; transform:translateY(15px);} to{opacity:1; transform:translateY(0);} }
.icon-box { text-align:center; font-size:40px; color:#012147; margin-bottom:8px;
}
.card-box h2 { text-align:center; color:#012147; font-weight:700; margin-bottom:6px; font-size:22px;
}
.card-box .subtitle { text-align:center; color:#6b7280; font-size:13px; margin-bottom:24px;
}
.password-wrap { position:relative; margin-bottom:18px;
}
.toggle-eye { position:absolute; top:38px; right:14px; cursor:pointer; color:#64748b;
}
.alert { border-radius:12px; font-size:14px;
}
.back-link { display:block; text-align:center; margin-top:18px; font-size:13px; color:#012147; text-decoration:none;
}
.back-link:hover { text-decoration:underline;
}
</style>
@endpush
@section('content')
<div class="card-box">
    <div class="icon-box"><i class="bi bi-shield-lock-fill"></i></div>
    <h2>Change Password</h2>
    <div class="subtitle">Update your admin account password</div>

    
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf

        <label class="form-label">New Password</label>
        <div class="password-wrap">
            <input type="password" name="password" id="newPassword" class="form-control" required>
            <i class="bi bi-eye toggle-eye" onclick="togglePassword('newPassword', this)"></i>
        </div>

        <label class="form-label">Confirm Password</label>
        <div class="password-wrap">
            <input type="password" name="password_confirmation" id="confirmPassword" class="form-control" required>
            <i class="bi bi-eye toggle-eye" onclick="togglePassword('confirmPassword', this)"></i>
        </div>

        <button type="submit" class="btn-navy">Update Password</button>
    </form>

    
</div>
@endsection
@push('scripts')
<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endpush
