<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Quizzes - {{ $subject->name }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){ body { padding:20px 12px; } }

    .container { max-width:1100px; margin:auto; }

    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:24px 28px; margin-bottom:26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h3{ margin:0; font-weight:700; font-size:20px; }
    .page-header small{ opacity:0.85; }

    .quiz-card{
        background:#fff; border-radius:14px; padding:22px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06);
        height:100%; display:flex; flex-direction:column;
    }
    .quiz-title{ font-weight:700; color:#012147; font-size:17px; margin-bottom:6px; }
    .quiz-desc{ color:#64748b; font-size:13.5px; margin-bottom:14px; }

    .quiz-meta{ font-size:13px; color:#475569; margin-bottom:14px; }
    .quiz-meta div{ margin-bottom:4px; display:flex; align-items:center; gap:6px; }

    .badge-status{ font-size:11.5px; font-weight:600; padding:6px 12px; border-radius:20px; }
    .badge-info{ background:#e6f0ff; color:#0a3d91; }
    .badge-danger{ background:#fdeaea; color:#8a1f1f; }
    .badge-warn{ background:#fff4e0; color:#8a5b00; }

    .btn-navy{ background:#012147; color:#fff; border:none; padding:11px; font-weight:600; border-radius:10px; width:100%; margin-top:auto; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }
    .btn-navy:disabled{ background:#94a3b8; }

    .empty-box{
        background:#fff; border-radius:14px; padding:60px 20px; text-align:center;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); color:#64748b;
    }
    .empty-box i{ font-size:2.5rem; color:#cbd5e1; }
</style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h3><i class="bi bi-puzzle"></i> Quizzes</h3>
        <small>{{ $subject->name }} — Available quizzes for this subject</small>
    </div>

    @if($quizzes->count() > 0)
        <div class="row g-3">
            @foreach($quizzes as $quiz)
            <div class="col-md-6 col-lg-4">
                <div class="quiz-card">
                    <div class="quiz-title">{{ $quiz->title }}</div>
                    <div class="quiz-desc">{{ Str::limit($quiz->description, 90) }}</div>

                    <div class="quiz-meta">
                        <div><i class="bi bi-question-circle"></i> {{ $quiz->questions->count() }} Questions</div>
                        <div><i class="bi bi-hourglass-split"></i> {{ $quiz->duration_minutes }} minutes</div>
                        <div><i class="bi bi-award"></i> {{ $quiz->total_points }} points</div>
                        @if($quiz->start_date)
                        <div><i class="bi bi-calendar-event"></i> {{ $quiz->start_date->format('M d, Y H:i') }}</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        @php
                            $attempts = $quiz->student_attempt;
                            $canAttempt = $quiz->can_attempt;
                        @endphp
                        <span class="badge-status badge-info">Attempts: {{ $attempts }} / {{ $quiz->max_attempts }}</span>
                        @if(!$canAttempt && $attempts >= $quiz->max_attempts)
                            <span class="badge-status badge-danger">Max attempts reached</span>
                        @elseif(!$quiz->isAvailable())
                            <span class="badge-status badge-warn">Not available</span>
                        @endif
                    </div>

                    @if($canAttempt)
                    <form action="{{ route('student.quiz.start', $quiz->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-navy">
                            <i class="bi bi-play-circle"></i> Start Quiz
                        </button>
                    </form>
                    @elseif($quiz->student_attempt > 0)
                    @php
                        $lastSubmission = $quiz->submissions()
                            ->where('student_id', session('student_id'))
                            ->latest('started_at')
                            ->first();
                    @endphp
                    @if($lastSubmission)
                    <a href="{{ route('student.quiz.result', $lastSubmission->id) }}" class="btn-navy" style="text-align:center;">
                        <i class="bi bi-clipboard-check"></i> View Result
                    </a>
                    @else
                    <button class="btn-navy" disabled>
                        <i class="bi bi-lock"></i> Not Available
                    </button>
                    @endif
                    @else
                    <button class="btn-navy" disabled>
                        <i class="bi bi-lock"></i> Not Available
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="empty-box">
            <i class="bi bi-inbox"></i>
            <p class="mt-3 mb-0">No quizzes available for this subject</p>
        </div>
    @endif
</div>
</body>
</html>