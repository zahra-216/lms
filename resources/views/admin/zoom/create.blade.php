@extends('layouts.admin')
@section('title')
New Zoom Class
@endsection
@section('back', route('admin.zoom.index'))
@push('styles')
<style>
.pick-results { margin-top:6px; border:1px solid #e2e8f0; border-radius:12px; background:#fff; max-height:300px; overflow:auto; }
.pick-results[hidden] { display:none; }
.pick-result { display:block; width:100%; text-align:left; background:#fff; border:0; border-top:1px solid #f1f5f9; padding:10px 14px; color:#012147; }
.pick-result:first-child { border-top:0; }
.pick-result:hover, .pick-result:focus { background:#eef2f9; outline:0; }
.pick-result small, .pick-chip small { display:block; color:#64748b; font-size:12.5px; }
.pick-note { padding:10px 14px; color:#64748b; font-size:13.5px; }
.pick-chips { display:flex; flex-direction:column; gap:8px; margin-top:12px; }
.pick-chip { display:flex; align-items:center; gap:10px; background:#e6f0ff; color:#0a3d91; border-radius:12px; padding:9px 12px; }
.pick-chip .txt { flex:1; min-width:0; overflow-wrap:anywhere; }
.pick-chip button { flex:none; border:0; background:#fff; color:#b91c1c; width:30px; height:30px; border-radius:50%; font-size:18px; line-height:1; }
.pick-chip button:hover { background:#b91c1c; color:#fff; }
.pick-empty { color:#64748b; font-size:13.5px; padding:10px 0 0; }
.pick-error { color:#b91c1c; font-size:13.5px; margin-top:8px; }
</style>
@endpush
@section('content')
@php
    $moduleData = $subjects->map(fn ($s) => [
        'id' => $s->id,
        'code' => $s->code,
        'name' => $s->name,
        'where' => collect([$s->course->name ?? null, $s->level->name ?? null, $s->semester->name ?? null])->filter()->implode(' / '),
    ])->values();
    $preselected = collect(old('subject_ids', []))->map(fn ($v) => (int) $v)->values();
@endphp
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-camera-video"></i> New Zoom Class</h2>
            <small>The meeting is created in the campus Zoom account</small>
        </div>
    </div>

    @if(! $connected && ! $fake)
        <div class="alert alert-warning rounded-3">Zoom is not connected yet, so a class cannot be created right now.</div>
    @endif

    <div class="card-box">
        <form method="POST" action="{{ route('admin.zoom.store') }}" id="zoomForm">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Class title</label>
                    <input type="text" name="topic" value="{{ old('topic') }}" maxlength="200" class="form-control @error('topic') is-invalid @enderror" placeholder="e.g. Human Resource Management - Lecture 5" required>
                    @error('topic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="date" name="date" value="{{ old('date') }}" min="{{ now()->toDateString() }}" class="form-control @error('date') is-invalid @enderror" required>
                    @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Start time</label>
                    <input type="time" name="time" value="{{ old('time') }}" class="form-control @error('time') is-invalid @enderror" required>
                    @error('time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Length (minutes)</label>
                    <input type="number" name="duration" value="{{ old('duration', 120) }}" min="10" max="480" class="form-control @error('duration') is-invalid @enderror" required>
                    @error('duration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" for="moduleSearch">Modules this class covers</label>
                    <div class="text-muted small mb-2">Type a module code or name, or the course (for example "HRM" or "employee"), then click the module to add it. Add every module that joins this class. Attendance is saved under each added module, for the students of that module's course, level and current semester.</div>
                    <input type="text" id="moduleSearch" class="form-control" placeholder="Search by module code, name or course..." autocomplete="off">
                    <div class="pick-results" id="moduleResults" hidden></div>

                    <div class="pick-chips" id="moduleChips"></div>
                    <div class="pick-empty" id="moduleEmpty">No module added yet.</div>
                    <div class="pick-error" id="moduleError" @if(! $errors->has('subject_ids') && ! $errors->has('subject_ids.*')) hidden @endif>{{ $errors->first('subject_ids') ?: $errors->first('subject_ids.*') ?: 'Add at least one module for this class.' }}</div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary" @disabled(! $connected && ! $fake)><i class="bi bi-camera-video"></i> Create Zoom Class</button>
                <a href="{{ route('admin.zoom.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function () {
    var MODULES = @json($moduleData);
    var chosen = @json($preselected);
    var MAX_SHOWN = 12;

    var form = document.getElementById('zoomForm');
    var search = document.getElementById('moduleSearch');
    var results = document.getElementById('moduleResults');
    var chips = document.getElementById('moduleChips');
    var empty = document.getElementById('moduleEmpty');
    var error = document.getElementById('moduleError');
    var byId = {};
    MODULES.forEach(function (m) { byId[m.id] = m; m.hay = (m.code + ' ' + m.name + ' ' + m.where).toLowerCase(); });

    function el(tag, cls, text) {
        var e = document.createElement(tag);
        if (cls) e.className = cls;
        if (text !== undefined) e.textContent = text;
        return e;
    }

    function two(m, tag, cls) {
        // "CODE - Name" with the course line underneath; built with textContent so module names can never inject HTML
        var box = el(tag, cls);
        var line = el('span');
        line.appendChild(el('strong', '', m.code));
        line.appendChild(document.createTextNode(' - ' + m.name));
        box.appendChild(line);
        if (m.where) box.appendChild(el('small', '', m.where));
        return box;
    }

    function drawChips() {
        chips.innerHTML = '';
        chosen.forEach(function (id) {
            var m = byId[id];
            if (!m) return;
            var chip = el('div', 'pick-chip');
            var txt = two(m, 'div', 'txt');
            var x = el('button', '', '×');
            x.type = 'button';
            x.setAttribute('aria-label', 'Remove ' + m.code);
            x.addEventListener('click', function () {
                chosen = chosen.filter(function (v) { return v !== id; });
                drawChips();
                showResults();
            });
            var hidden = el('input');
            hidden.type = 'hidden';
            hidden.name = 'subject_ids[]';
            hidden.value = id;
            chip.appendChild(txt);
            chip.appendChild(x);
            chip.appendChild(hidden);
            chips.appendChild(chip);
        });
        empty.hidden = chosen.length > 0;
        if (chosen.length) error.hidden = true;
    }

    function add(id) {
        if (chosen.indexOf(id) === -1) chosen.push(id);
        search.value = '';
        results.hidden = true;
        drawChips();
        search.focus();
    }

    function matches() {
        var words = search.value.trim().toLowerCase().split(/\s+/).filter(Boolean);
        if (!words.length) return null;
        return MODULES.filter(function (m) {
            return chosen.indexOf(m.id) === -1 && words.every(function (w) { return m.hay.indexOf(w) !== -1; });
        });
    }

    function showResults() {
        var list = matches();
        results.innerHTML = '';
        if (list === null) { results.hidden = true; return; }
        results.hidden = false;
        if (!list.length) { results.appendChild(el('div', 'pick-note', 'No module found. Try a different word.')); return; }
        list.slice(0, MAX_SHOWN).forEach(function (m) {
            var b = two(m, 'button', 'pick-result');
            b.type = 'button';
            b.addEventListener('click', function () { add(m.id); });
            results.appendChild(b);
        });
        if (list.length > MAX_SHOWN) {
            var more = list.length - MAX_SHOWN;
            results.appendChild(el('div', 'pick-note', more + (more === 1 ? ' more module matches' : ' more modules match') + '. Type more letters to narrow the list.'));
        }
    }

    search.addEventListener('input', showResults);
    search.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {            // Enter picks the first match instead of sending the form
            e.preventDefault();
            var list = matches();
            if (list && list.length) add(list[0].id);
        } else if (e.key === 'Escape') {
            search.value = '';
            results.hidden = true;
        }
    });

    form.addEventListener('submit', function (e) {
        if (!chosen.length) {
            e.preventDefault();
            error.textContent = 'Add at least one module for this class.';
            error.hidden = false;
            search.focus();
        }
    });

    drawChips();
})();
</script>
@endpush
