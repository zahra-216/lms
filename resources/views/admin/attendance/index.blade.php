<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:1000px; margin:auto; }

    .back-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover { background:#012147; color:#fff; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:26px 30px; margin-bottom:26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; align-items:center; gap:14px;
    }
    .page-header h2 { margin:0; font-weight:700; font-size:22px; }
    .page-header i.header-icon { font-size:26px; opacity:.9; }

    /* Faculty cards */
    .faculty-card { background:#fff; border-radius:14px; margin-bottom:14px; box-shadow:0 4px 16px rgba(0,0,0,.05); overflow:hidden; }
    .faculty-header {
        padding:18px 22px; cursor:pointer; display:flex; justify-content:space-between; align-items:center;
        font-weight:700; font-size:15px; color:#012147; transition:.15s;
    }
    .faculty-header:hover { background:#f8fafc; }
    .faculty-header .label { display:flex; align-items:center; gap:12px; }
    .faculty-header .icon-circle {
        width:38px; height:38px; border-radius:10px; background:#fee2e2; color:#ef4444;
        display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;
    }
    .faculty-header .chevron { color:#94a3b8; transition:.2s; }
    .faculty-header .chevron.rotate { transform:rotate(180deg); }
    .faculty-body { display:none; padding:0 22px 20px; }
    .faculty-body.show { display:block; }

    /* Course */
    .course-block { background:#f8fafc; border-radius:12px; padding:6px 16px; margin-bottom:10px; }
    .course-title {
        font-weight:600; font-size:14px; color:#012147; padding:12px 0; cursor:pointer;
        display:flex; align-items:center; justify-content:space-between;
    }
    .course-title .label { display:flex; align-items:center; gap:8px; }
    .course-body { display:none; padding-bottom:14px; }
    .course-body.show { display:block; }

    /* Level */
    .level-block { border-left:2px solid #e2e8f0; padding-left:14px; margin-bottom:10px; }
    .level-title {
        font-weight:600; font-size:13px; color:#1e3a6e; margin-bottom:6px; cursor:pointer;
        display:flex; align-items:center; justify-content:space-between;
    }
    .level-title .label { display:flex; align-items:center; gap:8px; }
    .level-body { display:none; }
    .level-body.show { display:block; }

    /* Semester */
    .semester-title {
        font-weight:600; font-size:12px; color:#64748b; text-transform:uppercase; letter-spacing:.03em;
        margin:10px 0 8px; cursor:pointer; display:flex; align-items:center; justify-content:space-between;
    }
    .semester-body { display:none; padding-left:4px; }
    .semester-body.show { display:block; }

    /* Subject link */
    .subject-link {
        display:flex; justify-content:space-between; align-items:center;
        background:#fff; border:1px solid #e2e8f0; padding:11px 14px; border-radius:10px; margin-bottom:8px;
        text-decoration:none; color:#012147; transition:.15s;
    }
    .subject-link:hover { border-color:#ef4444; box-shadow:0 4px 12px rgba(239,68,68,0.12); color:#012147; }
    .subject-link .subj-label { display:flex; align-items:center; gap:10px; font-size:13.5px; font-weight:500; }
    .subject-link .subj-icon {
        width:28px; height:28px; border-radius:8px; background:#fee2e2; color:#ef4444;
        display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0;
    }
    .subject-link .arrow { color:#94a3b8; font-size:13px; }

    .chevron-sm { font-size:12px; color:#94a3b8; transition:.2s; }
    .chevron-sm.rotate { transform:rotate(180deg); }

    .empty-note { text-align:center; color:#6b7280; padding:30px; background:#fff; border-radius:14px; }

    @media (max-width:576px){ body { padding:20px 12px; } }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.dashboard') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <i class="bi bi-calendar-check header-icon"></i>
        <h2>Student Attendance</h2>
    </div>

    @forelse($grouped as $facultyName => $courses)
        @php $facultyId = \Illuminate\Support\Str::slug($facultyName); @endphp
        <div class="faculty-card">
            <div class="faculty-header" onclick="toggleBlock('faculty-{{ $facultyId }}', this)">
                <span class="label">
                    <span class="icon-circle"><i class="bi bi-building"></i></span>
                    {{ $facultyName }}
                </span>
                <i class="bi bi-chevron-down chevron"></i>
            </div>
            <div class="faculty-body" id="faculty-{{ $facultyId }}">
                @foreach($courses as $courseName => $levels)
                    @php $courseId = $facultyId.'-'.\Illuminate\Support\Str::slug($courseName); @endphp
                    <div class="course-block">
                        <div class="course-title" onclick="toggleBlock('course-{{ $courseId }}', this)">
                            <span class="label"><i class="bi bi-book"></i> {{ $courseName }}</span>
                            <i class="bi bi-chevron-down chevron-sm"></i>
                        </div>
                        <div class="course-body" id="course-{{ $courseId }}">
                            @foreach($levels as $levelName => $semesters)
                                @php $levelId = $courseId.'-'.\Illuminate\Support\Str::slug($levelName); @endphp
                                <div class="level-block">
                                    <div class="level-title" onclick="toggleBlock('level-{{ $levelId }}', this)">
                                        <span class="label"><i class="bi bi-layers"></i> {{ $levelName }}</span>
                                        <i class="bi bi-chevron-down chevron-sm"></i>
                                    </div>
                                    <div class="level-body" id="level-{{ $levelId }}">
                                        @foreach($semesters as $semesterName => $subjects)
                                            @php $semId = $levelId.'-'.\Illuminate\Support\Str::slug($semesterName); @endphp
                                            <div class="semester-title" onclick="toggleBlock('sem-{{ $semId }}', this)">
                                                <span>{{ $semesterName }}</span>
                                                <i class="bi bi-chevron-down chevron-sm"></i>
                                            </div>
                                            <div class="semester-body" id="sem-{{ $semId }}">
                                                @foreach($subjects as $subject)
                                                    <a href="{{ route('admin.attendance.show', $subject->id) }}" class="subject-link">
                                                        <span class="subj-label">
                                                            <span class="subj-icon"><i class="bi bi-journal-text"></i></span>
                                                            {{ $subject->code }} - {{ $subject->name }}
                                                        </span>
                                                        <i class="bi bi-arrow-right arrow"></i>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="empty-note">No subjects found.</div>
    @endforelse
</div>

<script>
function toggleBlock(id, headerEl) {
    const el = document.getElementById(id);
    el.classList.toggle('show');
    const chevron = headerEl.querySelector('.chevron, .chevron-sm');
    if (chevron) chevron.classList.toggle('rotate');
}
</script>
</body>
</html>