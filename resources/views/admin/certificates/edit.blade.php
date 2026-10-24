@extends('layouts.admin')
@section('title')
Edit Certificate
@endsection
@section('back', route('admin.certificates.student', $student->id))
@push('styles')
<style>
label { font-weight:600; font-size:13.5px; margin-bottom:6px; display:block;
}
.required::after { content:" *"; color:#dc2626;
}
.current-photo { width:80px; height:80px; border-radius:10px; object-fit:cover; border:2px solid #e2e8f0; margin-bottom:10px;
}
.submit-btn { background:#012147; color:#fff; border:none; padding:12px 26px; border-radius:10px; font-weight:600; width:100%;
}
.submit-btn:hover { background:#1e3a6e;
}
.row-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px;
}
@media (max-width:576px) {
.row-2 { grid-template-columns:1fr;
}
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h2><i class="bi bi-pencil-square"></i> Edit Certificate</h2>
        <small>{{ $student->name }} - {{ $student->registration_no }}</small>
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
        <form method="POST" action="{{ route('admin.certificates.update', $certificate->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label class="required">Certificate Number</label>
            <input type="text" name="certificate_number" class="form-control" value="{{ old('certificate_number', $certificate->certificate_number) }}" required>

            <div class="row-2">
                <div>
                    <label class="required">Student Name</label>
                    <input type="text" name="student_name" class="form-control" value="{{ old('student_name', $certificate->student_name) }}" required>
                </div>
                <div>
                    <label class="required">Father's Name</label>
                    <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $certificate->father_name) }}" required>
                </div>
            </div>

            <label class="required">Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $certificate->date_of_birth->format('Y-m-d')) }}" required>

            <label class="required">Course</label>
            <input type="text" name="course" class="form-control" value="{{ old('course', $certificate->course) }}" required>

            <div class="row-2">
                <div>
                    <label class="required">Course Start</label>
                    <input type="date" name="course_start" class="form-control" value="{{ old('course_start', $certificate->course_start->format('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="required">Course End</label>
                    <input type="date" name="course_end" class="form-control" value="{{ old('course_end', $certificate->course_end->format('Y-m-d')) }}" required>
                </div>
            </div>

            <label class="required">Award Status</label>
            <select name="award_status" class="form-select" required>
                <option value="Distinction" {{ old('award_status', $certificate->award_status)=='Distinction'?'selected':'' }}>Distinction</option>
                <option value="Merit" {{ old('award_status', $certificate->award_status)=='Merit'?'selected':'' }}>Merit</option>
                <option value="Pass" {{ old('award_status', $certificate->award_status)=='Pass'?'selected':'' }}>Pass</option>
            </select>

            @if($certificate->photo)
                <label>Current Photo</label><br>
                <img src="{{ asset('storage/'.$certificate->photo) }}" class="current-photo"><br>
            @endif
            <label>Replace Photo (leave empty to keep current)</label>
            <input type="file" name="photo" class="form-control" accept="image/*">

            <button type="submit" class="submit-btn"><i class="bi bi-check-circle"></i> Update Certificate</button>
        </form>
    </div>
</div>
@endsection
