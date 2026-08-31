<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $quiz->title }} - {{ $subject->name }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root{ --navy:#0a2452; --navy-light:#153a7a; --blue:#2563eb; --bg:#f5f7fb; --border:#e6eaf1; --muted:#64748b; }
    *{ font-family:'Inter', sans-serif; }
    body{ background:var(--bg); padding:36px 16px 60px; }
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .page-header h3 { margin: 0; }
    .page-header p { margin: 8px 0 0 0; }
</style>
</head>
<body>

<div class="container-fluid mt-4">
    <a href="{{ route('lecturer.quizzes.index', $subject->id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Quizzes
    </a>

    <div class="page-header mb-4">
        <h3><i class="bi bi-puzzle-fill"></i> {{ $quiz->title }}</h3>
        <p class="text-muted">{{ $subject->name }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title text-primary"><i class="bi bi-info-circle"></i> Quiz Info</h6>
                    <hr>
                    <dl class="row small">
                        <dt class="col-6">Status:</dt>
                        <dd class="col-6">
                            <span class="badge {{ $quiz->is_published ? 'bg-success' : 'bg-warning' }}">
                                {{ $quiz->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </dd>
                        <dt class="col-6">Questions:</dt>
                        <dd class="col-6"><strong>{{ $questions->count() }}</strong></dd>
                        <dt class="col-6">Duration:</dt>
                        <dd class="col-6">{{ $quiz->duration_minutes }} min</dd>
                        <dt class="col-6">Max Points:</dt>
                        <dd class="col-6">{{ $quiz->total_points }}</dd>
                        <dt class="col-6">Attempts:</dt>
                        <dd class="col-6">{{ $quiz->max_attempts }}</dd>
                        <dt class="col-6">Grading:</dt>
                        <dd class="col-6">{{ ucfirst($quiz->grading_type) }}</dd>
                        @if($quiz->start_date)
                        <dt class="col-12">Start:</dt>
                        <dd class="col-12 small">{{ $quiz->start_date->format('M d, Y H:i') }}</dd>
                        @endif
                        @if($quiz->end_date)
                        <dt class="col-12">End:</dt>
                        <dd class="col-12 small">{{ $quiz->end_date->format('M d, Y H:i') }}</dd>
                        @endif
                    </dl>
                    <hr>
                    <div class="d-grid gap-2">
                        @if($quiz->canBeEdited())
                        <a href="{{ route('lecturer.quizzes.edit', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title text-success"><i class="bi bi-graph-up"></i> Analytics</h6>
                    <hr>
                    <div class="text-center mb-3">
                        <div class="text-muted small">Total Attempts</div>
                        <h3 class="text-primary">{{ $analytics['total_attempts'] }}</h3>
                    </div>
                    <div class="text-center mb-3">
                        <div class="text-muted small">Unique Students</div>
                        <h3 class="text-info">{{ $analytics['unique_students'] }}</h3>
                    </div>
                    <div class="text-center">
                        <div class="text-muted small">Average Score</div>
                        <h3 class="text-success">{{ number_format($analytics['average_score'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title text-info"><i class="bi bi-question-circle"></i> Questions</h6>
                    <hr>
                    @php
                        $mcCount = $questions->where('type', 'multiple_choice')->count();
                        $tfCount = $questions->where('type', 'true_false')->count();
                        $saCount = $questions->where('type', 'short_answer')->count();
                    @endphp
                    <div class="small">
                        <div class="mb-2">
                            <span class="badge bg-primary">MC</span>
                            <strong>{{ $mcCount }}</strong> Multiple Choice
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-info">T/F</span>
                            <strong>{{ $tfCount }}</strong> True/False
                        </div>
                        <div>
                            <span class="badge bg-secondary">SA</span>
                            <strong>{{ $saCount }}</strong> Short Answer
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title text-warning"><i class="bi bi-sliders"></i> Actions</h6>
                    <hr>
                    <div class="d-grid gap-2">
                        @if($quiz->canBeEdited())
                        <a href="{{ route('lecturer.quizzes.edit', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i> Edit Quiz
                        </a>
                        <form action="{{ route('lecturer.quizzes.destroy', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Are you sure?')">
                                <i class="bi bi-trash"></i> Delete Quiz
                            </button>
                        </form>
                        @else
                        <div class="alert alert-danger small mb-0">
                            <i class="bi bi-exclamation-circle"></i> Quiz has started. Cannot edit or delete.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="bi bi-list"></i> Quiz Questions</h6>
        </div>
        <div class="card-body">
            @if($questions->count() > 0)
                @foreach($questions as $index => $question)
                <div class="mb-4 pb-4 border-bottom" @if($loop->last) style="border-bottom:none!important;" @endif>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6>{{ $index + 1 }}. {{ $question->question_text }}</h6>
                        <div>
                            <span class="badge bg-secondary">{{ $question->points }} pts</span>
                            <span class="badge {{ $question->type === 'multiple_choice' ? 'bg-primary' : ($question->type === 'true_false' ? 'bg-info' : 'bg-warning') }}">
                                {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                            </span>
                        </div>
                    </div>
                    @if($question->type === 'multiple_choice')
                        <div class="small">
                            @foreach($question->answers as $answer)
                            <div class="mb-1">
                                <i class="bi {{ $answer->is_correct ? 'bi-check-circle-fill text-success' : 'bi-circle' }}"></i>
                                {{ $answer->answer_text }}
                                @if($answer->is_correct) <span class="badge bg-success">Correct</span> @endif
                            </div>
                            @endforeach
                        </div>
                    @elseif($question->type === 'true_false')
                        <div class="small">
                            Correct Answer: <strong>{{ ucfirst($question->correct_answer) }}</strong>
                        </div>
                    @else
                        <div class="small text-muted">
                            Model Answer: {{ $question->correct_answer }}
                        </div>
                    @endif
                </div>
                @endforeach
            @else
                <p class="text-muted text-center py-4">No questions in this quiz</p>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0"><i class="bi bi-file-earmark-check"></i> Student Submissions ({{ $submissions->count() }})</h6>
        </div>
        <div class="card-body">
            @if($submissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
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
                                    <span class="badge {{ $submission->status === 'graded' ? 'bg-success' : ($submission->status === 'submitted' ? 'bg-info' : 'bg-warning') }}">
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
                                    <a href="{{ route('lecturer.quizzes.submissions.grade', ['subject' => $subject->id, 'quiz' => $quiz->id, 'submission' => $submission->id]) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Grade
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-5">No submissions yet</p>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>