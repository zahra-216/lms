<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Grade Submission - {{ $quiz->title }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){ body { padding:20px 12px; } }

    .container { max-width:1200px; margin:auto; }

    .back-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .back-btn:hover{ background:#012147; color:#fff; }

    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:24px 28px; margin:18px 0 26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h3{ margin:0; font-weight:700; font-size:20px; }
    .page-header small{ opacity:0.85; }

    .section-card{
        background:#fff; border-radius:14px; padding:22px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:20px;
    }
    .section-card h6{ font-weight:700; color:#012147; font-size:14px; margin-bottom:16px; display:flex; align-items:center; gap:6px; }

    .info-row{ display:flex; justify-content:space-between; padding:7px 0; border-bottom:1px solid #f1f5f9; font-size:13.5px; }
    .info-row:last-child{ border-bottom:none; }
    .info-row span:first-child{ color:#64748b; }
    .info-row span:last-child{ font-weight:600; color:#012147; }

    .score-box{ border-radius:10px; padding:12px 14px; font-size:13.5px; margin-bottom:12px; }
    .score-box-auto{ background:#e6f0ff; color:#0a3d91; }
    .score-box-manual{ background:#e7f8ee; color:#0f5c33; }
    .score-box-status{ background:#fff4e0; color:#8a5b00; }

    .form-label{ font-weight:600; color:#012147; font-size:14px; }
    .form-control, .form-select{ border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px; }
    .form-control:focus, .form-select:focus{ border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }
    .input-group-text{ border-radius:0 10px 10px 0; background:#f8fafc; font-weight:600; color:#012147; }

    .btn-navy{ background:#012147; color:#fff; border:none; padding:13px; font-weight:600; border-radius:10px; width:100%; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }

    .answer-block{ padding-bottom:18px; margin-bottom:18px; border-bottom:1px solid #f1f5f9; }
    .answer-block:last-child{ border-bottom:none; margin-bottom:0; padding-bottom:0; }
    .pts-badge{ font-size:11px; font-weight:700; padding:4px 9px; border-radius:8px; background:#64748b; color:#fff; }

    .answer-box{ background:#f8fafc; border-radius:10px; padding:14px; margin-top:8px; font-size:13.5px; }
    .answer-tag{ font-size:11.5px; font-weight:600; color:#8a5b00; background:#fff4e0; padding:4px 10px; border-radius:20px; display:inline-flex; align-items:center; gap:4px; }
    .answer-tag-correct{ color:#0f5c33; background:#e7f8ee; }
    .answer-tag-incorrect{ color:#8a1f1f; background:#fdeaea; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.quizzes.show', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back to Quiz
    </a>

    <div class="page-header">
        <h3><i class="bi bi-clipboard-check"></i> Grade Submission</h3>
        <small>{{ $subject->name }} | {{ $quiz->title }}</small>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="section-card">
                <h6><i class="bi bi-person-circle"></i> Student Info</h6>
                <div class="mb-2">
                    <strong>{{ $submission->student->name }}</strong><br>
                    <small class="text-muted">{{ $submission->student->email }}</small>
                </div>
                <div class="info-row"><span>Attempt</span><span>{{ $submission->attempt_number }} / {{ $quiz->max_attempts }}</span></div>
                <div class="info-row"><span>Started</span><span>{{ $submission->started_at->format('M d H:i') }}</span></div>
                <div class="info-row"><span>Submitted</span><span>{{ $submission->submitted_at?->format('M d H:i') ?? 'N/A' }}</span></div>
                <div class="info-row">
                    <span>Duration</span>
                    <span>
                        @if($submission->submitted_at)
                            {{ $submission->started_at->diffInMinutes($submission->submitted_at) }} min
                        @else
                            —
                        @endif
                    </span>
                </div>
            </div>

            <div class="section-card">
                <h6><i class="bi bi-info-circle"></i> Scoring</h6>
                @if($quiz->grading_type !== 'manual')
                <div class="score-box score-box-auto">
                    <div class="mb-1" style="font-size:12px; font-weight:600;">Auto Score</div>
                    <strong>{{ $submission->automatic_score ?? 'N/A' }}</strong> / {{ $quiz->total_points }}
                </div>
                @endif
                @if($quiz->grading_type !== 'automatic')
                <div class="score-box score-box-manual">
                    <div class="mb-1" style="font-size:12px; font-weight:600;">Manual Score</div>
                    <strong>{{ $submission->manual_score ?? 'Not graded' }}</strong> / {{ $quiz->total_points }}
                </div>
                @endif
                <div class="score-box score-box-status mb-0">
                    <strong>Status:</strong> {{ ucfirst($submission->status) }}
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <form action="{{ route('lecturer.quizzes.submissions.saveGrades', ['subject' => $subject->id, 'quiz' => $quiz->id, 'submission' => $submission->id]) }}" method="POST">
                @csrf

                @if($errors->any())
                <div class="alert alert-danger rounded-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="section-card">
                    <h6><i class="bi bi-file-earmark"></i> Student Answers</h6>
                    @php $qIndex = 1; @endphp
                    @foreach($submission->answers as $answer)
                    @continue(!$answer->question)
                    @php
                        $question = $answer->question;
                        $isCorrect = $answer->is_correct;
                    @endphp
                    <div class="answer-block">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <strong>Q{{ $qIndex }}. {{ $question->question_text }}</strong>
                            <span class="pts-badge">{{ $question->points }} pts</span>
                        </div>

                        @if($question->type === 'multiple_choice')
                            <div class="answer-box">
                                <small class="d-block text-muted mb-2">Student's Answer:</small>
                                <strong>{{ $answer->answer?->answer_text ?? 'Not answered' }}</strong>
                                @php $correctAnswer = $question->answers()->where('is_correct', true)->first(); @endphp
                                <div class="mt-2"><strong class="text-success">Correct Answer:</strong> {{ $correctAnswer->answer_text }}</div>
                                <div class="mt-2">
                                    @if($isCorrect)
                                        <span class="answer-tag answer-tag-correct"><i class="bi bi-check"></i> Correct</span>
                                    @else
                                        <span class="answer-tag answer-tag-incorrect"><i class="bi bi-x"></i> Incorrect</span>
                                    @endif
                                </div>
                            </div>
                        @elseif($question->type === 'true_false')
                            <div class="answer-box">
                                <small class="d-block text-muted mb-2">Student's Answer:</small>
                                <strong>{{ ucfirst($answer->answer?->answer_text ?? 'Not answered') }}</strong>
                                <div class="mt-2"><strong class="text-success">Correct Answer:</strong> {{ ucfirst($question->correct_answer) }}</div>
                                <div class="mt-2">
                                    @if($isCorrect)
                                        <span class="answer-tag answer-tag-correct"><i class="bi bi-check"></i> Correct</span>
                                    @else
                                        <span class="answer-tag answer-tag-incorrect"><i class="bi bi-x"></i> Incorrect</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="answer-box">
                                <small class="d-block text-muted mb-2">Student's Answer:</small>
                                <p class="mb-2 pb-2 border-bottom">{{ $answer->answer_text ?? 'Not answered' }}</p>
                                <small class="d-block text-muted mb-1">Model Answer:</small>
                                <p class="mb-2 text-success">{{ $question->correct_answer }}</p>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="answer_corrections[{{ $answer->id }}]" value="1"
                                           id="correct{{ $answer->id }}" {{ $isCorrect ? 'checked' : '' }}>
                                    <label class="form-check-label" for="correct{{ $answer->id }}">Mark as Correct</label>
                                </div>
                            </div>
                        @endif
                    </div>
                    @php $qIndex++; @endphp
                    @endforeach
                </div>

                <div class="section-card">
                    <h6><i class="bi bi-pencil-square"></i> Grading</h6>
                    <div class="mb-4">
                        <label class="form-label">Manual Score <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="manual_score" class="form-control" min="0" max="{{ $quiz->total_points }}"
                                   value="{{ $submission->manual_score ?? '' }}" step="0.1" required>
                            <span class="input-group-text">/ {{ $quiz->total_points }}</span>
                        </div>
                        <small class="d-block mt-1 text-muted">Enter the manual score for this submission</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Remarks/Feedback</label>
                        <textarea name="lecturer_remarks" class="form-control" rows="5" placeholder="Enter any feedback for the student...">{{ $submission->lecturer_remarks }}</textarea>
                    </div>

                    <button type="submit" class="btn-navy">
                        <i class="bi bi-check-circle"></i> Save Grade & Remarks
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>