<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Lecture Videos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>{{ $subject->code }} - {{ $subject->name }} — Lecture Videos</h2>
        <a href="{{ route('lecturer.videos.create', $subject->id) }}" class="btn btn-primary">+ Add Video</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($subject->videos && $subject->videos->count())
        <table class="table table-bordered bg-white">
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
                        <span class="badge bg-{{ $video->is_published ? 'success' : 'secondary' }}">
                            {{ $video->is_published ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        @if($video->type === 'file' && $video->video_path)
                            <a href="{{ asset('storage/' . $video->video_path) }}" target="_blank">View</a>
                        @elseif($video->video_url)
                            <a href="{{ $video->video_url }}" target="_blank">Open Link</a>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('lecturer.videos.edit', [$subject->id, $video->id]) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('lecturer.videos.destroy', [$subject->id, $video->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this video?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted mt-3">No lecture videos uploaded yet for this subject.</p>
    @endif
</div>
</body>
</html>