@extends('layouts.admin')
@section('title')
New Zoom Class
@endsection
@section('back', route('admin.zoom.index'))
@push('styles')
<style>
.module-box { max-height:380px; overflow:auto; border:1px solid #e2e8f0; border-radius:12px; background:#fff; }
.module-group { background:#f1f5f9; color:#012147; font-weight:700; font-size:13px; padding:8px 14px; position:sticky; top:0; z-index:1; }
.module-row { display:flex; gap:10px; align-items:flex-start; padding:9px 14px; border-top:1px solid #f1f5f9; cursor:pointer; margin:0; }
.module-row:hover { background:#f8fafc; }
.module-row input { width:18px; height:18px; margin-top:2px; flex:none; }
.module-row small { color:#64748b; display:block; }
</style>
@endpush
@section('content')
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
        <form method="POST" action="{{ route('admin.zoom.store') }}">
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
                    <label class="form-label fw-semibold">Modules this class covers</label>
                    <div class="text-muted small mb-2">Tick every module that joins this class. Attendance is saved under each ticked module, for the students of that module's course, level and current semester.</div>
                    <input type="text" id="moduleSearch" class="form-control mb-2" placeholder="Search by module code, name or course...">
                    <div class="module-box @error('subject_ids') border-danger @enderror" id="moduleBox">
                        @php $group = null; $selected = collect(old('subject_ids', []))->map(fn ($v) => (int) $v); @endphp
                        @foreach($subjects as $s)
                            @php $label = ($s->course->name ?? '-').' - '.($s->level->name ?? '-').' - '.($s->semester->name ?? '-'); @endphp
                            @if($label !== $group)
                                @php $group = $label; @endphp
                                <div class="module-group" data-group>{{ $s->course->faculty->name ?? '' }} / {{ $label }}</div>
                            @endif
                            <label class="module-row" data-text="{{ strtolower($s->code.' '.$s->name.' '.$label) }}">
                                <input type="checkbox" name="subject_ids[]" value="{{ $s->id }}" @checked($selected->contains($s->id))>
                                <span><strong>{{ $s->code }}</strong> - {{ $s->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="small mt-2"><span id="pickedCount">0</span> module(s) ticked</div>
                    @error('subject_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
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
    var search = document.getElementById('moduleSearch');
    var box = document.getElementById('moduleBox');
    var rows = box.querySelectorAll('.module-row');
    var counter = document.getElementById('pickedCount');

    function count() { counter.textContent = box.querySelectorAll('input:checked').length; }

    search.addEventListener('input', function () {
        var q = search.value.trim().toLowerCase();
        rows.forEach(function (r) { r.style.display = (!q || r.dataset.text.indexOf(q) !== -1) ? '' : 'none'; });
        // hide group headings that have no visible row after them
        box.querySelectorAll('[data-group]').forEach(function (g) {
            var el = g.nextElementSibling, any = false;
            while (el && !el.hasAttribute('data-group')) { if (el.style.display !== 'none') any = true; el = el.nextElementSibling; }
            g.style.display = any ? '' : 'none';
        });
    });

    box.addEventListener('change', count);
    count();
})();
</script>
@endpush
