<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $recording->title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f6f9; }
        .navy-header { background: #012147; color: #fff; padding: 20px; }
        .video-wrapper { position: relative; padding-bottom: 56.25%; height: 0; border-radius: 10px; overflow: hidden; }
        .video-wrapper iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
    </style>
</head>
<body>

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

</body>
</html>