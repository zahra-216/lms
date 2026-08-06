<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Timetable - {{ $subject->name }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:480px){ body{ padding:20px 12px; } }
    .container { max-width:900px; margin:auto; }
    .back-btn{ border:none; background:#fff; color:#012147; font-weight:600; padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06); text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px; }
    .back-btn:hover{ background:#012147; color:#fff; }
    .page-header{ background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff; border-radius:18px; padding:26px 30px; margin-bottom:30px; box-shadow:0 10px 30px rgba(1,33,71,0.25); }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }
    .card-box{ background:#fff; border-radius:15px; box-shadow:0 6px 20px rgba(0,0,0,0.06); padding:22px; margin-bottom:18px; }
    .badge-day{ background:#eef2ff; color:#4338ca; padding:5px 12px; border-radius:8px; font-size:13px; font-weight:600; margin:3px; display:inline-block; }
    .empty-state{ text-align:center; padding:50px 20px; color:#94a3b8; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h2><i class="bi bi-calendar-week"></i> Timetable - {{ $subject->code }}</h2>
        <small>{{ $subject->name }}</small>
    </div>

    @if($entries->isEmpty())
        <div class="card-box empty-state">
            <i class="bi bi-calendar-x" style="font-size:2rem;"></i>
            <p class="mt-2 mb-0">No timetable has been set for you yet.</p>
        </div>
    @else
        @foreach($entries as $groupId => $rows)
            <div class="card-box">
                @foreach($rows as $row)
                    <span class="badge-day">{{ $row->day }} &bull; {{ \Carbon\Carbon::parse($row->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($row->end_time)->format('h:i A') }}</span>
                @endforeach
                @if($rows->first()->content_covered)
                    <p class="mt-3 mb-0" style="font-size:14px; color:#475569;">
                        <i class="bi bi-journal-text"></i> {{ $rows->first()->content_covered }}
                    </p>
                @endif
            </div>
        @endforeach
    @endif
</div>
</body>
</html>