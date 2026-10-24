@extends('layouts.admin')
@section('title')
Edit Lecturer
@endsection
@section('back', route('admin.lecturers.index'))
@push('styles')
<style>
.password-wrap { position:relative;
}
.password-wrap .form-control { padding-right:42px;
}
.toggle-eye { position:absolute; top:38px; right:14px; cursor:pointer; color:#64748b;
}
.hint { font-size:12px; color:#6b7280; margin-top:-8px; margin-bottom:16px;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Lecturer</h3>
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
        <div class="text-center mb-4" style="padding-bottom:20px; border-bottom:1px solid #e2e8f0;">
            <img src="{{ route('admin.lecturers.qr', $lecturer->id) }}" alt="QR Code" style="width:180px; border-radius:10px;">
            <div class="mt-2">
                <a href="{{ route('admin.lecturers.qr', $lecturer->id) }}" download="{{ $lecturer->username }}-qr.svg" class="back-btn" style="margin:0; display:inline-flex;">
                    <i class="bi bi-download"></i> Download QR
                </a>
            </div>
        </div>

        <form action="{{ route('admin.lecturers.update', $lecturer->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="{{ $lecturer->username }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ $lecturer->name }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $lecturer->email }}">
            </div>

            <div class="mb-3 password-wrap">
                <label class="form-label">New Password</label>
                <input type="password" name="password" id="lecturerNewPassword" class="form-control" placeholder="Leave blank to keep unchanged">
                <i class="bi bi-eye toggle-eye" onclick="togglePassword('lecturerNewPassword', this)"></i>
            </div>

            <div class="mb-2 password-wrap">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="lecturerConfirmPassword" class="form-control">
                <i class="bi bi-eye toggle-eye" onclick="togglePassword('lecturerConfirmPassword', this)"></i>
            </div>
            <div class="hint">Only fill this in if you want to reset the lecturer's password.</div>

            <button type="submit" class="btn-navy">Update Lecturer</button>
        </form>
    </div>
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
