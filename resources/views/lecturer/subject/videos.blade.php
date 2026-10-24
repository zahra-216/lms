@extends('layouts.lecturer')
@section('title')
{{ $subject->name }} - Lecture Videos
@endsection
@push('styles')
<style>
@media (max-width:576px) {
.page-header .add-btn { width:100%; text-align:center;
}
}
table.videos-table { border-collapse:separate; border-spacing:0;
}
table.videos-table thead th {
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap;
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
.badge-pill { padding:6px 12px; border-radius:20px; font-weight:600;
}
.badge-yes { background:#dcfce7; color:#15803d;
}
.badge-no { background:#e2e8f0; color:#64748b;
}
.btn-edit { background:#fef9c3; color:#a16207; border:none;
}
.btn-edit:hover { background:#fde68a; color:#a16207;
}
.btn-delete { background:#fee2e2; color:#b91c1c; border:none;
}
.btn-delete:hover { background:#fecaca; color:#b91c1c;
}
</style>
@endpush
@section('content')
<div class="container">
    <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <h2><i class="bi bi-camera-video"></i> {{ $subject->code }} - {{ $subject->name }} — Lecture Videos</h2>
        <a href="{{ route('lecturer.videos.create', $subject->id) }}" class="add-btn">
            <i class="bi bi-plus-circle"></i> Add Video
        </a>
    </div>

    

    <div class="card-box">
    @if($subject->videos && $subject->videos->count())
    <div class="table-responsive">
        <table class="table videos-table align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Published</th>
                    <th>Watch</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subject->videos as $video)
                <tr>
                    <td>{{ $video->title }}</td>
                    <td>{{ ucfirst($video->type) }}</td>
                    <td>
                        <span class="badge-pill {{ $video->is_published ? 'badge-yes' : 'badge-no' }}">
                            {{ $video->is_published ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        @if($video->type === 'file' && $video->video_path)
                            <a href="{{ asset('storage/' . $video->video_path) }}" target="_blank"><i class="bi bi-play-circle"></i> View</a>
                        @elseif($video->video_url)
                            <a href="{{ $video->video_url }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Open Link</a>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('lecturer.videos.edit', [$subject->id, $video->id]) }}" class="btn btn-sm btn-edit">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form action="{{ route('lecturer.videos.destroy', [$subject->id, $video->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this video?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-delete">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <p class="text-muted mb-0">No lecture videos uploaded yet for this subject.</p>
    @endif
    </div>
</div>
@endsection
