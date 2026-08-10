<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Assignment</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){ body { padding:20px 12px; } }
    .container { max-width:700px; margin:auto; }
    .back-btn{ border:none; background:#fff; color:#012147; font-weight:600; padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06); text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .back-btn:hover{ background:#012147; color:#fff; }
    .page-header{ background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff; border-radius:18px; padding:24px 28px; margin:18px 0 26px; box-shadow:0 10px 30px rgba(1,33,71,0.25); }
    .page-header h3{ margin:0; font-weight:700; font-size:20px; }
    .page-header small{ opacity:0.85; }
    .card-box{ background:#fff; padding:26px; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.06); }
    .form-label{ font-weight:600; color:#012147; font-size:14px; }
    .form-control, .form-select{ border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px; }
    .form-control:focus, .form-select:focus{ border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }
    .form-check-input{ width:18px; height:18px; cursor:pointer; }
    .form-check-label{ cursor:pointer; }
    .btn-navy{ background:#012147; color:#fff; border:none; padding:12px; font-weight:600; border-radius:10px; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }
    .current-file{ font-size:13px; color:#64748b; margin-bottom:8px; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.subject.assignments', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Assignment</h3>
        <small>{{ $subject->name }}</small>
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
        <form action="{{ route('lecturer.assignments.update', [$subject->id, $assignment->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $assignment->title) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" required>{{ old('description', $assignment->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Due Date <span class="text-danger">*</span></label>
                <input type="datetime-local" name="due_date" class="form-control"
                       value="{{ old('due_date', \Carbon\Carbon::parse($assignment->due_date)->format('Y-m-d\TH:i')) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Total Points <span class="text-danger">*</span></label>
                <input type="number" name="total_points" class="form-control" min="0" value="{{ old('total_points', $assignment->total_points) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Submission Type</label>
                <select name="submission_type" class="form-select">
                    @foreach(['file' => 'File Upload', 'text' => 'Text'] as $value => $label)
                        <option value="{{ $value }}" {{ $assignment->submission_type == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Late Penalty (%)</label>
                <input type="number" name="late_penalty" class="form-control" min="0" max="100" value="{{ old('late_penalty', $assignment->late_penalty) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Attachment (assignment brief / instructions document)</label>

                @if($assignment->file_path)
                    <div class="current-file">
                        Current file:
                        <a href="{{ Storage::disk('public')->url($assignment->file_path) }}" target="_blank">
                            {{ basename($assignment->file_path) }}
                        </a>
                    </div>
                @endif

                <input type="file" name="assignment_file" class="form-control">
            </div>

            <button class="btn btn-navy w-100">Update Assignment</button>
        </form>
    </div>
</div>
</body>
</html>