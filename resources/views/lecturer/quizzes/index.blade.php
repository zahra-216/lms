@extends('layouts.lecturer')
@section('title')
Quizzes - {{ $subject->name }}
@endsection
@push('styles')
<style>
.alert-soft-success { background:#e7f8ee; color:#0f5c33; border:none; border-radius:12px;
}
.alert-soft-danger { background:#fdeaea; color:#8a1f1f; border:none; border-radius:12px;
}
.quiz-card {
        background:#fff; border-radius:14px; padding:22px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06);
        height:100%; display:flex; flex-direction:column;
}
.quiz-title { font-weight:700; color:#012147; font-size:17px; margin-bottom:6px;
}
.quiz-desc { color:#64748b; font-size:13.5px; margin-bottom:14px;
}
.badge-status { font-size:11.5px; font-weight:600; padding:6px 12px; border-radius:20px;
}
.badge-published { background:#e7f8ee; color:#0f5c33;
}
.badge-draft { background:#fff4e0; color:#8a5b00;
}
.badge-editable { background:#e6f0ff; color:#0a3d91;
}
.badge-locked { background:#fdeaea; color:#8a1f1f;
}
.quiz-meta { font-size:13px; color:#475569; margin-bottom:14px;
}
.quiz-meta div { margin-bottom:4px; display:flex; align-items:center; gap:6px;
}
.quiz-actions { margin-top:auto; display:flex; gap:8px;
}
.quiz-actions form { flex:1; margin:0;
}
.btn-soft {
        border-radius:10px; font-weight:600; font-size:13px; padding:8px 10px;
        border:1px solid #e2e8f0; background:#fff; color:#012147; width:100%;
        display:flex; align-items:center; justify-content:center; gap:5px;
}
.btn-soft:hover { background:#012147; color:#fff;
}
.btn-soft-danger { border-color:#f4c7c7; color:#b91c1c;
}
.btn-soft-danger:hover { background:#b91c1c; color:#fff;
}
</style>
@endpush
@section('content')
<div class="container">
    <a href="{{ route('lecturer.subject.assignments', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <div>
            <h3><i class="bi bi-puzzle"></i> Quizzes</h3>
            <small>{{ $subject->name }}</small>
        </div>
        @if(Auth::guard('lecturer')->check())
        <a href="{{ route('lecturer.quizzes.create', $subject->id) }}" class="btn-navy">
            <i class="bi bi-plus-circle"></i> Create Quiz
        </a>
        @endif
    </div>

    

    

    @if($quizzes->count() > 0)
        <div class="row g-3">
            @foreach($quizzes as $quiz)
            <div class="col-md-6 col-lg-4">
                <div class="quiz-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="quiz-title">{{ $quiz->title }}</div>
                        <span class="badge-status {{ $quiz->is_published ? 'badge-published' : 'badge-draft' }}">
                            {{ $quiz->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                    <div class="quiz-desc">{{ Str::limit($quiz->description, 90) }}</div>

                    <div class="quiz-meta">
                        <div><i class="bi bi-question-circle"></i> {{ $quiz->questions->count() }} Questions</div>
                        <div><i class="bi bi-hourglass-split"></i> {{ $quiz->duration_minutes }} mins</div>
                        <div><i class="bi bi-award"></i> {{ $quiz->total_points }} points</div>
                        @if($quiz->start_date)
                            <div><i class="bi bi-calendar-event"></i> {{ $quiz->start_date->format('M d, Y H:i') }}</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        @if($quiz->canBeEdited())
                            <span class="badge-status badge-editable">Can be edited</span>
                        @else
                            <span class="badge-status badge-locked">Quiz started</span>
                        @endif
                    </div>

                    <div class="quiz-actions">
                        <a href="{{ route('lecturer.quizzes.show', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" class="btn-soft">
                            <i class="bi bi-eye"></i> View
                        </a>
                        @if($quiz->canBeEdited())
                        <a href="{{ route('lecturer.quizzes.edit', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" class="btn-soft">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('lecturer.quizzes.destroy', ['subject' => $subject->id, 'quiz' => $quiz->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-soft btn-soft-danger" onclick="return confirm('Are you sure?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="empty-box">
            <i class="bi bi-inbox"></i>
            <p class="mt-3 mb-0">No quizzes yet. <a href="{{ route('lecturer.quizzes.create', $subject->id) }}" style="color:#012147; font-weight:600;">Create one now</a></p>
        </div>
    @endif
</div>
@endsection
