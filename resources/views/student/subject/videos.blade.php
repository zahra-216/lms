@extends('layouts.student')
@section('title')
{{ $subject->name }} - Lecture Videos
@endsection
@section('back', route('student.subject.portal.show', $subject->id))
@push('styles')
<style>
table.videos-table { border-collapse:separate; border-spacing:0;
}
table.videos-table thead th {
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap; text-align:left;
}
table.videos-table thead th:first-child { border-top-left-radius:10px;
}
table.videos-table thead th:last-child { border-top-right-radius:10px;
}
table.videos-table tbody td { vertical-align:middle; padding:10px;
}
table.videos-table tbody tr:nth-child(even) { background:#f8fafc;
}
table.videos-table tbody tr:hover { background:#eef2f9;
}
.watch-link {
        display:inline-flex; align-items:center; gap:6px;
        color:#012147; font-weight:600; text-decoration:none;
        padding:6px 14px; border-radius:20px; background:#eef2f9; font-size:13px;
}
.watch-link:hover { background:#012147; color:#fff;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h2><i class="bi bi-camera-video"></i> {{ $subject->code }} - {{ $subject->name }} — Lecture Videos</h2>
    </div>

    <div class="card-box">
    @if($subject->videos && $subject->videos->count())
        <div class="table-responsive">
        <table class="table videos-table align-middle mb-0">
            <thead>
                <tr><th>Title</th><th>Watch</th></tr>
            </thead>
            <tbody>
                @foreach($subject->videos as $video)
                    @if($video->is_published)
                    <tr>
                        <td>{{ $video->title }}</td>
                        <td>
                            @if($video->type === 'file' && $video->video_path)
                                <a href="{{ asset('storage/' . $video->video_path) }}" target="_blank" class="watch-link">
                                    <i class="bi bi-play-circle"></i> Watch
                                </a>
                            @elseif($video->video_url)
                                <a href="{{ $video->video_url }}" target="_blank" class="watch-link">
                                    <i class="bi bi-play-circle"></i> Watch
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        </div>
    @else
        <div class="empty-state">No lecture videos uploaded yet for this subject.</div>
    @endif
    </div>
</div>
@endsection
