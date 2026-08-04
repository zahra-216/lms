<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notes - {{ $subject->name }}</title>
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
    .page-header h3 { margin:0; font-weight:700; font-size:20px; }

    .btn-add {
        background:#fff; color:#012147; font-weight:600; padding:9px 18px;
        border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .btn-add:hover { background:#e2e8f0; color:#012147; }

    .card-box { background:#fff; border-radius:16px; padding:20px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }

    table.notes-table { border-collapse:separate; border-spacing:0; width:100%; }
    table.notes-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 14px; white-space:nowrap; }
    table.notes-table thead th:first-child { border-top-left-radius:10px; }
    table.notes-table thead th:last-child { border-top-right-radius:10px; }
    table.notes-table tbody td { vertical-align:middle; padding:12px 14px; }
    table.notes-table tbody tr:nth-child(even) { background:#f8fafc; }
    table.notes-table tbody tr:hover { background:#eef2f9; }

    .badge-yes { background:#dcfce7; color:#15803d; padding:5px 12px; border-radius:20px; font-size:12.5px; font-weight:600; }
    .badge-no { background:#e2e8f0; color:#64748b; padding:5px 12px; border-radius:20px; font-size:12.5px; font-weight:600; }

    .btn-icon { border:none; padding:6px 12px; border-radius:8px; color:#fff; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; }
    .edit { background:#eef2f9; color:#012147; }
    .edit:hover { background:#012147; color:#fff; }
    .delete { background:#fee2e2; color:#991b1b; }
    .delete:hover { background:#dc2626; color:#fff; }
    .download { background:#10b981; }
    .download:hover { background:#059669; }
    .open { background:#3b82f6; }
    .open:hover { background:#2563eb; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header { flex-direction:column; align-items:flex-start; }
        table.notes-table { display:block; overflow-x:auto; white-space:nowrap; }
    }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.dashboard') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h3><i class="bi bi-file-earmark-text"></i> {{ $subject->name }} - Notes</h3>
        <a href="{{ route('admin.subjects.notes.create', $subject->id) }}" class="btn-add">
            <i class="bi bi-plus-circle"></i> Add Note
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <div class="card-box">
        <div class="table-responsive">
        <table class="table notes-table align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>File / URL</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notes as $note)
                    <tr>
                        <td><b>{{ $note->title }}</b></td>
                        <td>{{ $note->description }}</td>
                        <td>{{ ucfirst($note->type) }}</td>
                        <td>
                            @if($note->url)
                                <a href="{{ $note->url }}" target="_blank" class="btn-icon open"><i class="bi bi-link-45deg"></i></a>
                            @endif
                            @if($note->file_path)
                                <a href="{{ route('admin.subjects.notes.download', [$subject->id, $note->id]) }}" class="btn-icon download"><i class="bi bi-download"></i></a>
                            @endif
                        </td>
                        <td>{{ $note->order }}</td>
                        <td>
                            @if($note->is_published)
                                <span class="badge-yes">Published</span>
                            @else
                                <span class="badge-no">Hidden</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.subjects.notes.edit', [$subject->id,$note->id]) }}" class="btn-icon edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.subjects.notes.destroy', [$subject->id,$note->id]) }}" method="POST" onsubmit="return confirm('Delete this note?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" style="border:none;"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $notes->links() }}
    </div>
</div>
</body>
</html>