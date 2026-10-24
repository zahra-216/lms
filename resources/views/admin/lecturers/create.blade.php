@extends('layouts.admin')
@section('title')
Create Lecturer
@endsection
@section('back', route('admin.lecturers.index'))
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-person-plus"></i> Create Lecturer</h3>
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
        <form action="{{ route('admin.lecturers.store') }}" method="POST" autocomplete="off">
            @csrf

            <input type="text" name="fake_username" style="display:none">
            <input type="password" name="fake_password" style="display:none">

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autocomplete="off" readonly onfocus="this.removeAttribute('readonly')">
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" required autocomplete="off">
            </div>

            <div class="mb-3">
                <label class="form-label">Email (optional)</label>
                <input type="email" name="email" class="form-control" autocomplete="off">
            </div>

            <div class="mb-4" style="position:relative;">
                <label class="form-label">Password</label>
                <input type="password" name="password" id="lecturerPassword" class="form-control" required autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')" style="padding-right:42px;">
                <i class="bi bi-eye" onclick="togglePassword('lecturerPassword', this)"
                   style="position:absolute; top:38px; right:14px; cursor:pointer; color:#64748b;"></i>
            </div>

            <button type="submit" class="btn-navy">Save Lecturer</button>
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
