<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Quiz Result - {{ $quiz->title }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){ body { padding:20px 12px; } }

    .container { max-width:900px; margin:auto; }

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

    .status-card{
        background:#fff; border-radius:14px; padding:28px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); text-align:center; height:100%;
    }
    .status-card h6{ color:#64748b; font-size:13px; font-weight:600; margin-bottom:10px; }
    .status-card .big-icon{ font-size:36px; margin-bottom:10px; }
    .text-success-navy{ color:#0f5c33; }
    .text-warning-navy{ color:#8a5b00; }

    .remarks-card{
        background:#fff; border-radius:14px; padding:22px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:20px;
        border-left:4px solid #0ea5b7;
    }
    .remarks-card h6{ font-weight:700; color:#0ea5b7; font-size:14px; margin-bottom:10px; }

    .section-card{
        background:#fff; border-radius:14px; padding:22px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:20px;
    }
    .section-card h6{ font-weight:700; color:#012147; font-size:15px; margin-bottom:18px; }

    .answer-block{ padding-bottom:18px; margin-bottom:18px; border-bottom:1px solid #f1f5f9; }
    .answer-block:last-child{ border-bottom:none; margin-bottom:0; padding-bottom:0; }
    .pts-badge{ font-size:11px; font-weight:700; padding:4px 9px; border-radius:8px; background:#64748b; color:#fff; }

    .answer-box{ background:#f8fafc; border-radius:10px; padding:14px; margin-top:8px; font-size:13.5px; }
    .answer-tag{ font-size:11.5px; font-weight:600; padding:4px 10px; border-radius:20px; display:inline-flex; align-items:center; gap:4px; margin-top:8px; }
    .answer-tag-correct{ color:#0f5c33; background:#e7f8ee; }
    .answer-tag-incorrect{ color:#8a1f1f; background:#fdeaea; }

    .alert-soft-info{ background:#e6f0ff; color:#0a3d91; border:none; border-radius:12px; padding:16px; }

    .btn-navy{ background:#012147; color:#fff; border:none; padding:12px 24px; font-weight:600; border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('student.quiz.index', $quiz->subject_id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back to Quizzes
    </a>

    <div class="page-header">
        <h3><i class="bi bi-check-circle"></i> Quiz Submitted</h3>
        <small>{{ $quiz->title }}</small>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="status-card">
                <h6>Submission Status</h6>
                <div class="big-icon text-success-navy"><i class="bi bi-check-circle-fill"></i></div>
                <div class="fw-bold text-success-navy mb-2" style="font-size:18px;">Submitted</div>
                <p class="text-muted mb-0" style="font-size:13px;">
                    Submitted at: {{ $submission->submitted_at->format('M d, Y H:i') }}
                </p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="status-card">
                <h6>Grading Status</h6>
                @if($submission->isGraded())
                    <div class="big-icon text-success-navy"><i class="bi bi-clipboard-check"></i></div>
                    <div class="fw-bold text-success-navy mb-2" style="font-size:18px;">Graded</div>
                    <p class="mb-0" style="font-size:14px;">
                        <strong>Score: {{ number_format($submission->getTotalScore(), 2) }} / {{ $quiz->total_points }}</strong>
                    </p>
                @else
                    <div class="big-icon text-warning-navy"><i class="bi bi-clock-history"></i></div>
                    <div class="fw-bold text-warning-navy mb-2" style="font-size:18px;">Pending</div>
                    <p class="text-muted mb-0" style="font-size:13px;">Waiting for grading by instructor</p>
                @endif
            </div>
        </div>
    </div>

    @if($submission->isGraded())
        @if($submission->lecturer_remarks)
        <div class="remarks-card">
            <h6><i class="bi bi-chat-left-quote"></i> Instructor Remarks</h6>
            <p class="mb-0" style="font-size:14px;">{{ $submission->lecturer_remarks }}</p>
        </div>
        @endif

        @if($canShowAnswers)
        <div class="section-card">
            <h6><i class="bi bi-file-earmark"></i> Review Your Answers</h6>
            @php $qIndex = 1; @endphp
            @foreach($answersGrouped as $questionId => $answers)
            @php
                $question = $answers->first()->question;
                $answer = $answers->first();
            @endphp
            <div class="answer-block">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <strong>Q{{ $qIndex }}. {{ $question->question_text }}</strong>
                    <span class="pts-badge">{{ $question->points }} pts</span>
                </div>

                @if($question->type === 'multiple_choice' || $question->type === 'true_false')
                    <div class="answer-box">
                        <strong>Your Answer:</strong> {{ $answer->answer?->answer_text ?? 'Not answered' }}<br>
                        @php $correct = $question->answers()->where('is_correct', true)->first(); @endphp
                        <strong class="text-success">Correct Answer:</strong> {{ $correct->answer_text }}
                        <div>
                            @if($answer->is_correct)
                                <span class="answer-tag answer-tag-correct"><i class="bi bi-check"></i> Correct</span>
                            @else
                                <span class="answer-tag answer-tag-incorrect"><i class="bi bi-x"></i> Incorrect</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="answer-box mb-2">
                        <strong>Your Answer:</strong><br>
                        {{ $answer->answer_text }}
                    </div>
                    <div class="answer-box">
                        <strong class="text-success">Model Answer:</strong><br>
                        {{ $question->correct_answer }}
                    </div>
                @endif
            </div>
            @php $qIndex++; @endphp
            @endforeach
        </div>
        @else
        <div class="alert-soft-info">
            <i class="bi bi-info-circle"></i>
            Your answers will be available after grading is complete.
        </div>
        @endif
    @else
    <div class="alert-soft-info">
        <i class="bi bi-clock-history"></i>
        Your quiz is waiting to be graded by the instructor. Check back later for your score and feedback.
    </div>
    @endif

    <div class="mt-4 text-center">
        <a href="{{ route('student.quiz.index', $quiz->subject_id) }}" class="btn-navy">
            <i class="bi bi-arrow-left"></i> Back to Quizzes
        </a>
    </div>
</div>
</body>
</html>