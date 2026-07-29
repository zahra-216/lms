<!DOCTYPE html>
<html>
<head>
    <title>Lecture Records | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI'; background: #f4f6f9; color:#012147; padding:40px; }
        .container { max-width:1200px; margin:auto; }
        h1 { text-align:center; margin-bottom:20px; }

        .level-card { background:#012147; color:#fff; border-radius:12px; margin-bottom:18px; overflow:hidden; }
        .level-card .level-header { padding:16px 22px; cursor:pointer; display:flex; justify-content:space-between; align-items:center; }
        .level-card .level-body { background:#f4f6f9; color:#012147; padding:20px; display:none; }
        .level-card .level-body.show { display:block; }

        .faculty-block { background:#fff; border-radius:10px; margin-bottom:14px; box-shadow:0 2px 8px rgba(0,0,0,.06); }
        .faculty-header { padding:12px 18px; font-weight:600; cursor:pointer; display:flex; justify-content:space-between; align-items:center; background:#e9edf5; border-radius:10px 10px 0 0; }
        .faculty-body { display:none; padding:15px; }
        .faculty-body.show { display:block; }

        .course-block { margin-bottom:16px; }
        .course-title { font-weight:600; color:#0d6efd; margin-bottom:8px; cursor:pointer; }
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
        .subject-link:hover { background:#3b82f6; color:#fff; }

        .empty-note { text-align:center; color:#6b7280; padding:10px; }
        .chevron { transition:.2s; }
        .rotate { transform:rotate(180deg); }
    </style>
</head>
<body>
<div class="container">
    <h1>Lecture Records</h1>

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
                                                    <a href="{{ route('admin.lecture-records.show', $subject->id) }}" class="subject-link">
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