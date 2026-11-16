@extends('layouts.admin')
@section('title')
Timetable
@endsection
@section('no_back', '1')
@push('styles')
<style>
.tt-step { font-weight:800; color:var(--navy-900); margin:0 0 8px; }
.tt-step span { display:inline-grid; place-items:center; width:26px; height:26px; border-radius:50%; background:var(--navy-900); color:#fff; font-size:13px; margin-right:8px; }
.tt-block { padding:18px 0; border-top:1px solid var(--line); }
.tt-block:first-of-type { border-top:0; padding-top:0; }
.pick-results { margin-top:6px; border:1px solid #e2e8f0; border-radius:12px; background:#fff; max-height:300px; overflow:auto; }
.pick-results[hidden] { display:none; }
.pick-result { display:block; width:100%; text-align:left; background:#fff; border:0; border-top:1px solid #f1f5f9; padding:10px 14px; color:#012147; }
.pick-result:first-child { border-top:0; }
.pick-result:hover, .pick-result:focus { background:#eef2f9; outline:0; }
.pick-result small, .pick-chip small { display:block; color:#64748b; font-size:12.5px; }
.pick-note { padding:10px 14px; color:#64748b; font-size:13.5px; }
.pick-chip { display:flex; align-items:center; gap:10px; background:#e6f0ff; color:#0a3d91; border-radius:12px; padding:9px 12px; margin-top:10px; }
.pick-chip .txt { flex:1; min-width:0; overflow-wrap:anywhere; }
.pick-chip button { flex:none; border:0; background:#fff; color:#b91c1c; width:30px; height:30px; border-radius:50%; font-size:18px; line-height:1; }
.opt-btns { display:flex; flex-wrap:wrap; gap:8px; }
.opt-btn { border:1px solid var(--line); background:#fff; color:var(--navy-900); border-radius:12px; padding:9px 18px; font-weight:700; }
.opt-btn:hover { background:var(--sky-soft); }
.opt-btn.on { background:var(--navy-900); border-color:var(--navy-900); color:#fff; }
.tt-rowbox { display:grid; grid-template-columns:130px 112px 112px minmax(0,1.5fr) minmax(0,1.2fr) minmax(0,1fr) 40px; gap:10px; align-items:end; padding:12px; margin-bottom:10px; border:1px solid var(--line); border-radius:14px; background:var(--surface); }
.tt-rowbox label { font-size:12px; font-weight:700; color:var(--muted); margin-bottom:3px; display:block; }
.tt-rowbox .tt-del { height:38px; border:0; border-radius:10px; background:#fff; color:#b91c1c; font-size:18px; border:1px solid var(--line); }
.tt-rowbox .tt-del:hover { background:#b91c1c; color:#fff; }
@media (max-width: 1499.98px) { .tt-rowbox { grid-template-columns:repeat(3, minmax(0,1fr)); } .tt-rowbox .tt-del { grid-column:1 / -1; justify-self:end; padding:0 22px; } }
@media (max-width: 575.98px) { .tt-rowbox { grid-template-columns:minmax(0,1fr) minmax(0,1fr); } .tt-rowbox > div:nth-child(1), .tt-rowbox > div:nth-child(4), .tt-rowbox > div:nth-child(5), .tt-rowbox > div:nth-child(6) { grid-column:1 / -1; } }
.tt-list td, .tt-list th { white-space:normal; }
.tt-reader { border:1px dashed #94a3b8; border-radius:14px; padding:14px; margin-bottom:14px; background:#f8fafc; }
.tt-rowbox .needs-check { border-color:#dc2626 !important; background:#fef2f2; }
.tt-rowbox .read-hint { grid-column:1 / -1; font-size:12.5px; color:#b91c1c; margin:-4px 0 0; }
</style>
@endpush
@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-calendar-week"></i> Timetable</h2>
            <small>Choose a course, level and semester, then add the weekly classes. Students and lecturers see it on their pages.</small>
        </div>
    </div>

    <div class="card-box">
        <form method="POST" action="{{ route('admin.timetables.store') }}" id="ttForm">
            @csrf
            <input type="hidden" name="course_id" id="courseId">
            <input type="hidden" name="level_id" id="levelId">
            <input type="hidden" name="semester_id" id="semesterId">

            <div class="tt-block">
                <div class="tt-step"><span>1</span> Course</div>
                <input type="text" id="courseSearch" class="form-control" placeholder="Search the course by name..." autocomplete="off">
                <div class="pick-results" id="courseResults" hidden></div>
                <div id="courseChip"></div>
            </div>

            <div class="tt-block" id="levelBlock" hidden>
                <div class="tt-step"><span>2</span> Level</div>
                <div class="opt-btns" id="levelBtns"></div>
            </div>

            <div class="tt-block" id="semBlock" hidden>
                <div class="tt-step"><span>3</span> Semester</div>
                <div class="opt-btns" id="semBtns"></div>
            </div>

            <div class="tt-block" id="rowsBlock" hidden>
                <div class="tt-step"><span>4</span> Classes</div>
                <div class="alert alert-info rounded-3 py-2" id="existingNote" hidden>A timetable already exists for this semester. Saving will replace it with what you see below.</div>
                <div class="alert alert-warning rounded-3 py-2" id="noModules" hidden>This semester has no modules yet. Add them first on the Faculties page, then come back.</div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="batch">Batch <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" name="batch" id="batch" maxlength="60" class="form-control" placeholder="e.g. Batch 25">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold" for="modeNote">Line under the title <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" name="mode_note" id="modeNote" maxlength="150" class="form-control" placeholder="e.g. Online via Zoom - 1-hour sessions">
                    </div>
                </div>

                <div class="tt-reader" id="readerBox">
                    <div class="fw-bold mb-1"><i class="bi bi-image"></i> Fill from a timetable picture <span class="text-muted fw-normal">(optional)</span></div>
                    <div class="small text-muted mb-2">Choose a picture of the timetable and the classes below are filled in for you. You can change anything before saving. The picture is sent to Claude (Anthropic) to be read and is not stored.</div>
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <input type="file" id="ttImage" accept="image/jpeg,image/png,image/webp" class="d-none">
                        <button type="button" class="btn btn-outline-primary" id="ttImageBtn"><i class="bi bi-upload"></i> Choose picture</button>
                        <span class="small" id="readerStatus" role="status"></span>
                    </div>
                </div>

                <div id="rows"></div>
                <div class="d-flex gap-2 flex-wrap mt-2">
                    <button type="button" class="btn btn-outline-primary" id="addRow"><i class="bi bi-plus-lg"></i> Add class</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i> Save timetable</button>
                </div>
                <div class="text-danger small mt-2" id="formError" hidden></div>
            </div>
        </form>
    </div>

    <div class="card-box">
        <h3 class="section-title"><i class="bi bi-list-check text-primary"></i> Saved timetables</h3>
        @if ($saved->isEmpty())
            <div class="text-muted">No timetable has been saved yet.</div>
        @else
            <div class="table-responsive">
                <table class="table tt-list align-middle mb-0">
                    <thead><tr><th>Course</th><th>Level</th><th>Semester</th><th>Batch</th><th>Classes</th><th>Updated</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($saved as $t)
                            <tr>
                                <td>{{ $t->course->name ?? '-' }}</td>
                                <td>{{ $t->level->name ?? '-' }}</td>
                                <td>{{ $t->semester->name ?? '-' }}</td>
                                <td>{{ $t->batch ?: '-' }}</td>
                                <td>{{ $t->rows->count() }}</td>
                                <td>{{ $t->updated_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end flex-wrap">
                                        <a href="{{ route('admin.timetables.index', ['edit' => $t->id]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>
                                        <form method="POST" action="{{ route('admin.timetables.destroy', $t->id) }}" data-confirm="Delete this timetable? Students and lecturers will no longer see it.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
@push('scripts')
<script>
(function () {
    var TREE = @json($tree);
    var LECTURERS = @json($lecturers);
    var EXISTING = @json($existing);
    var PRESET = @json($preset);
    var OLD = @json(['rows' => old('rows'), 'batch' => old('batch'), 'mode_note' => old('mode_note')]);
    var DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    var MAX_SHOWN = 12;

    var $ = function (id) { return document.getElementById(id); };
    var form = $('ttForm'), search = $('courseSearch'), results = $('courseResults'), chip = $('courseChip');
    var levelBlock = $('levelBlock'), levelBtns = $('levelBtns'), semBlock = $('semBlock'), semBtns = $('semBtns');
    var rowsBlock = $('rowsBlock'), rowsBox = $('rows'), existingNote = $('existingNote'), noModules = $('noModules'), formError = $('formError');
    var course = null, level = null, semester = null;

    TREE.forEach(function (c) { c.hay = (c.name + ' ' + c.faculty).toLowerCase(); });

    function el(tag, cls, text) {
        var e = document.createElement(tag);
        if (cls) e.className = cls;
        if (text !== undefined) e.textContent = text;
        return e;
    }

    // ---- 1. course ----
    function showResults() {
        var words = search.value.trim().toLowerCase().split(/\s+/).filter(Boolean);
        results.innerHTML = '';
        if (!words.length) { results.hidden = true; return; }
        var list = TREE.filter(function (c) { return words.every(function (w) { return c.hay.indexOf(w) !== -1; }); });
        results.hidden = false;
        if (!list.length) { results.appendChild(el('div', 'pick-note', 'No course found. Only courses the campus offers are listed.')); return; }
        list.slice(0, MAX_SHOWN).forEach(function (c) {
            var b = el('button', 'pick-result');
            b.type = 'button';
            b.appendChild(el('strong', '', c.name));
            if (c.faculty) b.appendChild(el('small', '', c.faculty));
            b.addEventListener('click', function () { pickCourse(c); });
            results.appendChild(b);
        });
    }

    function pickCourse(c, keep) {
        course = c; level = null; semester = null;
        $('courseId').value = c.id; $('levelId').value = ''; $('semesterId').value = '';
        search.value = ''; results.hidden = true;
        chip.innerHTML = '';
        var box = el('div', 'pick-chip');
        var txt = el('div', 'txt'); txt.appendChild(el('strong', '', c.name));
        if (c.faculty) txt.appendChild(el('small', '', c.faculty));
        var x = el('button', '', '×'); x.type = 'button'; x.setAttribute('aria-label', 'Choose another course');
        x.addEventListener('click', clearCourse);
        box.appendChild(txt); box.appendChild(x); chip.appendChild(box);
        search.hidden = true;
        drawLevels();
        if (!keep) { semBlock.hidden = true; rowsBlock.hidden = true; }
    }

    function clearCourse() {
        course = level = semester = null;
        $('courseId').value = $('levelId').value = $('semesterId').value = '';
        chip.innerHTML = ''; search.hidden = false; search.focus();
        levelBlock.hidden = semBlock.hidden = rowsBlock.hidden = true;
    }

    // ---- 2. level ----
    function drawLevels() {
        levelBtns.innerHTML = '';
        levelBlock.hidden = false;
        if (!course.levels.length) { levelBtns.appendChild(el('div', 'text-muted', 'This course has no levels yet. Add them on the Faculties page.')); return; }
        course.levels.forEach(function (l) {
            var b = el('button', 'opt-btn' + (level && level.id === l.id ? ' on' : ''), l.name);
            b.type = 'button';
            b.addEventListener('click', function () { pickLevel(l); });
            levelBtns.appendChild(b);
        });
    }

    function pickLevel(l, keep) {
        level = l; semester = null;
        $('levelId').value = l.id; $('semesterId').value = '';
        drawLevels(); drawSemesters();
        if (!keep) rowsBlock.hidden = true;
    }

    // ---- 3. semester ----
    function drawSemesters() {
        semBtns.innerHTML = '';
        semBlock.hidden = false;
        if (!level.semesters.length) { semBtns.appendChild(el('div', 'text-muted', 'This level has no semesters yet. Add them on the Faculties page.')); return; }
        level.semesters.forEach(function (s) {
            var key = course.id + '-' + level.id + '-' + s.id;
            var b = el('button', 'opt-btn' + (semester && semester.id === s.id ? ' on' : ''), s.name + (EXISTING[key] ? ' ✓' : ''));
            b.type = 'button';
            b.title = EXISTING[key] ? 'A timetable is already saved' : '';
            b.addEventListener('click', function () { pickSemester(s); });
            semBtns.appendChild(b);
        });
    }

    function pickSemester(s, useOld) {
        semester = s;
        $('semesterId').value = s.id;
        drawSemesters();
        rowsBlock.hidden = false;
        rowsBox.innerHTML = '';
        formError.hidden = true;
        noModules.hidden = s.modules.length > 0;
        $('addRow').disabled = s.modules.length === 0;

        var key = course.id + '-' + level.id + '-' + s.id;
        var saved = EXISTING[key];
        existingNote.hidden = !saved;
        var rows, batch, mode;
        if (useOld && OLD.rows) { rows = Object.keys(OLD.rows).map(function (k) { return OLD.rows[k]; }); batch = OLD.batch; mode = OLD.mode_note; }
        else if (saved) { rows = saved.rows; batch = saved.batch; mode = saved.mode_note; }
        else { rows = []; batch = ''; mode = ''; }
        $('batch').value = batch || '';
        $('modeNote').value = mode || '';
        if (!rows.length && s.modules.length) rows = [{}];
        rows.forEach(addRow);
    }

    // ---- 4. rows ----
    function field(labelText, control) {
        var d = el('div');
        d.appendChild(el('label', '', labelText));
        d.appendChild(control);
        return d;
    }

    function select(name, options, value, placeholder) {
        var s = el('select', 'form-select');
        s.dataset.name = name; s.required = true;
        if (placeholder) { var o = el('option', '', placeholder); o.value = ''; s.appendChild(o); }
        options.forEach(function (op) {
            var o = el('option', '', op.label); o.value = op.value;
            if (String(op.value) === String(value)) o.selected = true;
            s.appendChild(o);
        });
        return s;
    }

    function input(name, type, value, extra) {
        var i = el('input', 'form-control');
        i.type = type; i.dataset.name = name; i.value = value || '';
        Object.keys(extra || {}).forEach(function (k) { i[k] = extra[k]; });
        return i;
    }

    function addRow(r) {
        r = r || {};
        var box = el('div', 'tt-rowbox');
        box.appendChild(field('Day', select('day', DAYS.map(function (d) { return { value: d, label: d }; }), r.day || 'Monday')));
        box.appendChild(field('Start', input('start_time', 'time', r.start_time || '09:00', { required: true })));
        box.appendChild(field('End', input('end_time', 'time', r.end_time || '10:00', { required: true })));
        box.appendChild(field('Module', select('subject_id', semester.modules.map(function (m) { return { value: m.id, label: m.code + ' - ' + m.name }; }), r.subject_id, 'Choose a module')));
        box.appendChild(field('Lecturer', select('lecturer_id', LECTURERS.map(function (l) { return { value: l.id, label: l.name }; }), r.lecturer_id, 'Choose a lecturer')));
        box.appendChild(field('Note (optional)', input('note', 'text', r.note, { maxLength: 150, placeholder: 'e.g. Combined class with HND BM' })));
        var del = el('button', 'tt-del', '×'); del.type = 'button'; del.setAttribute('aria-label', 'Remove this class'); del.title = 'Remove this class';
        del.addEventListener('click', function () { box.remove(); renumber(); });
        box.appendChild(del);
        rowsBox.appendChild(box);
        renumber();
    }

    function renumber() {
        Array.prototype.forEach.call(rowsBox.children, function (box, i) {
            Array.prototype.forEach.call(box.querySelectorAll('[data-name]'), function (f) { f.name = 'rows[' + i + '][' + f.dataset.name + ']'; });
        });
    }

    $('addRow').addEventListener('click', function () { addRow({}); });

    // ---- fill from a picture ----
    var imgInput = $('ttImage'), imgBtn = $('ttImageBtn'), status = $('readerStatus');
    var READ_URL = @json(route('admin.timetables.read'));
    var TOKEN = document.querySelector('#ttForm input[name=_token]').value;
    var FIELD_NAMES = { subject_id: 'module', lecturer_id: 'lecturer', start_time: 'time', end_time: 'time' };

    function say(text, bad) { status.textContent = text; status.className = 'small ' + (bad ? 'text-danger fw-semibold' : 'text-muted'); }

    imgBtn.addEventListener('click', function () { imgInput.click(); });

    imgInput.addEventListener('change', function () {
        var file = imgInput.files[0];
        if (!file) return;
        if (!course || !level || !semester) { say('Choose the course, level and semester first.', true); imgInput.value = ''; return; }
        if (rowsBox.children.length && !confirm('The picture will replace the classes shown below. Continue?')) { imgInput.value = ''; return; }

        var data = new FormData();
        data.append('_token', TOKEN);
        data.append('course_id', course.id); data.append('level_id', level.id); data.append('semester_id', semester.id);
        data.append('image', file);

        imgBtn.disabled = true;
        say('Reading the picture... this can take up to a minute.');

        fetch(READ_URL, { method: 'POST', body: data, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function (r) { return r.json().catch(function () { return {}; }).then(function (j) { return { ok: r.ok, status: r.status, json: j }; }); })
            .then(function (res) {
                if (!res.ok) {
                    var m = res.json.message;
                    if (res.json.errors) m = Object.keys(res.json.errors).map(function (k) { return res.json.errors[k][0]; })[0];
                    if (res.status === 419) m = 'Your session ended. Reload the page and try again.';
                    if (res.status === 429) m = 'Too many tries. Wait a minute and try again.';
                    say(m || 'The picture could not be read. Try again, or fill the classes by hand.', true);
                    return;
                }
                fillFromPicture(res.json);
            })
            .catch(function () { say('The picture could not be sent. Check your connection and try again.', true); })
            .then(function () { imgBtn.disabled = false; imgInput.value = ''; });
    });

    function fillFromPicture(j) {
        rowsBox.innerHTML = '';
        if (j.batch) $('batch').value = j.batch;
        if (j.mode_note) $('modeNote').value = j.mode_note;
        j.rows.forEach(function (r) { addRow(r); markChecks(rowsBox.lastChild, r); });
        renumber();
        if (j.unmatched > 0) {
            say(j.rows.length + ' classes filled in. ' + j.unmatched + ' field(s) could not be matched and are marked in red - please choose them. Check every row before saving.', true);
        } else {
            say(j.rows.length + ' classes filled in. Please check every row, then press Save.');
        }
        rowsBox.scrollIntoView({ block: 'start', behavior: 'smooth' });
    }

    function markChecks(box, r) {
        var bad = r.check || [];
        if (!bad.length) return;
        var what = [];
        bad.forEach(function (n) {
            var f = box.querySelector('[data-name="' + n + '"]');
            if (!f) return;
            f.classList.add('needs-check');
            f.addEventListener('change', function () { f.classList.remove('needs-check'); });
            if (what.indexOf(FIELD_NAMES[n]) === -1) what.push(FIELD_NAMES[n]);
        });
        var read = [];
        if (bad.indexOf('subject_id') !== -1 && r.read_module) read.push('module "' + r.read_module + '"');
        if (bad.indexOf('lecturer_id') !== -1 && r.read_lecturer) read.push('lecturer "' + r.read_lecturer + '"');
        var hint = el('div', 'read-hint', 'Please check the ' + what.join(', ') + (read.length ? ' - the picture says ' + read.join(' and ') : '') + '.');
        box.appendChild(hint);
    }

    search.addEventListener('input', showResults);
    search.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var first = results.querySelector('.pick-result');
            if (first) first.click();
        } else if (e.key === 'Escape') { search.value = ''; results.hidden = true; }
    });

    form.addEventListener('submit', function (e) {
        renumber();
        if (!course || !level || !semester) {
            e.preventDefault();
            formError.textContent = 'Choose the course, level and semester first.';
            formError.hidden = false;
            return;
        }
        if (!rowsBox.children.length) {
            e.preventDefault();
            formError.textContent = 'Add at least one class.';
            formError.hidden = false;
        }
    });

    // open a saved timetable (Edit) or restore the form after a failed save
    if (PRESET) {
        var c = TREE.filter(function (x) { return x.id === PRESET.course_id; })[0];
        var l = c && c.levels.filter(function (x) { return x.id === PRESET.level_id; })[0];
        var s = l && l.semesters.filter(function (x) { return x.id === PRESET.semester_id; })[0];
        if (c && l && s) {
            pickCourse(c, true); pickLevel(l, true); pickSemester(s, !!OLD.rows);
            form.scrollIntoView({ block: 'start' });
        }
    }
})();
</script>
@endpush
