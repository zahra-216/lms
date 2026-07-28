<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject->name }} - Notes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
        .page-header .add-btn{ width:100%; text-align:center; }
    }

    .container { max-width:1000px; margin:auto; }

    .back-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .back-btn:hover{ background:#012147; color:#fff; }

    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:26px 30px; margin:18px 0 26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }

    .add-btn{
        background:#fff; color:#012147; font-weight:600; border:none;
        padding:10px 18px; border-radius:10px; text-decoration:none;
        display:inline-flex; align-items:center; gap:6px; transition:0.2s;
    }
    .add-btn:hover{ background:#e2e8f0; color:#012147; }

    .card-box{
        background:#fff; padding:20px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:26px;
    }

    table.notes-table{ border-collapse:separate; border-spacing:0; }
    table.notes-table thead th{
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap;
    }
    table.notes-table thead th:first-child{ border-top-left-radius:10px; }
    table.notes-table thead th:last-child{ border-top-right-radius:10px; }
    table.notes-table tbody td{ vertical-align:middle; padding:10px; }
    table.notes-table tbody tr:nth-child(even){ background:#f8fafc; }
    table.notes-table tbody tr:hover{ background:#eef2f9; }

    .badge-pill{ padding:6px 12px; border-radius:20px; font-weight:600; }
    .badge-yes{ background:#dcfce7; color:#15803d; }
    .badge-no{ background:#e2e8f0; color:#64748b; }

    .btn-edit{ background:#fef9c3; color:#a16207; border:none; }
    .btn-edit:hover{ background:#fde68a; color:#a16207; }
    .btn-delete{ background:#fee2e2; color:#b91c1c; border:none; }
    .btn-delete:hover{ background:#fecaca; color:#b91c1c; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <h2><i class="bi bi-file-earmark-text"></i> {{ $subject->code }} - {{ $subject->name }} — Notes</h2>
        <a href="{{ route('lecturer.notes.create', $subject->id) }}" class="add-btn">
            <i class="bi bi-plus-circle"></i> Add Note
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    @endif

    <div class="card-box">
    @if($subject->notes && $subject->notes->count())
    <div class="table-responsive">
        <table class="table notes-table align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Published</th>
                    <th>File / Link</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subject->notes as $note)
                <tr>
                    <td>{{ $note->title }}</td>
                    <td>{{ ucfirst($note->type) }}</td>
                    <td>
                        <span class="badge-pill {{ $note->is_published ? 'badge-yes' : 'badge-no' }}">
                            {{ $note->is_published ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        @if($note->file_path)
                            <a href="{{ route('lecturer.notes.download', $note->id) }}"><i class="bi bi-download"></i> Download</a>
                        @elseif($note->url)
                            <a href="{{ $note->url }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Open Link</a>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('lecturer.notes.edit', [$subject->id, $note->id]) }}" class="btn btn-sm btn-edit">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form action="{{ route('lecturer.notes.destroy', [$subject->id, $note->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this note?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-delete">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <p class="text-muted mb-0">No notes uploaded yet for this subject.</p>
    @endif
    </div>
</div>
</body>
</html>