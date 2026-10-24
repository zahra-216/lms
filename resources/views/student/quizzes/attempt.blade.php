@extends('layouts.student')
@section('title')
{{ $quiz->title }} - Attempt
@endsection
@section('no_back', '1')

@push('styles')
<style>
    .quiz-side { position:sticky; top:calc(var(--topbar-h) + 18px); }
    .timer-box { background:linear-gradient(120deg, var(--navy-900), var(--brand-700)); color:#fff; border-radius:16px; padding:18px; text-align:center; margin:14px 0; }
    .timer-box h4 { margin:0; font-weight:800; font-size:34px; color:#fff; font-variant-numeric:tabular-nums; }
    .timer-box.low { background:linear-gradient(120deg, #991b1b, var(--danger)); animation:pulse 1s infinite; }
    @keyframes pulse { 50% { opacity:.85; } }
    .info-row { display:flex; justify-content:space-between; font-size:13.5px; padding:7px 0; border-bottom:1px solid var(--line); }
    .info-row span:first-child { color:var(--muted); }
    .q-num { width:34px; height:34px; border-radius:10px; background:var(--sky-soft); color:var(--brand-700); display:grid; place-items:center; font-weight:800; }
    .pts-badge { font-size:12px; font-weight:700; padding:4px 10px; border-radius:999px; background:var(--surface); color:var(--muted); border:1px solid var(--line); }
    .question-text { font-size:16px; font-weight:600; color:var(--navy-900); margin:14px 0; }
    .choice { display:flex; align-items:center; gap:12px; padding:12px 16px; border:1px solid var(--line); border-radius:12px; margin-bottom:8px; cursor:pointer; transition:all .12s; }
    .choice:hover { background:var(--surface); }
    .choice:has(input:checked) { border-color:var(--brand-600); background:var(--sky-soft); }
    .choice input { width:18px; height:18px; flex-shrink:0; }
</style>
@endpush

@section('content')
@php
    $mins = intdiv($remainingSeconds, 60);
    $secs = $remainingSeconds % 60;
@endphp
<div class="container">
    <div class="page-header">
        <div>
            <h2>{{ $quiz->title }}</h2>
            <small>Answer every question you can. Your answers are saved on this device as you type. The quiz is handed in automatically when the timer ends.</small>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card-box quiz-side">
                <h3 class="section-title"><i class="bi bi-puzzle"></i> Quiz details</h3>
                <div class="info-row"><span>Questions</span><span class="fw-bold">{{ $questions->count() }}</span></div>
                <div class="info-row"><span>Duration</span><span class="fw-bold">{{ $quiz->duration_minutes }} min</span></div>
                <div class="info-row"><span>Answered</span><span class="fw-bold"><span id="answeredCount">0</span> / {{ $questions->count() }}</span></div>
                <div class="timer-box" id="timerBox">
                    <h4 id="timer">{{ sprintf('%d:%02d', $mins, $secs) }}</h4>
                    <small>time remaining</small>
                </div>
                <button type="submit" form="quizForm" class="btn-navy w-100 justify-content-center"><i class="bi bi-send-check"></i> Submit quiz</button>
            </div>
        </div>

        <div class="col-lg-9">
            <form action="{{ route('student.quiz.submit', $submission->id) }}" method="POST" id="quizForm" data-key="quiz-draft-{{ $submission->id }}">
                @csrf

                @foreach ($questions as $index => $question)
                    <div class="card-box" data-question>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <span class="q-num">{{ $index + 1 }}</span>
                                <span class="text-muted small">Question {{ $index + 1 }} of {{ $questions->count() }}</span>
                            </div>
                            <span class="pts-badge">{{ $question->points }} {{ \Illuminate\Support\Str::plural('point', $question->points) }}</span>
                        </div>

                        <p class="question-text">{{ $question->question_text }}</p>

                        @if ($question->type === 'multiple_choice' || $question->type === 'true_false')
                            @forelse ($question->answers as $answer)
                                <label class="choice">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}">
                                    <span>{{ $answer->answer_text }}</span>
                                </label>
                            @empty
                                <div class="alert alert-warning mb-0">This question has no answer options. Please tell your lecturer.</div>
                            @endforelse
                        @else
                            <textarea name="answers[{{ $question->id }}]" class="form-control" rows="6" placeholder="Write your answer here. Long answers are fine."></textarea>
                            <div class="text-end small text-muted mt-1"><span data-count>0</span> characters</div>
                        @endif
                    </div>
                @endforeach

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn-navy"><i class="bi bi-send-check"></i> Submit quiz</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('quizForm');
    var key = form.dataset.key;
    var left = {{ (int) $remainingSeconds }};
    var submitting = false;

    // ---- restore a draft kept on this device (survives an accidental refresh) ----
    var draft = {};
    try { draft = JSON.parse(localStorage.getItem(key) || '{}'); } catch (e) { draft = {}; }
    Object.keys(draft).forEach(function (name) {
        var els = form.querySelectorAll('[name="' + name + '"]');
        els.forEach(function (el) {
            if (el.type === 'radio') el.checked = (el.value === draft[name]);
            else el.value = draft[name];
        });
    });

    function collect() {
        var data = {};
        form.querySelectorAll('textarea[name^="answers"]').forEach(function (t) { if (t.value.trim()) data[t.name] = t.value; });
        form.querySelectorAll('input[type="radio"]:checked').forEach(function (r) { data[r.name] = r.value; });
        return data;
    }
    function refresh() {
        var data = collect();
        document.getElementById('answeredCount').textContent = Object.keys(data).length;
        form.querySelectorAll('[data-count]').forEach(function (c) { c.textContent = c.closest('[data-question]').querySelector('textarea').value.length; });
        try { localStorage.setItem(key, JSON.stringify(data)); } catch (e) { /* storage unavailable */ }
    }
    form.addEventListener('input', refresh);
    form.addEventListener('change', refresh);
    refresh();

    // ---- timer ----
    function tick() {
        var m = Math.floor(left / 60), s = left % 60;
        document.getElementById('timer').textContent = m + ':' + (s < 10 ? '0' : '') + s;
        document.getElementById('timerBox').classList.toggle('low', left <= 60);
        if (left <= 0) { submitting = true; try { localStorage.removeItem(key); } catch (e) {} form.submit(); return; }
        left--;
    }
    setInterval(tick, 1000);
    tick();

    // ---- confirm before handing in, and say how many are still blank ----
    form.addEventListener('submit', function (e) {
        if (submitting) return;
        var total = form.querySelectorAll('[data-question]').length;
        var blank = total - Object.keys(collect()).length;
        var msg = blank > 0
            ? 'You have ' + blank + ' unanswered question' + (blank > 1 ? 's' : '') + '. Submit anyway? You cannot change your answers afterwards.'
            : 'Submit your quiz now? You cannot change your answers afterwards.';
        if (!confirm(msg)) { e.preventDefault(); return; }
        submitting = true;
        try { localStorage.removeItem(key); } catch (err) {}
    });

    window.addEventListener('beforeunload', function (e) { if (!submitting) { e.preventDefault(); e.returnValue = ''; } });
})();
</script>
@endpush
