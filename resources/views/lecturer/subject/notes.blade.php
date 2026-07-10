<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Notes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background:#f4f6f9; font-family:'Segoe UI', sans-serif; padding:40px; }
    .container { max-width:800px; margin:auto; }
    h2 { color:#012147; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2>{{ $subject->code }} - {{ $subject->name }} — Notes</h2>

    @if($subject->notes && $subject->notes->count())
        <ul class="list-group mt-3">
            @foreach($subject->notes as $note)
                <li class="list-group-item">{{ $note->title ?? $note->name ?? 'Untitled note' }}</li>
            @endforeach
        </ul>
    @else
        <p class="text-muted mt-3">No notes uploaded yet for this subject.</p>
    @endif
</div>
</body>
</html>