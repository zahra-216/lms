@php
    /** @var \App\Models\Timetable $timetable */
    $compact = $compact ?? false;
    $rows = $timetable->sortedRows();
    $title = $timetable->course->name ?? 'Timetable';
    $sub = collect([$timetable->level->name ?? null, $timetable->semester->name ?? null, $timetable->batch])->filter()->implode(' · ');
@endphp
<div class="tt-card {{ $compact ? 'tt-compact' : '' }}">
    <div class="tt-head">
        <div class="tt-brand">TT Metro Campus</div>
        <h3 class="tt-title">{{ $title }}</h3>
        <div class="tt-sub">{{ $sub }} - Weekly Class Time Table</div>
        <span class="tt-pill">{{ $timetable->mode_note ? $timetable->mode_note.' · ' : '' }}{{ $rows->count() }} {{ \Illuminate\Support\Str::plural('class', $rows->count()) }} / week</span>
    </div>

    @if ($compact)
        <table class="tt-table">
            <thead><tr><th>Day &amp; time</th><th>Module</th></tr></thead>
            <tbody>
                @foreach ($rows as $r)
                    <tr>
                        <td><span class="tt-day">{{ $r->day }}</span><span class="tt-time">{{ $r->timeRange() }}</span></td>
                        <td>
                            <span class="tt-module">{{ $r->subject->name ?? '-' }}</span>
                            @if ($r->note)<span class="tt-note">{{ $r->note }}</span>@endif
                            <span class="tt-lect">{{ $r->lecturer->name ?? '' }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <table class="tt-table">
            <thead><tr><th>Day</th><th>Time</th><th>Module</th><th>Lecturer</th></tr></thead>
            <tbody>
                @foreach ($rows as $r)
                    <tr>
                        <td><span class="tt-day">{{ $r->day }}</span></td>
                        <td><span class="tt-time">{{ $r->timeRange() }}</span></td>
                        <td>
                            <span class="tt-module">{{ $r->subject->name ?? '-' }}</span>
                            @if ($r->note)<span class="tt-note">{{ $r->note }}</span>@endif
                        </td>
                        <td>{{ $r->lecturer->name ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <div class="tt-foot">Please join on time. Any changes will be informed through the WhatsApp group &amp; LMS.</div>
</div>
