<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Timetable - {{ $subject->name }} | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:480px){ body{ padding:20px 12px; } }
    .container { max-width:1000px; margin:auto; }
    .back-btn{ border:none; background:#fff; color:#012147; font-weight:600; padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06); text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px; }
    .back-btn:hover{ background:#012147; color:#fff; }
    .page-header{ background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff; border-radius:18px; padding:26px 30px; margin-bottom:30px; box-shadow:0 10px 30px rgba(1,33,71,0.25); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }
    .page-header small{ opacity:0.85; }
    .btn-add{ background:#fff; color:#012147; font-weight:600; padding:10px 20px; border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
    .btn-add:hover{ background:#e2e8f0; color:#012147; }
    .card-box{ background:#fff; border-radius:15px; box-shadow:0 6px 20px rgba(0,0,0,0.06); padding:22px; margin-bottom:18px; }
    .card-box h5{ color:#012147; font-weight:700; margin-bottom:14px; }
    table{ width:100%; }
    thead th{ background:#012147; color:#fff; padding:10px 12px; font-size:13px; }
    tbody td{ padding:10px 12px; font-size:14px; border-bottom:1px solid #eee; }
    tbody tr:nth-child(even){ background:#f8fafc; }
    .badge-day{ background:#eef2ff; color:#4338ca; padding:4px 10px; border-radius:8px; font-size:12px; font-weight:600; margin:2px; display:inline-block; }
    .actions a{ margin-right:10px; text-decoration:none; font-size:15px; }
    .actions .edit{ color:#f59e0b; }
    .actions .delete{ color:#ef4444; background:none; border:none; padding:0; }
    .empty-state{ text-align:center; padding:50px 20px; color:#94a3b8; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.subjects.show', $subject->id) }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <div>
            <h2><i class="bi bi-calendar-week"></i> Timetable - {{ $subject->code }}</h2>
            <small>{{ $subject->name }}</small>
        </div>
        <a href="{{ route('admin.subjects.timetables.create', $subject->id) }}" class="btn-add">
            <i class="bi bi-plus-circle"></i> Add Timetable
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($entries->isEmpty())
        <div class="card-box empty-state">
            <i class="bi bi-calendar-x" style="font-size:2rem;"></i>
            <p class="mt-2 mb-0">No timetable entries yet.</p>
        </div>
    @else
        @foreach($entries as $groupId => $rows)
            <div class="card-box">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <h5><i class="bi bi-person-badge"></i> {{ $rows->first()->lecturer->name ?? 'N/A' }}</h5>
                        @foreach($rows as $row)
                            <span class="badge-day">{{ $row->day }} &bull; {{ \Carbon\Carbon::parse($row->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($row->end_time)->format('h:i A') }}</span>
                        @endforeach
                        @if($rows->first()->content_covered)
                            <p class="mt-2 mb-0 text-muted" style="font-size:13px;"><i class="bi bi-journal-text"></i> {{ $rows->first()->content_covered }}</p>
                        @endif
                    </div>
                    <div class="actions">
                        <a href="{{ route('admin.subjects.timetables.edit', [$subject->id, $groupId]) }}" class="edit"><i class="bi bi-pencil-square"></i></a>
                        <form action="{{ route('admin.subjects.timetables.destroy', [$subject->id, $groupId]) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this timetable entry?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
</body>
</html>