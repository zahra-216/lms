<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create Quiz</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){ body { padding:20px 12px; } }

    .container { max-width:700px; margin:auto; }

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

    .card-box{
        background:#fff; padding:26px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06);
    }

    .form-label{ font-weight:600; color:#012147; font-size:14px; }
    .form-control, .form-select{ border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px; }
    .form-control:focus, .form-select:focus{ border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }

    .form-check-input{ width:18px; height:18px; cursor:pointer; }
    .form-check-label{ cursor:pointer; }

    .btn-navy{ background:#012147; color:#fff; border:none; padding:12px; font-weight:600; border-radius:10px; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }
    .btn-outline-navy{ border:1px solid #e2e8f0; color:#012147; padding:12px; font-weight:600; border-radius:10px; background:#fff; }
    .btn-outline-navy:hover{ background:#012147; color:#fff; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.quizzes.index', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back to Quizzes
    </a>

    <div class="page-header">
        <h3><i class="bi bi-plus-circle"></i> Create New Quiz</h3>
        <small>{{ $subject->name }}</small>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-box">
        <form action="{{ route('lecturer.quizzes.store', $subject->id) }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Total Points <span class="text-danger">*</span></label>
                    <input type="number" name="total_points" class="form-control" min="1" value="{{ old('total_points', 100) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                    <input type="number" name="duration_minutes" class="form-control" min="1" value="{{ old('duration_minutes', 60) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Max Attempts <span class="text-danger">*</span></label>
                    <input type="number" name="max_attempts" class="form-control" min="1" value="{{ old('max_attempts', 1) }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date & Time</label>
                    <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date & Time</label>
                    <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Grading Type <span class="text-danger">*</span></label>
                <select name="grading_type" class="form-select" required>
                    <option value="automatic">Automatic (for MC/True-False)</option>
                    <option value="manual">Manual (Lecturer)</option>
                    <option value="both">Both</option>
                </select>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="show_correct_answers" value="1" id="showAnswers">
                <label class="form-check-label" for="showAnswers">
                    Show correct answers to students after grading
                </label>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="is_published" value="1" id="publishQuiz">
                <label class="form-check-label" for="publishQuiz">
                    <strong>Publish Quiz</strong> (Make available for students)
                </label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-navy flex-fill">
                    <i class="bi bi-check-circle"></i> Create Quiz
                </button>
                <a href="{{ route('lecturer.quizzes.index', $subject->id) }}" class="btn btn-outline-navy flex-fill text-center text-decoration-none">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>