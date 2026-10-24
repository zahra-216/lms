@extends('layouts.admin')
@section('title')
Add Certificate
@endsection
@section('back', route('admin.certificates.student', $student->id))
@push('styles')
<style>
label { font-weight:600; font-size:13.5px; margin-bottom:6px; display:block;
}
.required::after { content:" *"; color:#dc2626;
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
        <h2><i class="bi bi-file-earmark-plus"></i> Add Certificate</h2>
        <small>{{ $student->name }} — {{ $student->registration_no }}</small>
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
        <form method="POST" action="{{ route('admin.certificates.store', $student->id) }}" enctype="multipart/form-data">
            @csrf

            <label class="required">Certificate Number</label>
            <input type="text" name="certificate_number" class="form-control" value="{{ old('certificate_number') }}" required>

            <div class="row-2">
                <div>
                    <label class="required">Student Name</label>
                    <input type="text" name="student_name" class="form-control" value="{{ old('student_name', $student->name) }}" required>
                </div>
                <div>
                    <label class="required">Father's Name</label>
                    <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}" required>
                </div>
            </div>

            <label class="required">Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" required>

            <label class="required">Course</label>
            <input type="text" name="course" class="form-control" value="{{ old('course') }}" required>

            <div class="row-2">
                <div>
                    <label class="required">Course Start</label>
                    <input type="date" name="course_start" class="form-control" value="{{ old('course_start') }}" required>
                </div>
                <div>
                    <label class="required">Course End</label>
                    <input type="date" name="course_end" class="form-control" value="{{ old('course_end') }}" required>
                </div>
            </div>

            <label class="required">Award Status</label>
            <select name="award_status" class="form-select" required>
                <option value="">Select</option>
                <option value="Distinction" {{ old('award_status')=='Distinction'?'selected':'' }}>Distinction</option>
                <option value="Merit" {{ old('award_status')=='Merit'?'selected':'' }}>Merit</option>
                <option value="Pass" {{ old('award_status')=='Pass'?'selected':'' }}>Pass</option>
            </select>

            <label class="required">Student Photo</label>
            <input type="file" name="photo" class="form-control" accept="image/*" required>

            <button type="submit" class="submit-btn"><i class="bi bi-check-circle"></i> Save Certificate</button>
        </form>
    </div>
</div>
@endsection
