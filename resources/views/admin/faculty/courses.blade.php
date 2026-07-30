<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $faculty->name }} - Courses | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:1100px; margin:auto; }

    .back-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover { background:#012147; color:#fff; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:26px 30px; margin-bottom:22px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h2 { margin:0 0 4px; font-weight:700; font-size:22px; }
    .page-header small { opacity:0.85; }

    .section-heading { font-size:18px; font-weight:700; color:#012147; margin-bottom:16px; display:flex; align-items:center; gap:8px; }

    .add-btn {
        background:#012147; color:#fff; padding:9px 18px; border-radius:10px;
        text-decoration:none; font-weight:600; font-size:14px; display:inline-flex; align-items:center; gap:8px;
    }
    .add-btn:hover { background:#1e3a6e; color:#fff; }

    .course-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px; margin-bottom:32px; }
    .course-card { border-radius:16px; overflow:hidden; background:#fff; height:180px; position:relative; color:#fff; box-shadow:0 8px 24px rgba(0,0,0,.07); cursor:pointer; transition:transform .25s; }
    .course-card:hover { transform:translateY(-5px); }
    .course-card::before { content:''; position:absolute; inset:0; background:linear-gradient(180deg, rgba(1,33,71,0) 40%, rgba(1,33,71,0.9) 100%); }
    .course-card .card-body { position:absolute; bottom:0; left:0; right:0; padding:14px 16px; z-index:2; }
    .course-card h6 { font-size:14px; font-weight:700; margin:0 0 6px; }
    .course-card .badge { border-radius:20px; }
    .course-card-actions { position:absolute; top:10px; right:10px; z-index:3; display:flex; gap:6px; }
    .course-card-actions a, .course-card-actions button { width:30px; height:30px; border:none; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:12px; }
    .icon-edit { background:#eef2f9; color:#012147; }
    .icon-edit:hover { background:#012147; color:#fff; }
    .icon-delete { background:#fee2e2; color:#991b1b; }
    .icon-delete:hover { background:#dc2626; color:#fff; }

    .card-box { background:#fff; border-radius:16px; padding:20px; box-shadow:0 8px 26px rgba(0,0,0,0.06); margin-bottom:18px; }
    .structure-header { cursor:pointer; }
    .structure-header h5 { font-size:16px; color:#012147; font-weight:700; margin-bottom:2px; }
    .structure-header small { color:#64748b; }

    .level-block h6 { font-size:14px; font-weight:700; color:#012147; margin:14px 0 10px; display:flex; align-items:center; gap:8px; }
    .level-block h6::before { content:''; width:6px; height:6px; border-radius:50%; background:#3b82f6; }

    .semester-btn { border-radius:10px !important; border:1px solid #e2e8f0 !important; color:#012147 !important; background:#f8fafc !important; font-weight:600; padding:10px 14px !important; }
    .semester-btn:hover { background:#eef2f9 !important; }

    .subject-list .list-group-item { border:none; border-bottom:1px solid #f1f5f9; padding:11px 14px; }
    .subject-list .list-group-item:hover { background:#f8fafc; }
    .subject-list a { color:#012147; font-weight:500; text-decoration:none; }

    .quick-add-row { display:flex; gap:8px; margin-top:8px; flex-wrap:wrap; }
    .quick-add-row select, .quick-add-row input { border-radius:8px; border:1px solid #e2e8f0; padding:8px 10px; font-size:13px; flex:1; min-width:120px; }
    .quick-add-row button { border:none; background:#012147; color:#fff; border-radius:8px; padding:8px 14px; font-size:13px; font-weight:600; }
    .quick-add-row button:hover { background:#1e3a6e; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        .course-grid { grid-template-columns:1fr 1fr; }
        .structure-header { flex-direction:column; align-items:flex-start !important; gap:8px; }
        .card-box { padding:16px; }
    }
    @media (max-width:400px){
        .course-grid { grid-template-columns:1fr; }
    }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.faculties.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h2>{{ $faculty->name }}</h2>
        <small>Manage courses, levels, semesters and subjects for this faculty.</small>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-heading mb-0"><i class="bi bi-collection"></i> Courses</div>
        <a href="{{ route('admin.courses.create') }}" class="add-btn"><i class="bi bi-plus-lg"></i> Add Course</a>
    </div>

    @if($faculty->courses->count())
    <div class="course-grid">
        @foreach($faculty->courses as $course)
        @php
            $imgPath = $course->image && file_exists(public_path('storage/courses/'.$course->image))
                ? 'storage/courses/'.$course->image : 'https://picsum.photos/300x220?random='.$course->id;
        @endphp
        <div class="course-card" style="background-image:url('{{ asset($imgPath) }}'); background-size:cover; background-position:center;"
             onclick="openCourseStructure('course-{{ $course->id }}')">
            <div class="course-card-actions">
                <a href="{{ route('admin.courses.edit', $course->id) }}" class="icon-edit" onclick="event.stopPropagation()"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Delete this course?')" onclick="event.stopPropagation()">
                    @csrf @method('DELETE')
                    <button type="submit" class="icon-delete"><i class="bi bi-trash"></i></button>
                </form>
            </div>
            <div class="card-body">
                <h6>{{ $course->code }} - {{ $course->name }}</h6>
            </div>
        </div>
        @endforeach
    </div>

    <div class="section-heading"><i class="bi bi-diagram-3"></i> Faculty Structure</div>

    @foreach($faculty->courses as $course)
        @php $courseCollapseId = 'course-'.$course->id; @endphp
        <div class="card-box">
            <div class="d-flex justify-content-between align-items-center structure-header"
                 data-bs-toggle="collapse" data-bs-target="#{{ $courseCollapseId }}" aria-expanded="false">
                <div>
                    <h5>{{ $course->code }} - {{ $course->name }}</h5>
                    <small>{{ $course->description ?? 'No description available.' }}</small>
                </div>
                <i class="bi bi-chevron-down"></i>
            </div>

            <div class="collapse mt-3" id="{{ $courseCollapseId }}">

                <form method="POST" action="{{ route('admin.levels.store') }}" class="quick-add-row">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <select name="name" required>
                        <option value="">Add Level...</option>
                        <option value="Diploma">Diploma</option>
                        <option value="HND">HND</option>
                        <option value="Top-up">Top-up</option>
                        <option value="Degree">Degree</option>
                    </select>
                    <button type="submit"><i class="bi bi-plus-lg"></i> Add Level</button>
                </form>

                @foreach($course->levels as $level)
                    <div class="level-block mb-3">
                        <h6>
                            {{ $level->name }}
                            <form action="{{ route('admin.levels.destroy', $level->id) }}" method="POST" onsubmit="return confirm('Delete this level?')" style="display:inline; margin-left:auto;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="color:#ef4444; border:none; background:none; padding:0 4px;"><i class="bi bi-trash"></i></button>
                            </form>
                        </h6>

                        <form method="POST" action="{{ route('admin.semesters.store') }}" class="quick-add-row ps-3">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                            <input type="hidden" name="level_id" value="{{ $level->id }}">
                            <select name="name" required>
                                <option value="">Add Semester...</option>
                                <option value="Semester 1">Semester 1</option>
                                <option value="Semester 2">Semester 2</option>
                                <option value="Semester 3">Semester 3</option>
                                <option value="Semester 4">Semester 4</option>
                                <option value="Semester 5">Semester 5</option>
                                <option value="Semester 6">Semester 6</option>
                            </select>
                            <button type="submit"><i class="bi bi-plus-lg"></i> Add Semester</button>
                        </form>

                        @foreach($level->semesters as $semester)
                            @php $collapseId = 'semester-'.$course->id.'-'.$level->id.'-'.$semester->id; @endphp
                            <div class="mb-2 ps-3">
                                <button class="btn semester-btn w-100 text-start d-flex justify-content-between align-items-center"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false">
                                    <span>{{ $semester->name }}</span>
                                    <i class="bi bi-chevron-down"></i>
                                </button>

                                <div class="collapse mt-2" id="{{ $collapseId }}">
                                    <form method="POST" action="{{ route('admin.subjects.store') }}" class="quick-add-row ps-3">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                                        <input type="hidden" name="level_id" value="{{ $level->id }}">
                                        <input type="hidden" name="semester_id" value="{{ $semester->id }}">
                                        <input type="text" name="code" placeholder="Code" required>
                                        <input type="text" name="name" placeholder="Subject name" required>
                                        <button type="submit"><i class="bi bi-plus-lg"></i> Add Subject</button>
                                    </form>

                                    @if($semester->subjects->count())
                                        <ul class="list-group subject-list mb-2 ps-3">
                                            @foreach($semester->subjects as $subject)
                                                <li class="list-group-item py-2 d-flex justify-content-between align-items-center">
                                                    <a href="{{ route('admin.subjects.show', $subject->id) }}">
                                                        {{ $subject->code }} - {{ $subject->name }}
                                                    </a>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('admin.subjects.edit', $subject->id) }}" style="color:#012147;"><i class="bi bi-pencil"></i></a>
                                                        <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('Delete this subject?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" style="border:none;background:none;color:#ef4444;"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted ps-3">No subjects yet.</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

            </div>
        </div>
    @endforeach
    @else
        <p class="text-muted">No courses yet for this faculty.</p>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openCourseStructure(collapseId){
    const target = document.getElementById(collapseId);
    if(!target) return;
    const bsCollapse = bootstrap.Collapse.getOrCreateInstance(target, { toggle:false });
    bsCollapse.show();
    setTimeout(() => target.closest('.card-box').scrollIntoView({ behavior:'smooth', block:'start' }), 150);
}
</script>
</body>
</html>