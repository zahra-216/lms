<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject->name }} - Recordings | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    .container { max-width:900px; margin:auto; }
    .back-btn{ border:none; background:#fff; color:#012147; font-weight:600; padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06); text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px; }
    .back-btn:hover{ background:#012147; color:#fff; }
    .page-header{ background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff; border-radius:18px; padding:24px 28px; margin-bottom:26px; box-shadow:0 10px 30px rgba(1,33,71,0.25); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; }
    .page-header h3{ margin:0; font-weight:700; font-size:20px; }
    .add-btn{ background:#fff; color:#012147; font-weight:600; padding:10px 18px; border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .rec-card{ background:#fff; border-radius:14px; padding:18px 20px; box-shadow:0 6px 20px rgba(0,0,0,0.06); display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
    .rec-title{ font-weight:600; color:#012147; margin:0; }
    .rec-actions a{ margin-left:10px; text-decoration:none; font-size:1.1rem; }
    .rec-actions .edit-link{ color:#3b82f6; }
    .rec-actions .del-link{ color:#ef4444; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.subjects.show', $subject->id) }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <div>
            <h3><i class="bi bi-collection-play"></i> Recordings</h3>
            <small>{{ $subject->code }} - {{ $subject->name }}</small>
        </div>
        <a href="{{ route('admin.subjects.recordings.create', $subject->id) }}" class="add-btn">
            <i class="bi bi-plus-circle"></i> Add Recording
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    @forelse($recordings as $recording)
        <div class="rec-card">
            <p class="rec-title"><i class="bi bi-play-circle text-secondary me-2"></i>{{ $recording->title }}</p>
            <div class="rec-actions">
                <a href="{{ route('admin.subjects.recordings.edit', [$subject->id, $recording->id]) }}" class="edit-link"><i class="bi bi-pencil-square"></i></a>
                <a href="#" class="del-link" onclick="event.preventDefault(); document.getElementById('del-{{ $recording->id }}').submit();"><i class="bi bi-trash"></i></a>
                <form id="del-{{ $recording->id }}" action="{{ route('admin.subjects.recordings.destroy', [$subject->id, $recording->id]) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @empty
        <p class="text-muted">No recordings added yet.</p>
    @endforelse
</div>
</body>
</html>