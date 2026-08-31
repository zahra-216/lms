<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Grade Submission - {{ $quiz->title }}</title>
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
    <a href="{{ route('lecturer.quizzes.show', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Quiz
    </a>

    <div class="page-header mb-4">
        <h3><i class="bi bi-clipboard-check"></i> Grade Submission</h3>
        <p class="text-muted">{{ $subject->name }} | {{ $quiz->title }}</p>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="card-title text-primary"><i class="bi bi-person-circle"></i> Student Info</h6>
                    <hr>
                    <div class="mb-3">
                        <strong>{{ $submission->student->name }}</strong><br>
                        <small class="text-muted">{{ $submission->student->email }}</small>
                    </div>
                    <dl class="row small mb-0">
                        <dt class="col-6">Attempt:</dt>
                        <dd class="col-6">{{ $submission->attempt_number }} / {{ $quiz->max_attempts }}</dd>
                        <dt class="col-6">Started:</dt>
                        <dd class="col-6">{{ $submission->started_at->format('M d H:i') }}</dd>
                        <dt class="col-6">Submitted:</dt>
                        <dd class="col-6">{{ $submission->submitted_at?->format('M d H:i') ?? 'N/A' }}</dd>
                        <dt class="col-6">Duration:</dt>
                        <dd class="col-6">
                            @if($submission->submitted_at)
                                {{ $submission->started_at->diffInMinutes($submission->submitted_at) }} min
                            @else
                                —
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-info"><i class="bi bi-info-circle"></i> Scoring</h6>
                    <hr>
                    @if($quiz->grading_type !== 'manual')
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Auto Score</label>
                        <div class="alert alert-info mb-0">
                            <strong>{{ $submission->automatic_score ?? 'N/A' }}</strong> / {{ $quiz->total_points }}
                        </div>
                    </div>
                    @endif
                    @if($quiz->grading_type !== 'automatic')
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Manual Score</label>
                        <div class="alert alert-success mb-0">
                            <strong>{{ $submission->manual_score ?? 'Not graded' }}</strong> / {{ $quiz->total_points }}
                        </div>
                    </div>
                    @endif
                    <hr>
                    <div class="alert alert-warning small mb-0">
                        <strong>Status:</strong> {{ ucfirst($submission->status) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <form action="{{ route('lecturer.quizzes.submissions.saveGrades', ['subject' => $subject->id, 'quiz' => $quiz->id, 'submission' => $submission->id]) }}" method="POST">
                @csrf

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-file-earmark"></i> Student Answers</h6>
                    </div>
                    <div class="card-body">
                        @php $qIndex = 1; @endphp
                        @foreach($submission->answers as $answer)
                        @php
                            $question = $answer->question;
                            $isCorrect = $answer->is_correct;
                        @endphp
                        <div class="mb-4 pb-4 border-bottom" @if($loop->last) style="border-bottom:none!important;" @endif>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">Q{{ $qIndex }}. {{ $question->question_text }}</h6>
                                <span class="badge bg-secondary">{{ $question->points }} pts</span>
                            </div>

                            @if($question->type === 'multiple_choice')
                                <div class="bg-light p-3 rounded mb-2">
                                    <small class="d-block text-muted mb-2">Student's Answer:</small>
                                    <strong>{{ $answer->answer?->answer_text ?? 'Not answered' }}</strong>
                                    @php
                                        $correctAnswer = $question->answers()->where('is_correct', true)->first();
                                    @endphp
                                    <div class="mt-2">
                                        <strong class="text-success"><i class="bi bi-check-circle"></i> Correct Answer:</strong> {{ $correctAnswer->answer_text }}
                                    </div>
                                    <div class="mt-2">
                                        @if($isCorrect)
                                            <span class="badge bg-success"><i class="bi bi-check"></i> Correct</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x"></i> Incorrect</span>
                                        @endif
                                    </div>
                                </div>

                            @elseif($question->type === 'true_false')
                                <div class="bg-light p-3 rounded mb-2">
                                    <small class="d-block text-muted mb-2">Student's Answer:</small>
                                    <strong>{{ ucfirst($answer->answer?->answer_text ?? 'Not answered') }}</strong>
                                    <div class="mt-2">
                                        <strong class="text-success"><i class="bi bi-check-circle"></i> Correct Answer:</strong> {{ ucfirst($question->correct_answer) }}
                                    </div>
                                    <div class="mt-2">
                                        @if($isCorrect)
                                            <span class="badge bg-success"><i class="bi bi-check"></i> Correct</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x"></i> Incorrect</span>
                                        @endif
                                    </div>
                                </div>

                            @else
                                <div class="bg-light p-3 rounded mb-2">
                                    <small class="d-block text-muted mb-2">Student's Answer:</small>
                                    <p class="mb-0 border-bottom pb-2">{{ $answer->answer_text ?? 'Not answered' }}</p>
                                    <small class="d-block text-muted mt-2 mb-2">Model Answer:</small>
                                    <p class="mb-2 text-success">{{ $question->correct_answer }}</p>

                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" name="answer_corrections[{{ $answer->id }}]" value="1"
                                               id="correct{{ $answer->id }}" {{ $isCorrect ? 'checked' : '' }}>
                                        <label class="form-check-label" for="correct{{ $answer->id }}">
                                            Mark as Correct
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @php $qIndex++; @endphp
                        @endforeach
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-pencil-square"></i> Grading</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Manual Score <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="manual_score" class="form-control" min="0" max="{{ $quiz->total_points }}"
                                       value="{{ $submission->manual_score ?? '' }}" step="0.1" required>
                                <span class="input-group-text">/ {{ $quiz->total_points }}</span>
                            </div>
                            <small class="d-block mt-1 text-muted">Enter the manual score for this submission</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Remarks/Feedback</label>
                            <textarea name="lecturer_remarks" class="form-control" rows="5" placeholder="Enter any feedback for the student...">{{ $submission->lecturer_remarks }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Save Grade & Remarks
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>