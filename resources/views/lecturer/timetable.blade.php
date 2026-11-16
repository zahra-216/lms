@extends('layouts.lecturer')
@section('title')
My Timetable
@endsection
@section('no_back', '1')
@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-calendar-week"></i> My Timetable</h2>
            <small>Your weekly classes, from the timetables set by the campus</small>
        </div>
    </div>

    @if ($rows->isEmpty())
        <div class="empty-state"><i class="bi bi-calendar-week"></i>No classes have been set for you yet.</div>
    @else
        <div class="tt-card">
            <div class="tt-head">
                <div class="tt-brand">TT Metro Campus</div>
                <h3 class="tt-title">{{ auth('lecturer')->user()->name }}</h3>
                <div class="tt-sub">Weekly Class Time Table</div>
                <span class="tt-pill">{{ $rows->count() }} {{ \Illuminate\Support\Str::plural('class', $rows->count()) }} / week</span>
            </div>

            {{-- wide screens: one column for each detail --}}
            <table class="tt-table tt-wide">
                <thead><tr><th>Day</th><th>Time</th><th>Module</th><th>Course</th></tr></thead>
                <tbody>
                    @foreach ($rows as $r)
                        <tr>
                            <td><span class="tt-day">{{ $r->day }}</span></td>
                            <td><span class="tt-time">{{ $r->timeRange() }}</span></td>
                            <td>
                                <span class="tt-module">{{ $r->subject->name ?? '-' }}</span>
                                @if ($r->note)<span class="tt-note">{{ $r->note }}</span>@endif
                            </td>
                            <td>
                                <span class="tt-module">{{ $r->timetable->course->name ?? '' }}</span>
                                <span class="tt-lect">{{ collect([$r->timetable->level->name ?? null, $r->timetable->semester->name ?? null, $r->timetable->batch])->filter()->implode(' · ') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- phones: day and time together, module and course together --}}
            <table class="tt-table tt-narrow">
                <thead><tr><th>Day &amp; time</th><th>Module</th></tr></thead>
                <tbody>
                    @foreach ($rows as $r)
                        <tr>
                            <td><span class="tt-day">{{ $r->day }}</span><span class="tt-time">{{ $r->timeRange() }}</span></td>
                            <td>
                                <span class="tt-module">{{ $r->subject->name ?? '-' }}</span>
                                @if ($r->note)<span class="tt-note">{{ $r->note }}</span>@endif
                                <span class="tt-lect">{{ $r->timetable->course->name ?? '' }} - {{ collect([$r->timetable->level->name ?? null, $r->timetable->semester->name ?? null, $r->timetable->batch])->filter()->implode(' · ') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="tt-foot">Please join on time. Any changes will be informed through the WhatsApp group &amp; LMS.</div>
        </div>
    @endif
</div>
@endsection
