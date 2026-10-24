@extends('layouts.admin')
@section('title')
Timetable - {{ $subject->name }}
@endsection
@section('back', route('admin.subjects.show', $subject->id))
@push('styles')
<style>
.btn-add { background:#fff; color:#012147; font-weight:600; padding:10px 20px; border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;
}
.btn-add:hover { background:#e2e8f0; color:#012147;
}
.card-box h5 { color:#012147; font-weight:700; margin-bottom:14px;
}
tbody td { padding:10px 12px; font-size:14px; border-bottom:1px solid #eee;
}
tbody tr:nth-child(even) { background:#f8fafc;
}
.badge-day { background:#eef2ff; color:#4338ca; padding:4px 10px; border-radius:8px; font-size:12px; font-weight:600; margin:2px; display:inline-block;
}
.actions a { margin-right:10px; text-decoration:none; font-size:15px;
}
.actions .edit { color:#f59e0b;
}
.actions .delete { color:#ef4444; background:none; border:none; padding:0;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <div>
            <h2><i class="bi bi-calendar-week"></i> Timetable - {{ $subject->code }}</h2>
            <small>{{ $subject->name }}</small>
        </div>
        <a href="{{ route('admin.subjects.timetables.create', $subject->id) }}" class="btn-add">
            <i class="bi bi-plus-circle"></i> Add Timetable
        </a>
    </div>

    

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
@endsection
