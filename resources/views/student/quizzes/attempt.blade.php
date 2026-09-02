<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $quiz->title }} - Attempt</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){ body { padding:20px 12px; } }

    .container { max-width:1200px; margin:auto; }

    .timer-alert{
        background:#fff4e0; color:#8a5b00; border-radius:12px; padding:14px 18px;
        margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;
        font-weight:600; font-size:14px;
    }

    .section-card{
        background:#fff; border-radius:14px; padding:22px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:16px;
    }

    .sidebar-card{
        background:#fff; border-radius:14px; padding:22px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); position:sticky; top:20px;
    }
    .sidebar-card h6{ font-weight:700; color:#012147; font-size:15px; margin-bottom:14px; display:flex; align-items:center; gap:8px; }
    .sidebar-card .info-row{ display:flex; justify-content:space-between; font-size:13.5px; padding:6px 0; border-bottom:1px solid #f1f5f9; }
    .sidebar-card .info-row:last-child{ border-bottom:none; }
    .sidebar-card .info-row span:first-child{ color:#64748b; }
    .sidebar-card .info-row span:last-child{ font-weight:600; color:#012147; }

    .timer-box{
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:12px; padding:16px; text-align:center; margin-top:14px;
    }
    .timer-box h4{ margin:0; font-weight:800; font-size:26px; }
    .timer-box small{ opacity:0.8; }

    .q-badge{ font-size:12px; font-weight:600; color:#012147; }
    .pts-badge{ font-size:11px; font-weight:700; padding:4px 9px; border-radius:8px; background:#64748b; color:#fff; }

    .question-text{ font-size:15.5px; color:#0f172a; margin:12px 0 16px; font-weight:500; }

    .form-check{ padding:10px 14px 10px 40px; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:8px; }
    .form-check:hover{ background:#f8fafc; }
    .form-check-input{ width:18px; height:18px; cursor:pointer; margin-top:2px; }
    .form-check-label{ cursor:pointer; font-size:14px; }

    .form-control{ border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px; }
    .form-control:focus{ border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }

    .btn-navy{ background:#012147; color:#fff; border:none; padding:14px; font-weight:700; border-radius:10px; width:100%; font-size:15px; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }

    @media (max-width: 992px) {
        .sidebar-card { position: relative !important; top: auto !important; margin-bottom:20px; }
    }
</style>
</head>
<body>
<div class="container">
    <div class="timer-alert" id="timerAlert">
        <span><i class="bi bi-hourglass-split"></i> Time Remaining: <span id="timeDisplay">{{ $timeRemaining }}:00</span></span>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-3">
            <div class="sidebar-card">
                <h6><i class="bi bi-puzzle"></i> {{ $quiz->title }}</h6>
                <div class="info-row"><span>Total Questions</span><span>{{ $questions->count() }}</span></div>
                <div class="info-row"><span>Duration</span><span>{{ $quiz->duration_minutes }} min</span></div>
                <div class="timer-box">
                    <h4 id="timer">{{ $timeRemaining }}:00</h4>
                    <small>remaining</small>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <form action="{{ route('student.quiz.submit', $submission->id) }}" method="POST" id="quizForm">
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

                @foreach($questions as $index => $question)
                <div class="section-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="q-badge">Question {{ $index + 1 }} of {{ $questions->count() }}</span>
                        <span class="pts-badge">{{ $question->points }} point{{ $question->points > 1 ? 's' : '' }}</span>
                    </div>

                    <p class="question-text">{{ $question->question_text }}</p>

                    @if($question->type === 'multiple_choice')
                        @foreach($question->answers as $answer)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]"
                                   value="{{ $answer->id }}" id="answer{{ $answer->id }}"
                                   {{ old("answers.{$question->id}") == $answer->id ? 'checked' : (isset($submittedAnswers[$question->id]) && $submittedAnswers[$question->id] == $answer->id ? 'checked' : '') }}>
                            <label class="form-check-label" for="answer{{ $answer->id }}">
                                {{ $answer->answer_text }}
                            </label>
                        </div>
                        @endforeach

                    @elseif($question->type === 'true_false')
                        @php
                            $trueAnswer = $question->answers()->where('answer_text', 'True')->first();
                            $falseAnswer = $question->answers()->where('answer_text', 'False')->first();
                        @endphp
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]"
                                   value="{{ $trueAnswer->id }}" id="true{{ $question->id }}"
                                   {{ (old("answers.{$question->id}") == $trueAnswer->id || (isset($submittedAnswers[$question->id]) && $submittedAnswers[$question->id] === 'True')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="true{{ $question->id }}">True</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]"
                                   value="{{ $falseAnswer->id }}" id="false{{ $question->id }}"
                                   {{ (old("answers.{$question->id}") == $falseAnswer->id || (isset($submittedAnswers[$question->id]) && $submittedAnswers[$question->id] === 'False')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="false{{ $question->id }}">False</label>
                        </div>

                    @else
                        <textarea name="answers[{{ $question->id }}]" class="form-control" rows="4"
                                  placeholder="Enter your answer here...">{{ old("answers.{$question->id}") ?? ($submittedAnswers[$question->id] ?? '') }}</textarea>
                    @endif
                </div>
                @endforeach

                <button type="submit" class="btn-navy" onclick="return confirm('Are you sure? You cannot change your answers after submitting.')">
                    <i class="bi bi-check-circle"></i> Submit Quiz
                </button>
            </form>
        </div>
    </div>
</div>

<script>
let timeRemaining = {{ $timeRemaining * 60 }};

function updateTimer() {
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    const display = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
    document.getElementById('timeDisplay').textContent = display;
    document.getElementById('timer').textContent = display;

    if (timeRemaining <= 0) {
        document.getElementById('quizForm').submit();
        return;
    }

    timeRemaining--;
}

setInterval(updateTimer, 1000);
updateTimer();
</script>
</body>
</html>