@extends('layouts.admin')
@section('title')
{{ $meeting->topic }} - Zoom Class
@endsection
@section('back', route('admin.zoom.index'))
@push('styles')
<style>
.zoom-facts { display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:14px; }
.zoom-facts .fact { background:#f8fafc; border-radius:12px; padding:12px 14px; }
.zoom-facts .fact span { display:block; font-size:12px; color:#64748b; }
.zoom-facts .fact strong { color:#012147; word-break:break-word; }
.join-box { display:flex; gap:8px; flex-wrap:wrap; }
.join-box input { flex:1; min-width:220px; }
.mod-card { border:1px solid #e2e8f0; border-radius:14px; padding:16px; background:#fff; margin-bottom:14px; }
.mod-numbers { display:flex; gap:10px; flex-wrap:wrap; margin:10px 0; }
.mod-numbers div { border-radius:10px; padding:8px 14px; font-weight:700; }
.n-present { background:#e7f8ee; color:#0f5c33; }
.n-absent { background:#fdeaea; color:#8a1f1f; }
.n-hand { background:#fff4e0; color:#8a5b00; }
.chip { display:inline-block; background:#e6f0ff; color:#0a3d91; border-radius:20px; padding:4px 12px; font-size:12.5px; font-weight:600; margin:2px 4px 2px 0; }
details > summary { cursor:pointer; font-weight:600; color:#012147; }
</style>
@endpush
@section('content')
@php
    $badge = ['scheduled' => 'bg-secondary', 'ended' => 'bg-warning text-dark', 'processed' => 'bg-success', 'failed' => 'bg-danger'][$meeting->status] ?? 'bg-secondary';
    $started = $meeting->start_time->isPast();
@endphp
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-camera-video"></i> {{ $meeting->topic }}</h2>
            <small>{{ $meeting->start_time->format('d M Y, h:i A') }} - {{ $meeting->start_time->copy()->addMinutes($meeting->duration_minutes)->format('h:i A') }}</small>
        </div>
        <span class="badge {{ $badge }} fs-6">{{ $meeting->statusLabel() }}</span>
    </div>

    <div class="card-box mb-3">
        <div class="zoom-facts mb-3">
            <div class="fact"><span>Meeting ID</span><strong>{{ $meeting->zoom_meeting_id }}</strong></div>
            <div class="fact"><span>Passcode</span><strong>{{ $meeting->passcode ?: '-' }}</strong></div>
            <div class="fact"><span>Length</span><strong>{{ $meeting->duration_minutes }} minutes</strong></div>
            <div class="fact"><span>Attendance date</span><strong>{{ ($meeting->held_at ?? $meeting->start_time)->format('d M Y') }}</strong></div>
        </div>

        <label class="form-label fw-semibold">Join link (share with students and lecturers)</label>
        <div class="join-box mb-3">
            <input type="text" class="form-control" id="joinLink" value="{{ $meeting->join_url }}" readonly>
            <button type="button" class="btn btn-outline-primary" id="copyJoin"><i class="bi bi-clipboard"></i> Copy</button>
        </div>

        <div class="mb-2"><span class="fw-semibold">Modules:</span>
            @foreach($meeting->subjects as $s)<span class="chip">{{ $s->code }} - {{ $s->name }}</span>@endforeach
        </div>

        @if($meeting->linked)
            <div class="small text-muted mt-2"><i class="bi bi-link-45deg"></i> This meeting was made in the Zoom app and is only linked here. Changing or deleting it here never changes it in Zoom.</div>
        @endif

        <div class="mt-3 d-flex gap-2 flex-wrap">
            @if($meeting->canBeEdited())
                <a href="{{ route('admin.zoom.edit', $meeting->id) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
            @endif
            @if($started)
                <form method="POST" action="{{ route('admin.zoom.fetch', $meeting->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary"><i class="bi bi-arrow-repeat"></i> {{ $meeting->status === 'processed' ? 'Fetch from Zoom again' : 'Fetch attendance now' }}</button>
                </form>
            @endif
            <form method="POST" action="{{ route('admin.zoom.destroy', $meeting->id) }}"
                  data-confirm="Delete this Zoom class?{{ $meeting->linked ? ' The meeting itself stays in Zoom. Attendance already saved stays in the Attendance pages.' : ($meeting->status === 'scheduled' ? ' It will also be cancelled in Zoom.' : ' Attendance already saved stays in the Attendance pages.') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
            </form>
        </div>

        <div class="small text-muted mt-3">
            @switch($meeting->status)
                @case('scheduled')
                    After the class ends, Zoom tells the LMS and the attendance is saved on its own. Nothing else to do.
                    @break
                @case('ended')
                    The class has ended. Zoom needs a few minutes to prepare the list; the LMS keeps trying
                    (try {{ $meeting->attempts }} of {{ config('services.zoom.max_attempts') }}).
                    @break
                @case('processed')
                    Saved {{ $meeting->processed_at?->diffForHumans() }}.
                    @break
                @case('failed')
                    Zoom did not give a participant list{{ $meeting->last_error ? ': '.$meeting->last_error : '' }}. If the class really took place, press "Fetch attendance now" later. If nobody joined, delete this class.
                    @break
            @endswitch
        </div>
    </div>

    @if($meeting->status === 'processed')
        <div class="card-box mb-3">
            <h5 class="fw-bold mb-3">Attendance saved</h5>
            @foreach($summary as $m)
                <div class="mod-card">
                    <div class="fw-bold">{{ $m['subject']->code }} - {{ $m['subject']->name }}</div>
                    <div class="small text-muted">{{ $m['subject']->course->name ?? '' }} / {{ $m['subject']->level->name ?? '' }} / {{ $m['subject']->semester->name ?? '' }} - {{ $m['total'] }} students</div>
                    <div class="mod-numbers">
                        <div class="n-present">{{ $m['present'] }} present</div>
                        <div class="n-absent">{{ $m['absent'] }} absent</div>
                        @if($m['by_hand'])<div class="n-hand">{{ $m['by_hand'] }} marked by hand (kept)</div>@endif
                    </div>
                    <details>
                        <summary>Show the students</summary>
                        <div class="table-responsive mt-2">
                            <table class="table align-middle mb-0">
                                <thead><tr><th>Registration no</th><th>Name</th><th>Status</th><th>Marked by</th></tr></thead>
                                <tbody>
                                    @forelse($m['students'] as $st)
                                        <tr>
                                            <td>{{ $st->registration_no }}</td>
                                            <td>{{ $st->name }}</td>
                                            <td>
                                                @if($st->status === 'present')<span class="badge bg-success">Present</span>
                                                @elseif($st->status === 'absent')<span class="badge bg-danger">Absent</span>
                                                @else<span class="badge bg-secondary">-</span>@endif
                                            </td>
                                            <td>{{ $st->source === 'zoom' ? 'Zoom' : ($st->source ? 'By hand' : '-') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted">No students are in this module's course, level and current semester.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </details>
                    <div class="mt-2"><a href="{{ route('admin.attendance.mark', ['id' => $m['subject']->id, 'date' => $meeting->attendanceDate()]) }}" class="small">Open this day in the Attendance page</a></div>
                </div>
            @endforeach
        </div>

        @if($unmatched->count())
            <div class="card-box mb-3">
                <h5 class="fw-bold">Names that did not match a student ({{ $unmatched->count() }})</h5>
                <p class="text-muted small">Lecturers and guests are fine here. If one of these is a student who typed the number wrongly, type the correct registration number and press Match. The student is then marked present.</p>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Name in Zoom</th><th>Joined</th><th>Registration number</th></tr></thead>
                        <tbody>
                            @foreach($unmatched as $p)
                                <tr>
                                    <td>{{ $p->name }}</td>
                                    <td class="small text-muted">{{ $p->join_time?->format('h:i A') ?? '-' }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.zoom.assign', [$meeting->id, $p->id]) }}" class="d-flex gap-2">
                                            @csrf
                                            <input type="text" name="registration_no" class="form-control form-control-sm" placeholder="e.g. TTMC/ML/DIP/HRM-O/24/02" required maxlength="100">
                                            <button type="submit" class="btn btn-sm btn-primary">Match</button>
                                        </form>
                                        @if(! empty($suggestions[$p->id]))
                                            <div class="small text-muted mt-2">Could it be:</div>
                                            <div class="d-flex flex-wrap gap-2 mt-1">
                                                @foreach($suggestions[$p->id] as $sg)
                                                    <form method="POST" action="{{ route('admin.zoom.assign', [$meeting->id, $p->id]) }}">
                                                        @csrf
                                                        <input type="hidden" name="registration_no" value="{{ $sg->registration_no }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Match {{ $p->name }} to this student">{{ $sg->name }} <span class="text-muted">({{ $sg->registration_no }})</span></button>
                                                    </form>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($outside->count())
            <div class="card-box mb-3">
                <h5 class="fw-bold">Joined, but not in these modules ({{ $outside->count() }})</h5>
                <p class="text-muted small">These students were found, but their course, level or current semester is not one of the ticked modules, so nothing was marked for them.</p>
                @foreach($outside as $p)
                    <div class="small">{{ $p->student->registration_no }} - {{ $p->student->name }}</div>
                @endforeach
            </div>
        @endif
    @endif
</div>
@endsection
@push('scripts')
<script>
document.getElementById('copyJoin').addEventListener('click', function () {
    var box = document.getElementById('joinLink');
    box.select();
    (navigator.clipboard ? navigator.clipboard.writeText(box.value) : Promise.resolve(document.execCommand('copy')))
        .then(function () { var b = document.getElementById('copyJoin'); b.innerHTML = '<i class="bi bi-check2"></i> Copied'; });
});
</script>
@endpush
