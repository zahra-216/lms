@extends('layouts.lecturer')
@section('title')
Timetable - {{ $subject->name }}
@endsection
@push('styles')
<style>
.badge-day { background:#eef2ff; color:#4338ca; padding:5px 12px; border-radius:8px; font-size:13px; font-weight:600; margin:3px; display:inline-block;
}
</style>
@endpush
@section('content')
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
@endsection
