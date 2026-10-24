@extends('layouts.admin')
@section('title')
Add Lecture Records
@endsection
@section('back', route('admin.lecture-records.index'))
@push('styles')
<style>
.row-2 { display:flex; gap:14px;
}
@media (max-width:576px) {
.row-2 { flex-direction:column;
}
}
.row-2 > div { flex:1;
}
.module-list {
        border:1px solid #e2e8f0; border-radius:10px; max-height:220px;
        overflow-y:auto; padding:10px 14px;
}
.module-item { display:flex; align-items:center; gap:10px; padding:6px 0; font-size:14px;
}
.module-item input { width:16px; height:16px;
}
.module-empty { color:#94a3b8; font-size:13px; padding:6px 0;
}
.lecturer-search-box { position:relative;
}
.lecturer-results {
        position:absolute; top:100%; left:0; right:0; background:#fff;
        border:1px solid #e2e8f0; border-radius:10px; margin-top:4px;
        max-height:200px; overflow-y:auto; box-shadow:0 8px 20px rgba(0,0,0,0.08);
        z-index:20; display:none;
}
.lecturer-results.show { display:block;
}
.lecturer-result-item { padding:10px 14px; cursor:pointer; font-size:14px;
}
.lecturer-result-item:hover { background:#eef2f9;
}
.lecturer-selected {
        margin-top:8px; padding:8px 14px; background:#eef2f9; border-radius:10px;
        display:flex; justify-content:space-between; align-items:center; font-size:14px; color:#012147;
}
.lecturer-selected button { border:none; background:none; color:#ef4444; font-weight:700; cursor:pointer;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-journal-plus"></i> Add Lecture Records</h3>
        <small>Select modules, then fill in shared details</small>
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
        <form action="{{ route('admin.lecture-records.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Faculty (select one or more)</label>
                <div id="facultyList" class="module-list"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Course</label>
                <div id="courseList" class="module-list">
                    <div class="module-empty">Select a faculty first.</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Level</label>
                <div id="levelList" class="module-list">
                    <div class="module-empty">Select a course first.</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Semester</label>
                <div id="semesterList" class="module-list">
                    <div class="module-empty">Select a level first.</div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Modules (select one or more)</label>
                <div id="moduleList" class="module-list">
                    <div class="module-empty">Select a semester first.</div>
                </div>
            </div>

            <hr class="mb-4">

            <div class="mb-3">
                <label class="form-label">Lecturer (optional)</label>
                <div class="lecturer-search-box">
                    <input type="text" id="lecturerSearch" class="form-control" placeholder="Search lecturer by name or username...">
                    <div id="lecturerResults" class="lecturer-results">
                        @foreach($lecturers as $lecturer)
                            <div class="lecturer-result-item"
                                 data-id="{{ $lecturer->id }}"
                                 data-name="{{ $lecturer->name }}"
                                 data-search="{{ strtolower($lecturer->name.' '.$lecturer->username) }}">
                                {{ $lecturer->name }} <span class="text-muted">({{ $lecturer->username }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div id="lecturerSelectedBox" style="display:none;" class="lecturer-selected">
                    <span id="lecturerSelectedName"></span>
                    <button type="button" onclick="clearLecturer()">&times;</button>
                </div>
                <input type="hidden" name="lecturer_id" id="lecturer_id">
            </div>

            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ old('date') }}" max="{{ date('Y-m-d') }}">
            </div>

            <div class="row-2 mb-3">
                <div>
                    <label class="form-label">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time') }}">
                </div>
                <div>
                    <label class="form-label">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time') }}">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Content Covered</label>
                <textarea name="content_covered" class="form-control" rows="4">{{ old('content_covered') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Remarks (optional)</label>
                <textarea name="remarks" class="form-control" rows="2">{{ old('remarks') }}</textarea>
            </div>

            <button class="btn btn-navy w-100">Save Record(s)</button>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
// ---------- Seed faculty checkboxes on page load ----------
document.getElementById('facultyList').innerHTML = `
    @foreach($faculties as $faculty)
        <label class="module-item">
            <input type="checkbox" class="lvl-faculty" value="{{ $faculty->id }}">
            {{ $faculty->name }}
        </label>
    @endforeach
`;

// ---------- Cascading multi-select logic ----------
function checkedValues(container, cls) {
    return [...container.querySelectorAll('.' + cls + ':checked')]
        .flatMap(el => el.value.split(','));
}

function renderCheckboxes(container, items, cls) {
    if (!items.length) {
        container.innerHTML = '<div class="module-empty">No options found.</div>';
        return;
    }
    container.innerHTML = items.map(i => `
        <label class="module-item">
            <input type="checkbox" class="${cls}" value="${i.ids ? i.ids.join(',') : i.id}">
            ${i.code ? i.code + ' - ' : ''}${i.name}
        </label>
    `).join('');
}

function resetBelow(containers, message) {
    containers.forEach(c => c.innerHTML = `<div class="module-empty">${message}</div>`);
}

const facultyList = document.getElementById('facultyList');
const courseList = document.getElementById('courseList');
const levelList = document.getElementById('levelList');
const semesterList = document.getElementById('semesterList');
const moduleList = document.getElementById('moduleList');

async function fetchIds(routeName, ids) {
    const params = new URLSearchParams();
    ids.forEach(id => params.append('ids[]', id));
    const res = await fetch(`/admin/lecture-records/${routeName}?${params.toString()}`);
    return res.json();
}

facultyList.addEventListener('change', async function () {
    const ids = checkedValues(facultyList, 'lvl-faculty');
    resetBelow([levelList, semesterList], 'Select a course first.');
    moduleList.innerHTML = '<div class="module-empty">Select a semester first.</div>';

    if (!ids.length) {
        resetBelow([courseList], 'Select a faculty first.');
        return;
    }
    const courses = await fetchIds('get-courses', ids);
    renderCheckboxes(courseList, courses, 'lvl-course');
});

courseList.addEventListener('change', async function () {
    const ids = checkedValues(courseList, 'lvl-course');
    resetBelow([semesterList], 'Select a level first.');
    moduleList.innerHTML = '<div class="module-empty">Select a semester first.</div>';

    if (!ids.length) {
        resetBelow([levelList], 'Select a course first.');
        return;
    }
    const levels = await fetchIds('get-levels', ids);
    renderCheckboxes(levelList, levels, 'lvl-level');
});

levelList.addEventListener('change', async function () {
    const ids = checkedValues(levelList, 'lvl-level');
    moduleList.innerHTML = '<div class="module-empty">Select a semester first.</div>';

    if (!ids.length) {
        resetBelow([semesterList], 'Select a level first.');
        return;
    }
    const semesters = await fetchIds('get-semesters', ids);
    renderCheckboxes(semesterList, semesters, 'lvl-semester');
});

semesterList.addEventListener('change', async function () {
    const ids = checkedValues(semesterList, 'lvl-semester');

    if (!ids.length) {
        moduleList.innerHTML = '<div class="module-empty">Select a semester first.</div>';
        return;
    }
    const modules = await fetchIds('get-subjects', ids);
    if (!modules.length) {
        moduleList.innerHTML = '<div class="module-empty">No modules found.</div>';
        return;
    }
    moduleList.innerHTML = modules.map(s => `
        <label class="module-item">
            <input type="checkbox" name="subject_ids[]" value="${s.id}">
            ${s.code} - ${s.name}
        </label>
    `).join('');
});

// ---------- Lecturer search ----------
const searchInput = document.getElementById('lecturerSearch');
const resultsBox = document.getElementById('lecturerResults');
const lecturerIdInput = document.getElementById('lecturer_id');
const selectedBox = document.getElementById('lecturerSelectedBox');
const selectedName = document.getElementById('lecturerSelectedName');

searchInput.addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();
    const items = resultsBox.querySelectorAll('.lecturer-result-item');
    let anyVisible = false;

    items.forEach(item => {
        const match = item.dataset.search.includes(query);
        item.style.display = match ? 'block' : 'none';
        if (match) anyVisible = true;
    });

    resultsBox.classList.toggle('show', query.length > 0 && anyVisible);
});

resultsBox.addEventListener('click', function (e) {
    const item = e.target.closest('.lecturer-result-item');
    if (!item) return;

    lecturerIdInput.value = item.dataset.id;
    selectedName.textContent = item.dataset.name;
    selectedBox.style.display = 'flex';
    searchInput.value = '';
    resultsBox.classList.remove('show');
});

function clearLecturer() {
    lecturerIdInput.value = '';
    selectedBox.style.display = 'none';
}

document.addEventListener('click', function (e) {
    if (!e.target.closest('.lecturer-search-box')) {
        resultsBox.classList.remove('show');
    }
});

document.getElementById('start_time').addEventListener('change', function () {
    document.getElementById('end_time').min = this.value;
});
</script>
@endpush
