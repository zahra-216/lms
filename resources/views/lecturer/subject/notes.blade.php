<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Notes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>{{ $subject->code }} - {{ $subject->name }} — Notes</h2>
        <a href="{{ route('lecturer.notes.create', $subject->id) }}" class="btn btn-primary">+ Add Note</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($subject->notes && $subject->notes->count())
        <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Published</th>
                    <th>File / Link</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subject->notes as $note)
                <tr>
                    <td>{{ $note->title }}</td>
                    <td>{{ ucfirst($note->type) }}</td>
                    <td>
                        <span class="badge bg-{{ $note->is_published ? 'success' : 'secondary' }}">
                            {{ $note->is_published ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        @if($note->file_path)
                            <a href="{{ route('lecturer.notes.download', $note->id) }}">Download</a>
                        @elseif($note->url)
                            <a href="{{ $note->url }}" target="_blank">Open Link</a>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('lecturer.notes.edit', [$subject->id, $note->id]) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('lecturer.notes.destroy', [$subject->id, $note->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this note?')">
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
        <p class="text-muted mt-3">No notes uploaded yet for this subject.</p>
    @endif
</div>
</body>
</html>