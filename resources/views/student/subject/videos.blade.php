<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Lecture Videos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="{{ route('student.subject.portal.show', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2>{{ $subject->code }} - {{ $subject->name }} — Lecture Videos</h2>

    @if($subject->videos && $subject->videos->count())
        <table class="table table-bordered bg-white mt-3">
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
                                <a href="{{ asset('storage/' . $video->video_path) }}" target="_blank">Watch</a>
                            @elseif($video->video_url)
                                <a href="{{ $video->video_url }}" target="_blank">Watch</a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted mt-3">No lecture videos uploaded yet for this subject.</p>
    @endif
</div>
</body>
</html>