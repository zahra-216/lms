<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:1200px; margin:auto; }

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
    .page-header h2 { margin:0; font-weight:700; font-size:22px; }

    .level-card { background:#012147; color:#fff; border-radius:12px; margin-bottom:18px; overflow:hidden; }
    .level-card .level-header { padding:16px 22px; cursor:pointer; display:flex; justify-content:space-between; align-items:center; }
    .level-card .level-body { background:#f4f6fb; color:#012147; padding:20px; display:none; }
    .level-card .level-body.show { display:block; }

    .faculty-block { background:#fff; border-radius:10px; margin-bottom:14px; box-shadow:0 2px 8px rgba(0,0,0,.06); }
    .faculty-header { padding:12px 18px; font-weight:600; cursor:pointer; display:flex; justify-content:space-between; align-items:center; background:#e9edf5; border-radius:10px 10px 0 0; }
    .faculty-body { display:none; padding:15px; }
    .faculty-body.show { display:block; }

    .course-block { margin-bottom:16px; }
    .course-title { font-weight:600; color:#012147; margin-bottom:8px; cursor:pointer; }
    .course-body { display:none; padding-left:15px; }
    .course-body.show { display:block; }

    .semester-title { font-weight:600; color:#012147; margin:10px 0 6px; cursor:pointer; }
    .semester-body { display:none; padding-left:15px; }
    .semester-body.show { display:block; }

    .subject-link {
        display:flex; justify-content:space-between; align-items:center;
        background:#f1f5f9; padding:10px 14px; border-radius:8px; margin-bottom:8px;
        text-decoration:none; color:#012147;
    }
    .subject-link:hover { background:#012147; color:#fff; }

    .empty-note { text-align:center; color:#6b7280; padding:10px; }
    .chevron { transition:.2s; }
    .rotate { transform:rotate(180deg); }

    @media (max-width:576px){ body { padding:20px 12px; } }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.dashboard') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h2><i class="bi bi-calendar-check"></i> Student Attendance</h2>
    </div>

    @forelse($grouped as $facultyName => $courses)
        @php $facultyId = \Illuminate\Support\Str::slug($facultyName); @endphp
        <div class="level-card">
            <div class="level-header" onclick="toggleBlock('faculty-{{ $facultyId }}', this)">
                <span><i class="fa fa-building"></i> {{ $facultyName }}</span>
                <i class="fa fa-chevron-down chevron"></i>
            </div>
            <div class="level-body" id="faculty-{{ $facultyId }}">
                @foreach($courses as $courseName => $levels)
                    @php $courseId = $facultyId.'-'.\Illuminate\Support\Str::slug($courseName); @endphp
                    <div class="faculty-block">
                        <div class="faculty-header" onclick="toggleBlock('course-{{ $courseId }}', this)">
                            <span><i class="fa fa-book"></i> {{ $courseName }}</span>
                            <i class="fa fa-chevron-down chevron"></i>
                        </div>
                        <div class="faculty-body" id="course-{{ $courseId }}">
                            @foreach($levels as $levelName => $semesters)
                                @php $levelId = $courseId.'-'.\Illuminate\Support\Str::slug($levelName); @endphp
                                <div class="course-block">
                                    <div class="course-title" onclick="toggleBlock('level-{{ $levelId }}', this)">
                                        <i class="fa fa-layer-group"></i> {{ $levelName }}
                                    </div>
                                    <div class="course-body" id="level-{{ $levelId }}">
                                        @foreach($semesters as $semesterName => $subjects)
                                            @php $semId = $levelId.'-'.\Illuminate\Support\Str::slug($semesterName); @endphp
                                            <div class="semester-title" onclick="toggleBlock('sem-{{ $semId }}', this)">
                                                <i class="fa fa-calendar3"></i> {{ $semesterName }}
                                            </div>
                                            <div class="semester-body" id="sem-{{ $semId }}">
                                                @foreach($subjects as $subject)
                                                    <a href="{{ route('admin.attendance.show', $subject->id) }}" class="subject-link">
                                                        <span>{{ $subject->code }} - {{ $subject->name }}</span>
                                                        <i class="fa fa-arrow-right"></i>
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
        <p class="empty-note">No subjects found.</p>
    @endforelse
</div>

<script>
function toggleBlock(id, headerEl) {
    const el = document.getElementById(id);
    el.classList.toggle('show');
    const chevron = headerEl.querySelector('.chevron');
    if (chevron) chevron.classList.toggle('rotate');
}
</script>
</body>
</html>