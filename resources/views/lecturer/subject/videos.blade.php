<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Lecture Videos</title>
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
    <h2>{{ $subject->code }} - {{ $subject->name }} — Lecture Videos</h2>

    <p class="text-muted mt-3">Lecture video uploads aren't set up yet — this section is a placeholder until that feature is built.</p>
</div>
</body>
</html>