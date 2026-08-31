@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <a href="{{ route('admin.quizzes.index', $subject->id) }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header mb-4">
        <h3><i class="bi bi-pencil-square"></i> Edit Quiz: {{ $quiz->title }}</h3>
        <p class="text-muted">{{ $subject->name }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Quiz Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.quizzes.update', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $quiz->title }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $quiz->description }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" class="form-control" min="1" value="{{ $quiz->duration_minutes }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Total Points</label>
                            <input type="number" name="total_points" class="form-control" min="1" value="{{ $quiz->total_points }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Max Attempts</label>
                            <input type="number" name="max_attempts" class="form-control" min="1" value="{{ $quiz->max_attempts }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Grading Type</label>
                            <select name="grading_type" class="form-select" required>
                                <option value="automatic" {{ $quiz->grading_type === 'automatic' ? 'selected' : '' }}>Automatic</option>
                                <option value="manual" {{ $quiz->grading_type === 'manual' ? 'selected' : '' }}>Manual</option>
                                <option value="both" {{ $quiz->grading_type === 'both' ? 'selected' : '' }}>Both</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Start Date & Time</label>
                            <input type="datetime-local" name="start_date" class="form-control" 
                                   value="{{ $quiz->start_date ? $quiz->start_date->format('Y-m-d\TH:i') : '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">End Date & Time</label>
                            <input type="datetime-local" name="end_date" class="form-control"
                                   value="{{ $quiz->end_date ? $quiz->end_date->format('Y-m-d\TH:i') : '' }}">
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="show_correct_answers" value="1" 
                                   id="showAnswers" {{ $quiz->show_correct_answers ? 'checked' : '' }}>
                            <label class="form-check-label" for="showAnswers">Show correct answers</label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_published" value="1" 
                                   id="publishQuiz" {{ $quiz->is_published ? 'checked' : '' }}>
                            <label class="form-check-label" for="publishQuiz">Publish</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-question-circle"></i> Questions ({{ $questions->count() }})</h6>
                </div>
                <div class="card-body">
                    @if($questions->count() > 0)
                        @foreach($questions as $question)
                        <div class="question-item card mb-3 border-start border-4 border-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</span>
                                        <span class="badge bg-secondary">{{ $question->points }} pts</span>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editQuestion{{ $question->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.quizzes.questions.destroy', ['subject' => $subject->id, 'quiz' => $quiz->id, 'question' => $question->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <p class="mb-0 mt-2"><strong>{{ $question->question_text }}</strong></p>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center py-4">No questions yet</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-plus-circle"></i> Add Question</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.quizzes.questions.store', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" id="questionType" required onchange="updateForm()">
                                <option value="">-- Select --</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="short_answer">Short Answer</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Question <span class="text-danger">*</span></label>
                            <textarea name="question_text" class="form-control" rows="2" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Points</label>
                            <input type="number" name="points" class="form-control" min="1" value="1" required>
                        </div>

                        <div id="mcSection" style="display:none;" class="alert alert-light border">
                            <div id="answersContainer">
                                <div class="input-group mb-2">
                                    <input type="text" name="answers[]" class="form-control" placeholder="Option 1">
                                    <select name="correct_answer_index" class="form-select" style="max-width:100px">
                                        <option value="0">Correct</option>
                                    </select>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addOption()">+ Add</button>
                        </div>

                        <div id="tfSection" style="display:none;" class="alert alert-light border">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_answer" value="true">
                                <label>True</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_answer" value="false">
                                <label>False</label>
                            </div>
                        </div>

                        <div id="saSection" style="display:none;" class="alert alert-light border">
                            <textarea name="correct_answer" class="form-control" rows="2" placeholder="Model answer..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Add Question</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
    }
</style>

<script>
function updateForm() {
    const type = document.getElementById('questionType').value;
    document.getElementById('mcSection').style.display = type === 'multiple_choice' ? 'block' : 'none';
    document.getElementById('tfSection').style.display = type === 'true_false' ? 'block' : 'none';
    document.getElementById('saSection').style.display = type === 'short_answer' ? 'block' : 'none';
}

function addOption() {
    const container = document.getElementById('answersContainer');
    const index = container.children.length;
    container.insertAdjacentHTML('beforeend', `
        <div class="input-group mb-2">
            <input type="text" name="answers[]" class="form-control" placeholder="Option ${index + 1}">
            <select name="correct_answer_index" class="form-select" style="max-width:100px">
                <option value="${index}">Correct</option>
            </select>
        </div>
    `);
}
</script>
@endsection
