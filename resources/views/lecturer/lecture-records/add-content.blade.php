<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Content</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){ body { padding:20px 12px; } }
    .container { max-width:600px; margin:auto; }
    .back-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .back-btn:hover{ background:#012147; color:#fff; }
    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:24px 28px; margin:18px 0 26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h3{ margin:0; font-weight:700; font-size:20px; }
    .card-box{
        background:#fff; padding:26px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06);
    }
    .form-label{ font-weight:600; color:#012147; font-size:14px; }
    .form-control{ border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px; }
    .form-control:focus{ border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }
    .form-control[readonly]{ background:#f1f5f9; color:#64748b; }
    .row-2{ display:flex; gap:14px; }
    @media (max-width:576px){ .row-2{ flex-direction:column; } }
    .row-2 > div{ flex:1; }
    .btn-navy{ background:#012147; color:#fff; border:none; padding:12px; font-weight:600; border-radius:10px; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.subject.lecture-records', $record->subject_id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Add Content</h3>
        <small>{{ $record->subject->code }} - {{ $record->subject->name }}</small>
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
        <form action="{{ route('lecturer.lecture-records.add-content.store', $record->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Lecturer Name</label>
                <input type="text" class="form-control" value="{{ auth()->guard('lecturer')->user()->name }}" readonly>
            </div>

            <div class="row-2 mb-3">
                <div>
                    <label class="form-label">Date</label>
                    <input type="text" class="form-control" value="{{ $record->date ? \Carbon\Carbon::parse($record->date)->format('d M Y') : 'Not scheduled yet' }}" readonly>
                </div>
                <div>
                    <label class="form-label">Duration</label>
                    <input type="text" class="form-control" value="{{ $record->duration ?? '—' }}" readonly>
                </div>
            </div>

            <div class="row-2 mb-4">
                <div>
                    <label class="form-label">Start Time</label>
                    <input type="text" class="form-control" value="{{ $record->start_time ? \Carbon\Carbon::parse($record->start_time)->format('h:i A') : 'Not scheduled yet' }}" readonly>
                </div>
                <div>
                    <label class="form-label">End Time</label>
                    <input type="text" class="form-control" value="{{ $record->end_time ? \Carbon\Carbon::parse($record->end_time)->format('h:i A') : 'Not scheduled yet' }}" readonly>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Content Covered</label>
                <textarea name="content_covered" class="form-control" rows="4" required>{{ old('content_covered', $record->content_covered) }}</textarea>
            </div>

            <button class="btn btn-navy w-100">Save Content</button>
        </form>
    </div>
</div>
</body>
</html>