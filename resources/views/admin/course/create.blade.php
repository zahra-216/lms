<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Course | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:560px; margin:auto; }

    .back-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover { background:#012147; color:#fff; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:24px 28px; margin-bottom:26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h3 { margin:0; font-weight:700; font-size:20px; }

    .card-box { background:#fff; border-radius:16px; padding:28px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }

    .form-label { font-weight:600; color:#012147; font-size:14px; margin-bottom:6px; display:block; }
    .form-control, .form-select, textarea { border-radius:10px; border:1px solid #e2e8f0; padding:11px 14px; width:100%; margin-bottom:18px; }
    .form-control:focus, .form-select:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); outline:none; }
    textarea { resize:vertical; min-height:100px; }

    .btn-navy { width:100%; background:#012147; color:#fff; border:none; padding:12px; border-radius:12px; font-weight:600; }
    .btn-navy:hover { background:#0b2d5a; }

    @media (max-width:480px){ .card-box{ padding:20px; } }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.courses.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h3><i class="bi bi-journal-bookmark"></i> Add Course</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif
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
        <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
            @csrf

            <label class="form-label">Course Code</label>
            <input type="text" name="code" class="form-control" placeholder="Course Code" value="{{ old('code') }}" required>

            <label class="form-label">Course Name</label>
            <input type="text" name="name" class="form-control" placeholder="Course Name" value="{{ old('name') }}" required>

            <label class="form-label">Faculty</label>
            <select name="faculty_id" class="form-select" required>
                <option value="">Select Faculty</option>
                @foreach($faculties as $faculty)
                    <option value="{{ $faculty->id }}" {{ old('faculty_id')==$faculty->id?'selected':'' }}>
                        {{ $faculty->name }}
                    </option>
                @endforeach
            </select>

            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" placeholder="Description">{{ old('description') }}</textarea>

            <label class="form-label">Course Image (optional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">

            <button type="submit" class="btn-navy">Add Course</button>
        </form>
    </div>
</div>
</body>
</html>