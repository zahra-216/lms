@extends('layouts.student')
@section('title')
{{ $subject->name }} — Recordings
@endsection
@push('styles')
<style>
.navy-header { background: #012147; color: #fff; padding: 20px;
}
.rec-card { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
</style>
@endpush
@section('content')
    <div class="navy-header">
        <div class="container">
            <a href="{{ route('student.subject.portal.show', $subject->id) }}" class="text-white text-decoration-none"><i class="bi bi-arrow-left"></i> Back</a>
            <h4 class="mt-2 mb-0">{{ $subject->name }} — Recordings</h4>
        </div>
    </div>

    <div class="container my-4">
        @if($recordings->isEmpty())
            <p class="text-muted">No recordings for this subject yet.</p>
        @else
            <div class="row g-3">
                @foreach($recordings as $recording)
                    <div class="col-md-4">
                        <a href="{{ route('student.recordings.show', $recording->id) }}" class="text-decoration-none">
                            <div class="card rec-card p-3">
                                <i class="bi bi-play-circle text-dark" style="font-size: 1.5rem;"></i>
                                <h6 class="text-dark mt-2 mb-0">{{ $recording->title }}</h6>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
