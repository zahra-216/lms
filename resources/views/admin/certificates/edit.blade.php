<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Certificate | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:700px; margin:auto; }

    .back-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover { background:#012147; color:#fff; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:26px 30px; margin-bottom:22px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h2 { margin:0 0 4px; font-weight:700; font-size:20px; }
    .page-header small { opacity:0.85; }

    .card-box { background:#fff; border-radius:14px; padding:28px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }

    label { font-weight:600; font-size:13.5px; margin-bottom:6px; display:block; }
    .required::after { content:" *"; color:#dc2626; }
    .form-control, .form-select { border-radius:10px; padding:10px 14px; border:1px solid #e2e8f0; margin-bottom:18px; }
    .form-control:focus, .form-select:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.08); }

    .current-photo { width:80px; height:80px; border-radius:10px; object-fit:cover; border:2px solid #e2e8f0; margin-bottom:10px; }

    .submit-btn { background:#012147; color:#fff; border:none; padding:12px 26px; border-radius:10px; font-weight:600; width:100%; }
    .submit-btn:hover { background:#1e3a6e; }

    .row-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    @media (max-width:576px){ .row-2 { grid-template-columns:1fr; } }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.certificates.student', $student->id) }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h2><i class="bi bi-pencil-square"></i> Edit Certificate</h2>
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
</body>
</html>