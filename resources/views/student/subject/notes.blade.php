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
    .page-header small{ opacity:0.85; }

    .card-box{
        background:#fff; padding:20px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:26px;
    }
    .section-title{
        font-weight:700; color:#012147; margin-bottom:16px;
        display:flex; align-items:center; gap:8px; font-size:17px;
    }

    table.marks-table{ border-collapse:separate; border-spacing:0; }
    table.marks-table thead th{
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap; text-align:left;
    }
    table.marks-table thead th:first-child{ border-top-left-radius:10px; }
    table.marks-table thead th:last-child{ border-top-right-radius:10px; }
    table.marks-table tbody td{ vertical-align:middle; padding:10px; }
    table.marks-table tbody tr:nth-child(even){ background:#f8fafc; }
    table.marks-table tbody tr:hover{ background:#eef2f9; }

    .note-link{
        display:inline-flex; align-items:center; gap:6px;
        color:#012147; font-weight:600; text-decoration:none;
        padding:6px 14px; border-radius:20px; background:#eef2f9; font-size:13px;
    }
    .note-link:hover{ background:#012147; color:#fff; }

    .empty-state{ text-align:center; color:#94a3b8; padding:20px 0; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('student.subject.portal.show', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <div>
            <h2><i class="bi bi-file-earmark-text"></i> {{ $subject->code }} — {{ $subject->name }}</h2>
            <small>Notes</small>
        </div>
        <i class="bi bi-journal-bookmark" style="font-size:44px; opacity:0.85;"></i>
    </div>

    <div class="card-box">
        <div class="section-title"><i class="bi bi-folder2-open"></i> Notes & Resources</div>

        <div class="table-responsive">
        <table class="table marks-table align-middle mb-0">
            <thead>
                <tr><th>Title</th><th>File / Link</th></tr>
            </thead>
            <tbody>
                @forelse($subject->notes as $note)
                    @if($note->is_published)
                    <tr>
                        <td>{{ $note->title }}</td>
                        <td>
                            @if($note->file_path)
                                <a href="{{ route('student.note.download', $note->id) }}" class="note-link">
                                    <i class="bi bi-download"></i> Download
                                </a>
                            @elseif($note->url)
                                <a href="{{ $note->url }}" target="_blank" class="note-link">
                                    <i class="bi bi-box-arrow-up-right"></i> Open Link
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="2" class="empty-state">No notes uploaded yet for this subject.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
</body>
</html>