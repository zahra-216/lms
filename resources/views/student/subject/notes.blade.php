<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Notes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="{{ route('student.subject.portal.show', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2>{{ $subject->code }} - {{ $subject->name }} — Notes</h2>

    @if($subject->notes && $subject->notes->count())
        <table class="table table-bordered bg-white mt-3">
            <thead>
                <tr><th>Title</th><th>File / Link</th></tr>
            </thead>
            <tbody>
                @foreach($subject->notes as $note)
                    @if($note->is_published)
                    <tr>
                        <td>{{ $note->title }}</td>
                        <td>
                            @if($note->file_path)
                                <a href="{{ route('student.note.download', $note->id) }}">Download</a>
                            @elseif($note->url)
                                <a href="{{ $note->url }}" target="_blank">Open Link</a>
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
        <p class="text-muted mt-3">No notes uploaded yet for this subject.</p>
    @endif
</div>
</body>
</html>