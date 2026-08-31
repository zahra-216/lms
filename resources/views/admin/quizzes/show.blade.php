<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $quiz->title }} - {{ $subject->name }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){ body { padding:20px 12px; } }

    .container { max-width:1100px; margin:auto; }

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

    .alert-soft-success{ background:#e7f8ee; color:#0f5c33; border:none; border-radius:12px; }

    .card-box{
        background:#fff; padding:22px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); height:100%;
    }
    .card-box h6{ font-weight:700; color:#012147; font-size:14px; margin-bottom:14px; display:flex; align-items:center; gap:6px; }

    .info-row{ display:flex; justify-content:space-between; padding:7px 0; border-bottom:1px solid #f1f5f9; font-size:13.5px; }
    .info-row:last-child{ border-bottom:none; }
    .info-row span:first-child{ color:#64748b; }
    .info-row span:last-child{ font-weight:600; color:#012147; }

    .badge-status{ font-size:11.5px; font-weight:600; padding:6px 12px; border-radius:20px; }
    .badge-published{ background:#e7f8ee; color:#0f5c33; }
    .badge-draft{ background:#fff4e0; color:#8a5b00; }

    .stat-number{ font-size:28px; font-weight:800; color:#012147; text-align:center; margin-bottom:2px; }
    .stat-label{ font-size:12.5px; color:#64748b; text-align:center; margin-bottom:16px; }

    .qtype-row{ display:flex; align-items:center; gap:8px; font-size:13.5px; margin-bottom:10px; }
    .qtype-badge{ font-size:11px; font-weight:700; padding:4px 9px; border-radius:8px; color:#fff; }
    .qtype-mc{ background:#2563eb; }
    .qtype-tf{ background:#0ea5b7; }
    .qtype-sa{ background:#f59e0b; }

    .btn-navy{ background:#012147; color:#fff; border:none; padding:10px; font-weight:600; border-radius:10px; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:6px; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }
    .btn-soft-danger{ border:1px solid #f4c7c7; color:#b91c1c; background:#fff; padding:10px; font-weight:600; border-radius:10px; width:100%; }
    .btn-soft-danger:hover{ background:#b91c1c; color:#fff; }
    .alert-locked{ background:#fdeaea; color:#8a1f1f; border-radius:10px; padding:12px; font-size:13px; }

    .section-card{
        background:#fff; border-radius:14px; padding:24px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:20px;
    }
    .section-card h6{ font-weight:700; color:#012147; font-size:15px; margin-bottom:18px; display:flex; align-items:center; gap:8px; }

    .question-block{ padding-bottom:18px; margin-bottom:18px; border-bottom:1px solid #f1f5f9; }
    .question-block:last-child{ border-bottom:none; margin-bottom:0; padding-bottom:0; }
    .question-block strong{ color:#012147; }

    .option-line{ font-size:13.5px; color:#475569; margin-bottom:4px; }
    .option-correct{ color:#0f5c33; font-weight:600; }

    table{ font-size:13.5px; }
    th{ color:#64748b; font-weight:600; border-bottom:2px solid #f1f5f9 !important; }
    td{ vertical-align:middle; }

    .btn-soft{
        border-radius:8px; font-weight:600; font-size:12.5px; padding:6px 12px;
        border:1px solid #e2e8f0; background:#fff; color:#012147; text-decoration:none;
        display:inline-flex; align-items:center; gap:5px;
    }
    .btn-soft:hover{ background:#012147; color:#fff; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.subjects.quizzes.index', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back to Quizzes
    </a>

    <div class="page-header">
        <h3><i class="bi bi-puzzle-fill"></i> {{ $quiz->title }}</h3>
        <small>{{ $subject->name }}</small>
    </div>

    @if(session('success'))
        <div class="alert alert-soft-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-3">
        <div class="col-lg-3 col-md-6">
            <div class="card-box">
                <h6><i class="bi bi-info-circle"></i> Quiz Info</h6>
                <div class="info-row">
                    <span>Status</span>
                    <span><span class="badge-status {{ $quiz->is_published ? 'badge-published' : 'badge-draft' }}">{{ $quiz->is_published ? 'Published' : 'Draft' }}</span></span>
                </div>
                <div class="info-row"><span>Questions</span><span>{{ $questions->count() }}</span></div>
                <div class="info-row"><span>Duration</span><span>{{ $quiz->duration_minutes }} min</span></div>
                <div class="info-row"><span>Max Points</span><span>{{ $quiz->total_points }}</span></div>
                <div class="info-row"><span>Attempts</span><span>{{ $quiz->max_attempts }}</span></div>
                <div class="info-row"><span>Grading</span><span>{{ ucfirst($quiz->grading_type) }}</span></div>
                @if($quiz->start_date)
                <div class="info-row"><span>Start</span><span>{{ $quiz->start_date->format('M d, H:i') }}</span></div>
                @endif
                @if($quiz->end_date)
                <div class="info-row"><span>End</span><span>{{ $quiz->end_date->format('M d, H:i') }}</span></div>
                @endif
                @if($quiz->canBeEdited())
                <a href="{{ route('admin.subjects.quizzes.edit', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" class="btn-navy mt-3">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                @endif
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-box">
                <h6><i class="bi bi-graph-up"></i> Analytics</h6>
                <div class="stat-number">{{ $analytics['total_attempts'] }}</div>
                <div class="stat-label">Total Attempts</div>
                <div class="stat-number">{{ $analytics['unique_students'] }}</div>
                <div class="stat-label">Unique Students</div>
                <div class="stat-number">{{ number_format($analytics['average_score'], 1) }}</div>
                <div class="stat-label">Average Score</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-box">
                <h6><i class="bi bi-question-circle"></i> Questions</h6>
                @php
                    $mcCount = $questions->where('type', 'multiple_choice')->count();
                    $tfCount = $questions->where('type', 'true_false')->count();
                    $saCount = $questions->where('type', 'short_answer')->count();
                @endphp
                <div class="qtype-row"><span class="qtype-badge qtype-mc">MC</span> {{ $mcCount }} Multiple Choice</div>
                <div class="qtype-row"><span class="qtype-badge qtype-tf">T/F</span> {{ $tfCount }} True/False</div>
                <div class="qtype-row"><span class="qtype-badge qtype-sa">SA</span> {{ $saCount }} Short Answer</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-box">
                <h6><i class="bi bi-sliders"></i> Actions</h6>
                @if($quiz->canBeEdited())
                <a href="{{ route('admin.subjects.quizzes.edit', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" class="btn-navy mb-2">
                    <i class="bi bi-pencil"></i> Edit Quiz
                </a>
                <form action="{{ route('admin.subjects.quizzes.destroy', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-soft-danger" onclick="return confirm('Are you sure?')">
                        <i class="bi bi-trash"></i> Delete Quiz
                    </button>
                </form>
                @else
                <div class="alert-locked">
                    <i class="bi bi-exclamation-circle"></i> Quiz has started. Cannot edit or delete.
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="section-card">
        <h6><i class="bi bi-list"></i> Quiz Questions</h6>
        @if($questions->count() > 0)
            @foreach($questions as $index => $question)
            <div class="question-block">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div><strong>{{ $index + 1 }}. {{ $question->question_text }}</strong></div>
                    <div class="d-flex gap-1">
                        <span class="qtype-badge {{ $question->type === 'multiple_choice' ? 'qtype-mc' : ($question->type === 'true_false' ? 'qtype-tf' : 'qtype-sa') }}">
                            {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                        </span>
                        <span class="qtype-badge" style="background:#64748b;">{{ $question->points }} pts</span>
                    </div>
                </div>
                @if($question->type === 'multiple_choice')
                    @foreach($question->answers as $answer)
                    <div class="option-line {{ $answer->is_correct ? 'option-correct' : '' }}">
                        <i class="bi {{ $answer->is_correct ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                        {{ $answer->answer_text }}
                    </div>
                    @endforeach
                @elseif($question->type === 'true_false')
                    <div class="option-line option-correct">Correct Answer: {{ ucfirst($question->correct_answer) }}</div>
                @else
                    <div class="option-line">Model Answer: {{ $question->correct_answer }}</div>
                @endif
            </div>
            @endforeach
        @else
            <p class="text-muted text-center py-3 mb-0">No questions in this quiz</p>
        @endif
    </div>

    <div class="section-card">
        <h6><i class="bi bi-file-earmark-check"></i> Student Submissions ({{ $submissions->count() }})</h6>
        @if($submissions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Attempt</th>
                            <th>Started</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $submission)
                        <tr>
                            <td>
                                <strong>{{ $submission->student->name }}</strong><br>
                                <small class="text-muted">{{ $submission->student->email }}</small>
                            </td>
                            <td>{{ $submission->attempt_number }} / {{ $quiz->max_attempts }}</td>
                            <td>{{ $submission->started_at->format('M d, H:i') }}</td>
                            <td>
                                @if($submission->submitted_at)
                                    {{ $submission->submitted_at->format('M d, H:i') }}
                                @else
                                    <span class="text-warning">In Progress</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-status {{ $submission->status === 'graded' ? 'badge-published' : ($submission->status === 'submitted' ? 'badge-editable' : 'badge-draft') }}"
                                      style="{{ $submission->status === 'submitted' ? 'background:#e6f0ff;color:#0a3d91;' : '' }}">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </td>
                            <td>
                                @if($submission->isGraded())
                                    <strong>{{ number_format($submission->getTotalScore(), 2) }} / {{ $quiz->total_points }}</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.subjects.quizzes.submissions.grade', ['subject' => $subject->id, 'quiz' => $quiz->id, 'submission' => $submission->id]) }}" class="btn-soft">
                                    <i class="bi bi-pencil"></i> Grade
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center py-4 mb-0">No submissions yet</p>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
