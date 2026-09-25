<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject->name }} — Recordings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f6f9; }
        .navy-header { background: #012147; color: #fff; padding: 20px; }
        .rec-card { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    </style>
</head>
<body>

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

</body>
</html>