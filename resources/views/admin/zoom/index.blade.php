@extends('layouts.admin')
@section('title')
Zoom Classes
@endsection
@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-camera-video"></i> Zoom Classes</h2>
            <small>Create a class here and its attendance comes back from Zoom by itself when the class ends</small>
        </div>
        <div>
            <a href="{{ route('admin.zoom.create') }}" class="btn btn-light fw-semibold"><i class="bi bi-plus-circle"></i> New Zoom Class</a>
        </div>
    </div>

    @if($fake)
        <div class="alert alert-info rounded-3">Testing mode: Zoom is pretended. Nothing is sent to the real Zoom.</div>
    @elseif(! $connected)
        <div class="alert alert-warning rounded-3">
            <i class="bi bi-exclamation-triangle"></i> Zoom is not connected yet, so classes cannot be created. The Zoom keys have to be added to the server settings first.
        </div>
    @endif

    <div class="card-box">
        @if($meetings->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Date and time</th>
                            <th>Modules</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($meetings as $m)
                            <tr>
                                <td class="fw-semibold">{{ $m->topic }}<div class="small text-muted fw-normal">Meeting ID {{ $m->zoom_meeting_id }}</div></td>
                                <td>{{ $m->start_time->format('d M Y') }}<div class="small text-muted">{{ $m->start_time->format('h:i A') }} - {{ $m->start_time->copy()->addMinutes($m->duration_minutes)->format('h:i A') }}</div></td>
                                <td>
                                    @foreach($m->subjects as $s)
                                        <div class="small">{{ $s->code }} - {{ $s->name }}</div>
                                    @endforeach
                                </td>
                                <td>
                                    @php
                                        $badge = ['scheduled' => 'bg-secondary', 'ended' => 'bg-warning text-dark', 'processed' => 'bg-success', 'failed' => 'bg-danger'][$m->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $m->statusLabel() }}</span>
                                    @if($m->status === 'processed')
                                        <div class="small text-muted mt-1">{{ $m->participants_count }} joins read</div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.zoom.show', $m->id) }}" class="btn btn-sm btn-primary">Open</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $meetings->links() }}</div>
        @else
            <div class="empty-state">
                <i class="bi bi-camera-video"></i>
                <div class="fw-semibold">No Zoom classes yet</div>
                <p class="mb-0">Press "New Zoom Class" to create the first one.</p>
            </div>
        @endif
    </div>
</div>
@endsection
