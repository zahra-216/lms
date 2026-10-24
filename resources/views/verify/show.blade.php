@extends('layouts.public')
@section('title')
Certificate verification
@endsection

@section('content')
<div class="pub-wrap" style="max-width:520px;margin-top:48px">
    <div class="card-box" style="padding:34px">
        <div class="text-center mb-3">
            <div class="stat-icon mx-auto" style="width:64px;height:64px;font-size:30px"><i class="bi bi-patch-check-fill"></i></div>
            <h1 class="h3 fw-bold mt-3 mb-1">Certificate verification</h1>
            <p class="text-muted">Enter the registration number and certificate number to confirm that a TT Metro Campus certificate is genuine.</p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger"><i class="bi bi-exclamation-octagon"></i><div>{{ session('error') }}</div></div>
        @endif

        <form method="POST" action="{{ route('verify.check') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="registration_no">Registration number</label>
                <input type="text" id="registration_no" name="registration_no" class="form-control" value="{{ old('registration_no') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="certificate_number">Certificate number</label>
                <input type="text" id="certificate_number" name="certificate_number" class="form-control" value="{{ old('certificate_number') }}" required>
            </div>
            <button type="submit" class="btn-navy w-100 justify-content-center py-3"><i class="bi bi-search"></i> Verify</button>
        </form>
    </div>
</div>
@endsection
