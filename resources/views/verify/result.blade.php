<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Certificate Verification Result | TT Metro Campus</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body {
        font-family:'Segoe UI', sans-serif; margin:0; min-height:100vh; color:#012147;
        background:linear-gradient(135deg,#012147,#1e3a6e);
        display:flex; align-items:center; justify-content:center; padding:20px;
    }
    .result-card {
        background:#fff; border-radius:18px; overflow:hidden; max-width:560px; width:100%;
        box-shadow:0 20px 50px rgba(0,0,0,0.25);
    }
    .result-header {
        background:#012147; color:#fff; text-align:center; padding:22px; font-weight:700; font-size:19px;
    }
    .result-header i { color:#4ade80; margin-right:8px; }

    .photo-wrap { text-align:center; padding:26px 0 10px; }
    .photo-wrap img {
        width:120px; height:120px; object-fit:cover; border-radius:10px;
        border:3px solid #012147; user-select:none; pointer-events:none;
    }

    .details { padding:10px 30px 30px; }
    .row-item { display:flex; justify-content:space-between; gap:14px; padding:11px 0; border-bottom:1px solid #f1f5f9; font-size:14px; }
    .row-item:last-child { border-bottom:none; }
    .row-item .label { font-weight:600; color:#012147; }
    .row-item .value { text-align:right; color:#334155; }

    .status-badge { padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
    .status-Distinction { background:#dcfce7; color:#166534; }
    .status-Merit { background:#dbeafe; color:#1e40af; }
    .status-Pass { background:#fef9c3; color:#854d0e; }

    .footer-note { text-align:center; font-size:11.5px; color:#94a3b8; padding:0 30px 24px; }
    .back-link { text-align:center; padding-bottom:20px; }
    .back-link a { color:#012147; font-size:13px; font-weight:600; text-decoration:none; }
</style>
</head>
<body oncontextmenu="return false;">
<div class="result-card">
    <div class="result-header"><i class="bi bi-check-circle-fill"></i>Certificate Verified</div>

    @if($certificate->photo)
        <div class="photo-wrap">
            <img src="{{ asset('storage/'.$certificate->photo) }}" alt="" draggable="false">
        </div>
    @endif

    <div class="details">
        <div class="row-item"><span class="label">Certificate Number</span><span class="value">{{ $certificate->certificate_number }}</span></div>
        <div class="row-item"><span class="label">Student Name</span><span class="value">{{ $certificate->student_name }}</span></div>
        <div class="row-item"><span class="label">Father's Name</span><span class="value">{{ $certificate->father_name }}</span></div>
        <div class="row-item"><span class="label">Date of Birth</span><span class="value">{{ $certificate->date_of_birth->format('d M Y') }}</span></div>
        <div class="row-item"><span class="label">Course</span><span class="value">{{ $certificate->course }}</span></div>
        <div class="row-item"><span class="label">Course Start</span><span class="value">{{ $certificate->course_start->format('d M Y') }}</span></div>
        <div class="row-item"><span class="label">Course End</span><span class="value">{{ $certificate->course_end->format('d M Y') }}</span></div>
        <div class="row-item"><span class="label">Award Status</span><span class="value"><span class="status-badge status-{{ $certificate->award_status }}">{{ $certificate->award_status }}</span></span></div>
    </div>

    <div class="footer-note">This is an official verification result issued by TT Metro Campus.</div>
    <div class="back-link"><a href="{{ route('verify.show') }}">← Verify another certificate</a></div>
</div>
</body>
</html>