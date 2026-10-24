@extends('layouts.public')
@section('title')
Certificate verified
@endsection

@push('styles')
<style>
    .result-row { display:flex; justify-content:space-between; gap:16px; padding:12px 0; border-bottom:1px solid var(--line); font-size:14.5px; }
    .result-row:last-child { border-bottom:0; }
    .result-row .label { color:var(--muted); font-weight:600; }
    .result-row .value { font-weight:700; color:var(--navy-900); text-align:right; }
    .status-badge { display:inline-block; padding:4px 12px; border-radius:999px; font-size:12.5px; font-weight:700; text-transform:capitalize; background:#dcfce7; color:#15803d; }
</style>
@endpush

@section('content')
<div class="pub-wrap" style="max-width:620px;margin-top:48px">
    <div class="card-box p-0 overflow-hidden">
        <div class="text-center text-white py-4" style="background:#047857">
            <i class="bi bi-check-circle-fill" style="font-size:44px"></i>
            <h1 class="h4 fw-bold mt-2 mb-0 text-white">Certificate verified</h1>
        </div>
        <div class="p-4">
            @if ($certificate->photo)
                <div class="text-center mb-3"><img src="{{ asset('storage/'.$certificate->photo) }}" alt="" draggable="false" style="width:130px;height:130px;object-fit:cover;border-radius:16px;border:3px solid var(--line)"></div>
            @endif
            <div class="result-row"><span class="label">Certificate number</span><span class="value">{{ $certificate->certificate_number }}</span></div>
            <div class="result-row"><span class="label">Student name</span><span class="value">{{ $certificate->student_name }}</span></div>
            <div class="result-row"><span class="label">Father's name</span><span class="value">{{ $certificate->father_name }}</span></div>
            <div class="result-row"><span class="label">Date of birth</span><span class="value">{{ $certificate->date_of_birth->format('d M Y') }}</span></div>
            <div class="result-row"><span class="label">Course</span><span class="value">{{ $certificate->course }}</span></div>
            <div class="result-row"><span class="label">Course start</span><span class="value">{{ $certificate->course_start->format('d M Y') }}</span></div>
            <div class="result-row"><span class="label">Course end</span><span class="value">{{ $certificate->course_end->format('d M Y') }}</span></div>
            <div class="result-row"><span class="label">Award status</span><span class="value"><span class="status-badge">{{ $certificate->award_status }}</span></span></div>
            <p class="text-center text-muted small mt-3 mb-0">This is an official verification result issued by TT Metro Campus.</p>
        </div>
    </div>
    <p class="text-center mt-3"><a href="{{ route('verify.show') }}"><i class="bi bi-arrow-left"></i> Verify another certificate</a></p>
</div>
@endsection
