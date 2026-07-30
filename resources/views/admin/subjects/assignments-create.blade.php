<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create Assignment</title>
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
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.subjects.assignments.index', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <h3><i class="bi bi-journal-plus"></i> Create Assignment</h3>
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
        <form action="{{ route('admin.subjects.assignments.store', $subject->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Due Date</label>
                <input type="datetime-local" name="due_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Total Points</label>
                <input type="number" name="total_points" class="form-control" min="0">
            </div>

            <div class="mb-3">
                <label class="form-label">Submission Type</label>
                <select name="submission_type" class="form-select">
                    <option value="file">File Upload</option>
                    <option value="text">Text</option>
                    <option value="link">Link</option>
                </select>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="allow_late" value="1" class="form-check-input" id="allowLate">
                <label class="form-check-label" for="allowLate">Allow late submissions</label>
            </div>

            <div class="mb-3">
                <label class="form-label">Late Penalty (%)</label>
                <input type="number" name="late_penalty" class="form-control" min="0" max="100">
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="is_published" value="1" class="form-check-input" id="isPublished" checked>
                <label class="form-check-label" for="isPublished">Publish immediately</label>
            </div>

            <div class="mb-3">
                <label class="form-label">Attachment (assignment brief / instructions document)</label>
                <input type="file" name="assignment_file" class="form-control">
            </div>

            <button class="btn btn-navy w-100">Create Assignment</button>
        </form>
    </div>
</div>
</body>
</html>