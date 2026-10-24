@extends('layouts.lecturer')
@section('title')
Edit Quiz - {{ $quiz->title }}
@endsection
@section('back', route('lecturer.quizzes.index', $subject->id))
@push('styles')
<style>
.alert-soft-success { background:#e7f8ee; color:#0f5c33; border:none; border-radius:12px;
}
.section-card {
        background:#fff; border-radius:14px; padding:24px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:20px;
}
.section-card h6 { font-weight:700; color:#012147; font-size:15px; margin-bottom:18px; display:flex; align-items:center; gap:8px;
}
.form-check-input { width:18px; height:18px; cursor:pointer;
}
.form-check-label { cursor:pointer;
}
.alert-note { background:#e6f0ff; color:#0a3d91; border-radius:12px; padding:14px 16px; font-size:13.5px;
}
.question-item {
        background:#f8fafc; border-left:4px solid #012147; border-radius:10px;
        padding:16px; margin-bottom:14px;
}
.qtype-badge { font-size:11px; font-weight:700; padding:4px 9px; border-radius:8px; color:#fff;
}
.qtype-mc { background:#2563eb;
}
.qtype-tf { background:#0ea5b7;
}
.qtype-sa { background:#f59e0b;
}
.pts-badge { font-size:11px; font-weight:700; padding:4px 9px; border-radius:8px; background:#64748b; color:#fff;
}
.icon-btn {
        border:1px solid #e2e8f0; background:#fff; color:#012147; border-radius:8px;
        width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;
}
.icon-btn:hover { background:#012147; color:#fff;
}
.icon-btn-danger:hover { background:#b91c1c; color:#fff; border-color:#b91c1c;
}
.option-line { font-size:13.5px; color:#475569; margin-bottom:3px;
}
.option-correct { color:#0f5c33; font-weight:600;
}
.type-section {
        background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:16px; margin-top:10px;
}
.option-row { display:flex; align-items:center; gap:10px; margin-bottom:10px;
}
.option-row input[type="radio"] { width:18px; height:18px; flex-shrink:0; cursor:pointer;
}
.btn-add-option {
        border:1px dashed #94a3b8; background:#fff; color:#012147; border-radius:8px;
        padding:7px 14px; font-size:13px; font-weight:600;
}
.btn-add-option:hover { background:#012147; color:#fff;
}
.empty-hint { text-align:center; color:#94a3b8; padding:30px 0;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Quiz: {{ $quiz->title }}</h3>
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

    <div class="row">
        <div class="col-lg-4">
            <div class="section-card">
                <h6><i class="bi bi-info-circle"></i> Quiz Details</h6>
                <form action="{{ route('lecturer.quizzes.update', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ $quiz->title }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ $quiz->description }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control" min="1" value="{{ $quiz->duration_minutes }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total Points</label>
                        <input type="number" name="total_points" class="form-control" min="1" value="{{ $quiz->total_points }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Max Attempts</label>
                        <input type="number" name="max_attempts" class="form-control" min="1" value="{{ $quiz->max_attempts }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Grading Type</label>
                        <select name="grading_type" class="form-select" required>
                            <option value="automatic" {{ $quiz->grading_type === 'automatic' ? 'selected' : '' }}>Automatic</option>
                            <option value="manual" {{ $quiz->grading_type === 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="both" {{ $quiz->grading_type === 'both' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Start Date & Time</label>
                        <input type="datetime-local" name="start_date" class="form-control"
                               value="{{ $quiz->start_date ? $quiz->start_date->format('Y-m-d\TH:i') : '' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">End Date & Time</label>
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

                    <button type="submit" class="btn-navy">
                        <i class="bi bi-check-circle"></i> Save Changes
                    </button>
                </form>
            </div>

            <div class="alert-note">
                <i class="bi bi-info-circle"></i>
                <strong>Note:</strong> Once quiz starts, you cannot edit or delete it.
            </div>
        </div>

        <div class="col-lg-8">
            <div class="section-card">
                <h6><i class="bi bi-question-circle"></i> Questions ({{ $questions->count() }})</h6>
                @if($questions->count() > 0)
                    @foreach($questions as $question)
                    <div class="question-item">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex gap-1">
                                <span class="qtype-badge {{ $question->type === 'multiple_choice' ? 'qtype-mc' : ($question->type === 'true_false' ? 'qtype-tf' : 'qtype-sa') }}">
                                    {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                                </span>
                                <span class="pts-badge">{{ $question->points }} pts</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="icon-btn" data-bs-toggle="modal" data-bs-target="#editQuestion{{ $question->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('lecturer.quizzes.questions.destroy', ['subject' => $subject->id, 'quiz' => $quiz->id, 'question' => $question->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn icon-btn-danger" onclick="return confirm('Delete this question?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p class="mb-2"><strong>{{ $question->question_text }}</strong></p>
                        @if($question->type === 'multiple_choice')
                            @foreach($question->answers as $answer)
                            <div class="option-line {{ $answer->is_correct ? 'option-correct' : '' }}">
                                {{ $answer->is_correct ? '✓' : '○' }} {{ $answer->answer_text }}
                            </div>
                            @endforeach
                        @elseif($question->type === 'true_false')
                            <p class="option-line option-correct mb-0">Correct: {{ ucfirst($question->correct_answer) }}</p>
                        @else
                            <p class="option-line mb-0">Short or long written answer (the lecturer marks it)</p>
                        @endif
                    </div>
                    @endforeach
                @else
                    <div class="empty-hint">No questions added yet</div>
                @endif
            </div>

            <div class="section-card">
                <h6><i class="bi bi-plus-circle"></i> Add New Question</h6>
                <form action="{{ route('lecturer.quizzes.questions.store', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Question Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" id="questionType" required onchange="updateQuestionForm()">
                            <option value="">-- Select Type --</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short / Long Answer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Question Text <span class="text-danger">*</span></label>
                        <textarea name="question_text" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Points <span class="text-danger">*</span></label>
                        <input type="number" name="points" class="form-control" min="1" value="1" required>
                    </div>

                    <div id="mcSection" style="display:none;">
                        <div class="type-section">
                            <label class="form-label">Answer Options - select the radio for the correct one <span class="text-danger">*</span></label>
                            <div id="answersContainer">
                                <div class="option-row">
                                    <input type="radio" name="correct_answer_index" value="0">
                                    <input type="text" name="answers[]" class="form-control" placeholder="Option 1">
                                </div>
                                <div class="option-row">
                                    <input type="radio" name="correct_answer_index" value="1">
                                    <input type="text" name="answers[]" class="form-control" placeholder="Option 2">
                                </div>
                            </div>
                            <button type="button" class="btn-add-option" onclick="addAnswerOption()">
                                <i class="bi bi-plus"></i> Add Option
                            </button>
                        </div>
                    </div>

                    <div id="tfSection" style="display:none;">
                        <div class="type-section">
                            <label class="form-label">Correct Answer <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_answer" value="true" id="trueOption">
                                <label class="form-check-label" for="trueOption">True</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_answer" value="false" id="falseOption">
                                <label class="form-check-label" for="falseOption">False</label>
                            </div>
                        </div>
                    </div>

                    <div id="saSection" style="display:none;">
                        <div class="type-section">
                            <label class="form-label">Model Answer <span class="text-danger">*</span></label>
                            <textarea name="correct_answer" class="form-control" rows="2" placeholder="This will be used for reference when grading"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn-navy mt-3">
                        <i class="bi bi-check-circle"></i> Add Question
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@foreach($questions as $question)
<div class="modal fade" id="editQuestion{{ $question->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:14px; border:none;">
            <div class="modal-header" style="background:#012147; color:#fff; border-radius:14px 14px 0 0;">
                <h5 class="modal-title">Edit Question</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('lecturer.quizzes.questions.update', ['subject' => $subject->id, 'quiz' => $quiz->id, 'question' => $question->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="{{ $question->type }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <input type="text" class="form-control" value="{{ ucfirst(str_replace('_', ' ', $question->type)) }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question Text</label>
                        <textarea name="question_text" class="form-control" rows="2" required>{{ $question->question_text }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Points</label>
                        <input type="number" name="points" class="form-control" min="1" value="{{ $question->points }}" required>
                    </div>

                    @if($question->type === 'multiple_choice')
                        <div class="type-section">
                            <label class="form-label">Answer Options - select the radio for the correct one</label>
                            @foreach($question->answers as $index => $answer)
                            <div class="option-row">
                                <input type="radio" name="correct_answer_index" value="{{ $index }}" {{ $answer->is_correct ? 'checked' : '' }}>
                                <input type="text" name="answers[]" class="form-control" value="{{ $answer->answer_text }}">
                            </div>
                            @endforeach
                        </div>
                    @elseif($question->type === 'true_false')
                        <div class="type-section">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_answer" value="true" {{ $question->correct_answer === 'true' ? 'checked' : '' }}>
                                <label class="form-check-label">True</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_answer" value="false" {{ $question->correct_answer === 'false' ? 'checked' : '' }}>
                                <label class="form-check-label">False</label>
                            </div>
                        </div>
                    @else
                        <div class="type-section">
                            <label class="form-label">Model Answer</label>
                            <textarea name="correct_answer" class="form-control" rows="2">{{ $question->correct_answer }}</textarea>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-navy" style="width:auto; padding:10px 20px;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
@push('scripts')
<script>
function updateQuestionForm() {
    const type = document.getElementById('questionType').value;
    const mc = document.getElementById('mcSection');
    const tf = document.getElementById('tfSection');
    const sa = document.getElementById('saSection');

    mc.style.display = type === 'multiple_choice' ? 'block' : 'none';
    tf.style.display = type === 'true_false' ? 'block' : 'none';
    sa.style.display = type === 'short_answer' ? 'block' : 'none';

    mc.querySelectorAll('input').forEach(el => el.disabled = type !== 'multiple_choice');
    tf.querySelectorAll('input').forEach(el => el.disabled = type !== 'true_false');
    sa.querySelectorAll('textarea').forEach(el => el.disabled = type !== 'short_answer');
}

function addAnswerOption() {
    const container = document.getElementById('answersContainer');
    const index = container.children.length;
    const html = `
        <div class="option-row">
            <input type="radio" name="correct_answer_index" value="${index}">
            <input type="text" name="answers[]" class="form-control" placeholder="Option ${index + 1}">
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}
</script>
@endpush
