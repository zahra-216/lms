<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Faculty | Admin</title>
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

    .card-box { background:#fff; border-radius:16px; padding:28px; box-shadow:0 8px 26px rgba(0,0,0,0.06); text-align:center; }

    .preview-img { width:100px; height:100px; border-radius:50%; object-fit:cover; border:3px solid #012147; margin:0 auto 20px; display:block; }

    .form-label { font-weight:600; color:#012147; font-size:14px; margin-bottom:6px; display:block; text-align:left; }
    .form-control { border-radius:10px; border:1px solid #e2e8f0; padding:11px 14px; width:100%; margin-bottom:18px; }
    .form-control:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); outline:none; }

    .btn-navy { width:100%; background:#012147; color:#fff; border:none; padding:12px; border-radius:12px; font-weight:600; }
    .btn-navy:hover { background:#0b2d5a; }

    @media (max-width:480px){ .card-box{ padding:20px; } }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.faculties.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Faculty</h3>
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
        @php
            $img = $faculty->image
                ? asset('storage/faculty/'.$faculty->image)
                : 'https://via.placeholder.com/100';
        @endphp
        <img src="{{ $img }}" class="preview-img">

        <form action="{{ route('admin.faculties.update', $faculty->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label class="form-label">Faculty Name</label>
            <input type="text" name="name" class="form-control" value="{{ $faculty->name }}" required>

            <label class="form-label">Faculty Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">

            <button type="submit" class="btn-navy">Update Faculty</button>
        </form>
    </div>
</div>
</body>
</html>