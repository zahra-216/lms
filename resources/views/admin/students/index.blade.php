<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Students | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; color:#012147; margin:0; padding:40px 20px; }
    .container { max-width:1200px; margin:auto; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:26px 30px; margin-bottom:22px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2 { margin:0; font-weight:700; font-size:22px; }

    .add-btn {
        background:#fff; color:#012147; padding:10px 18px; border-radius:10px;
        text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:8px;
    }
    .add-btn:hover { background:#e2e8f0; color:#012147; }

    .search-box { max-width:420px; margin:0 auto 25px; }
    .search-box .form-control { border-radius:10px 0 0 10px; }
    .search-box .btn { border-radius:0 10px 10px 0; }

    .level-card { background:#012147; color:#fff; border-radius:12px; margin-bottom:18px; overflow:hidden; }
    .level-card .level-header { padding:16px 22px; cursor:pointer; display:flex; justify-content:space-between; align-items:center; }
    .level-card .level-body { background:#f4f6fb; color:#012147; padding:20px; display:none; }
    .level-card .level-body.show { display:block; }

    .faculty-block { background:#fff; border-radius:10px; margin-bottom:14px; box-shadow:0 2px 8px rgba(0,0,0,.06); }
    .faculty-header {
        padding:12px 18px; font-weight:600; cursor:pointer; display:flex;
        justify-content:space-between; align-items:center; background:#e9edf5; border-radius:10px 10px 0 0;
    }
    .faculty-body { display:none; padding:15px; }
    .faculty-body.show { display:block; }

    .course-block { margin-bottom:16px; }
    .course-title { font-weight:600; color:#012147; margin-bottom:8px; }

    table { width:100%; border-collapse:collapse; margin-bottom:10px; }
    th, td { padding:10px; border:1px solid #e2e8f0; text-align:center; font-size:13.5px; }
    th { background:#012147; color:#fff; font-weight:600; }
    tbody tr:nth-child(even) { background:#f8fafc; }

    .btn-sm-action { padding:6px 12px; border:none; border-radius:8px; cursor:pointer; text-decoration:none; margin:2px; display:inline-flex; align-items:center; gap:5px; font-size:13px; font-weight:600; }
    .edit-btn { background:#eef2f9; color:#012147; }
    .edit-btn:hover { background:#012147; color:#fff; }
    .delete-btn { background:#fee2e2; color:#991b1b; }
    .delete-btn:hover { background:#dc2626; color:#fff; }
    .change-sem-btn { background:#012147; color:#fff; border:none; }
    .change-sem-btn:hover { background:#1e3a6e; }

    .empty-note { text-align:center; color:#6b7280; padding:10px; }
    .chevron { transition:.2s; }
    .rotate { transform:rotate(180deg); }

    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header { flex-direction:column; align-items:flex-start; }
        table { display:block; overflow-x:auto; white-space:nowrap; }
    }
</style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h2><i class="bi bi-people"></i> Students</h2>
        <a href="{{ route('admin.students.create') }}" class="add-btn"><i class="bi bi-plus-circle"></i> Add Student</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 text-center">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.students.index') }}" class="search-box">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name, reg no, or email..." value="{{ $search }}">
            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
            @if($search)
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    @php $levelIcons = ['Diploma' => 'fa-certificate', 'HND' => 'fa-graduation-cap', 'Top-up' => 'fa-arrow-up', 'Degree' => 'fa-award']; @endphp

    @foreach($grouped as $levelName => $faculties)
        @php $levelId = \Illuminate\Support\Str::slug($levelName); @endphp
        <div class="level-card">
            <div class="level-header" onclick="toggleBlock('level-{{ $levelId }}', this)">
                <span><i class="fa {{ $levelIcons[$levelName] ?? 'fa-layer-group' }}"></i> {{ $levelName }}
                    <span class="badge bg-light text-dark ms-2">{{ $faculties->flatten(1)->flatten(1)->count() }} students</span>
                </span>
                <i class="fa fa-chevron-down chevron"></i>
            </div>
            <div class="level-body" id="level-{{ $levelId }}">
                @forelse($faculties as $facultyName => $courses)
                    @php $facultyId = $levelId.'-'.\Illuminate\Support\Str::slug($facultyName); @endphp
                    <div class="faculty-block">
                        <div class="faculty-header" onclick="toggleBlock('faculty-{{ $facultyId }}', this)">
                            <span><i class="fa fa-building"></i> {{ $facultyName }}</span>
                            <i class="fa fa-chevron-down chevron"></i>
                        </div>
                        <div class="faculty-body" id="faculty-{{ $facultyId }}">
                            @foreach($courses as $courseName => $students)
                                @php
                                    $courseSlug = $levelId.'-'.$facultyId.'-'.\Illuminate\Support\Str::slug($courseName);
                                    $firstStudent = $students->first();
                                    $courseSemesters = $allSemesters
                                        ->where('course_id', $firstStudent->course_id)
                                        ->where('level_id', $firstStudent->level_id);
                                @endphp
                                <div class="course-block">
                                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                        <div class="course-title"><i class="fa fa-book"></i> {{ $courseName }}</div>
                                        <button type="button" class="btn-sm-action change-sem-btn open-sem-modal" data-course="{{ $courseSlug }}">
                                            <i class="fa fa-calendar"></i> Change Semester
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                    <table class="course-table" data-course="{{ $courseSlug }}">
                                        <tr>
                                            <th><input type="checkbox" class="select-all" onclick="toggleAll(this)"></th>
                                            <th>Reg No</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Branch</th>
                                            <th>Semester</th>
                                            <th>Action</th>
                                        </tr>
                                        @foreach($students as $student)
                                            <tr>
                                                <td><input type="checkbox" class="student-check" value="{{ $student->id }}"></td>
                                                <td>{{ $student->registration_no }}</td>
                                                <td>{{ $student->name }}</td>
                                                <td>{{ $student->email }}</td>
                                                <td>{{ $student->branch }}</td>
                                                <td>{{ $student->semester->name ?? '—' }}</td>
                                                <td>
                                                    <a href="{{ route('admin.students.edit', $student->id) }}" class="btn-sm-action edit-btn">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.students.destroy', $student->id) }}"
                                                          method="POST" style="display:inline;"
                                                          onsubmit="return confirm('Delete this student?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-sm-action delete-btn">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                    </div>

                                    <!-- CHANGE SEMESTER MODAL -->
                                    <div class="modal fade" id="semModal-{{ $courseSlug }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.students.bulk.semester.update') }}" class="bulk-sem-form" data-course="{{ $courseSlug }}">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5>Change Semester — {{ $courseName }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-muted selected-count-{{ $courseSlug }}"></p>
                                                        <select name="semester_id" class="form-select" required>
                                                            <option value="">Select Semester</option>
                                                            @foreach($courseSemesters as $s)
                                                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="hidden-student-ids"></div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Update Semester</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="empty-note">No students found under {{ $levelName }}.</p>
                @endforelse
            </div>
        </div>
    @endforeach

</div>

<script>
function toggleBlock(id, headerEl) {
    const el = document.getElementById(id);
    el.classList.toggle('show');
    const chevron = headerEl.querySelector('.chevron');
    chevron.classList.toggle('rotate');
}

function toggleAll(checkbox) {
    const table = checkbox.closest('table');
    table.querySelectorAll('.student-check').forEach(cb => cb.checked = checkbox.checked);
}

document.querySelectorAll('.open-sem-modal').forEach(btn => {
    btn.addEventListener('click', function () {
        const courseSlug = this.getAttribute('data-course');
        const table = document.querySelector(`table[data-course="${courseSlug}"]`);
        const checked = Array.from(table.querySelectorAll('.student-check:checked')).map(cb => cb.value);

        if (checked.length === 0) {
            alert('Please select at least one student first.');
            return;
        }

        const modalEl = document.getElementById('semModal-' + courseSlug);
        const form = modalEl.querySelector('.bulk-sem-form');
        const hiddenContainer = form.querySelector('.hidden-student-ids');
        hiddenContainer.innerHTML = '';

        checked.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'student_ids[]';
            input.value = id;
            hiddenContainer.appendChild(input);
        });

        const countEl = modalEl.querySelector('.selected-count-' + courseSlug);
        if (countEl) countEl.innerText = checked.length + ' student(s) selected';

        new bootstrap.Modal(modalEl).show();
    });
});

@if($search)
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.level-body, .faculty-body').forEach(el => el.classList.add('show'));
    document.querySelectorAll('.chevron').forEach(el => el.classList.add('rotate'));
});
@endif
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>