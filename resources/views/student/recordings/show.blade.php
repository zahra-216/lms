@extends('layouts.student')
@section('title')
{{ $recording->title }}
@endsection
@push('styles')
<style>
.navy-header { background: #012147; color: #fff; padding: 20px;
}
.video-wrapper { position: relative; padding-bottom: 56.25%; height: 0; border-radius: 10px; overflow: hidden;
}
.video-wrapper iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;
}
</style>
@endpush
@section('content')
    <div class="navy-header">
        <div class="container">
            <a href="{{ route('student.recordings.subject', $subject->id) }}" class="text-white text-decoration-none"><i class="bi bi-arrow-left"></i> Back</a>
            <h4 class="mt-2 mb-0">{{ $recording->title }}</h4>
        </div>
    </div>

    <div class="container my-4">
        <div class="video-wrapper">
            <iframe src="https://www.youtube.com/embed/{{ $recording->youtube_video_id }}" allowfullscreen></iframe>
        </div>
    </div>
@endsection
