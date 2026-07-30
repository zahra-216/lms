<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faculties | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:1100px; margin:auto; }

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
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2 { margin:0 0 4px; font-weight:700; font-size:22px; }
    .page-header small { opacity:0.85; }

    .add-btn {
        background:#fff; color:#012147; font-weight:600; padding:10px 20px;
        border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;
    }
    .add-btn:hover { background:#e2e8f0; color:#012147; }

    .faculty-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:20px; }

    .faculty-card { position:relative; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 8px 26px rgba(0,0,0,0.06); transition:transform .25s ease, box-shadow .25s ease; }
    .faculty-card:hover { transform:translateY(-5px); box-shadow:0 14px 32px rgba(0,0,0,.1); }

    .faculty-card-link { position:absolute; inset:0; z-index:1; }
    .faculty-card-img { height:150px; background:#f2f5fb; overflow:hidden; }
    .faculty-card-img img { width:100%; height:100%; object-fit:cover; display:block; }
    .faculty-card-body { padding:16px 18px 18px; position:relative; z-index:0; }
    .faculty-card-body h3 { margin:0 0 8px; font-size:16px; color:#012147; font-weight:700; }
    .faculty-card-cta { font-size:13px; font-weight:600; color:#012147; display:inline-flex; align-items:center; gap:6px; }

    .card-actions { position:absolute; top:10px; right:10px; display:flex; gap:6px; z-index:5; }
    .card-actions button, .card-actions a { width:32px; height:32px; border-radius:9px; border:none; display:flex; align-items:center; justify-content:center; font-size:13px; box-shadow:0 4px 10px rgba(0,0,0,0.15); }
    .icon-edit { background:#eef2f9; color:#012147; }
    .icon-edit:hover { background:#012147; color:#fff; }
    .icon-delete { background:#fee2e2; color:#991b1b; }
    .icon-delete:hover { background:#dc2626; color:#fff; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header { flex-direction:column; align-items:flex-start; }
        .faculty-grid { grid-template-columns:1fr; }
    }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.dashboard') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <div>
            <h2><i class="bi bi-person-badge"></i> Faculties</h2>
            <small>Select a faculty to manage its courses, semesters, and subjects.</small>
        </div>
        <a href="{{ route('admin.faculties.create') }}" class="add-btn"><i class="bi bi-plus-circle"></i> Add Faculty</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <div class="faculty-grid">
        @foreach($faculties as $faculty)
            @php
                $relativePath = 'storage/faculty/'.$faculty->image;
                $imgPath = ($faculty->image && file_exists(public_path($relativePath)))
                    ? asset($relativePath)
                    : 'https://picsum.photos/320/220?random='.$faculty->id;
            @endphp
            <div class="faculty-card">
                <a href="{{ route('admin.faculties.courses', $faculty->id) }}" class="faculty-card-link" aria-label="Manage {{ $faculty->name }}"></a>

                <div class="card-actions">
                    <a href="{{ route('admin.faculties.edit', $faculty->id) }}" class="icon-edit"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('admin.faculties.destroy', $faculty->id) }}" method="POST" onsubmit="return confirm('Delete this faculty?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="icon-delete"><i class="bi bi-trash"></i></button>
                    </form>
                </div>

                <div class="faculty-card-img"><img src="{{ $imgPath }}" alt="{{ $faculty->name }}"></div>
                <div class="faculty-card-body">
                    <h3>{{ $faculty->name }}</h3>
                    <span class="faculty-card-cta">Manage Courses <i class="bi bi-arrow-right"></i></span>
                </div>
            </div>
        @endforeach
    </div>
</div>
</body>
</html>