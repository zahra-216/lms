@extends('layouts.lecturer')
@section('title')
{{ $faculty->name }} - Courses
@endsection
@section('back', route('lecturer.dashboard'))

@push('styles')
<style>
    .course-tile { position:relative; display:flex; align-items:flex-end; min-height:170px; border-radius:var(--radius); overflow:hidden; cursor:pointer; color:#fff; background-size:cover; background-position:center; box-shadow:var(--shadow-card); transition:transform .2s, box-shadow .2s; }
    .course-tile:hover { transform:translateY(-4px); box-shadow:var(--shadow-lift); }
    .course-tile::before { content:''; position:absolute; inset:0; background:linear-gradient(180deg, rgba(6,26,51,.15) 0%, rgba(6,26,51,.88) 100%); }
    .course-tile > div { position:relative; padding:16px 18px; width:100%; }
    .course-tile h5 { color:#fff; font-size:15.5px; font-weight:800; margin:0 0 6px; }
    .structure-card { border:1px solid var(--line); border-radius:var(--radius); background:#fff; box-shadow:var(--shadow-card); margin-bottom:14px; padding:18px 22px; }
    .structure-header { cursor:pointer; }
    .structure-header h4 { font-size:17px; font-weight:800; margin:0 0 2px; }
    .semester-btn { background:var(--surface); border:1px solid var(--line); color:var(--navy-900); font-weight:600; border-radius:12px; padding:10px 16px; }
    .semester-btn:hover { background:var(--sky-soft); }
    .subject-list .list-group-item { border-color:var(--line); }
    .subject-list a { color:var(--navy-900); }
    .subject-list a:hover { color:var(--brand-700); }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-building"></i> {{ $faculty->name }}</h2>
            <small>Choose a course, then open a semester to reach its subjects.</small>
        </div>
    </div>

    @if ($faculty->courses->count())
        <h3 class="section-title"><i class="bi bi-collection text-primary"></i> Courses</h3>
        <input type="search" id="searchInput" class="form-control mb-3" placeholder="Search courses by name or code...">

        <div class="row g-3 mb-5" id="coursesContainer">
            @foreach ($faculty->courses as $course)
                @php
                    $rel = 'storage/courses/'.$course->image;
                    $img = ($course->image && file_exists(public_path($rel))) ? asset($rel) : asset('images/ttmc.jpeg');
                @endphp
                <div class="col-6 col-md-4 col-xl-3 course-item">
                    <div class="course-tile" style="background-image:url('{{ $img }}')" onclick="openCourseStructure('course-{{ $course->id }}')">
                        <div>
                            <h5>{{ $course->code }} - {{ $course->name }}</h5>
                            <span class="badge bg-success">{{ ucfirst($course->status) }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <h3 class="section-title"><i class="bi bi-diagram-3 text-primary"></i> Courses, levels and subjects</h3>
        @foreach ($faculty->courses as $course)
            @php $collapseId = 'course-'.$course->id; @endphp
            <div class="structure-card">
                <div class="d-flex justify-content-between align-items-center structure-header" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false">
                    <div>
                        <h4>{{ $course->code }} - {{ $course->name }}</h4>
                        <small class="text-muted">{{ $course->description ?? 'No course description available.' }}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary">{{ ucfirst($course->status) }}</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                </div>

                <div class="collapse mt-3" id="{{ $collapseId }}">
                    @forelse ($course->levels as $level)
                        @php
                            $semesterCount = match (strtolower(trim($level->name))) {
                                'hnd' => 4,
                                'diploma' => 2,
                                'degree' => 6,
                                default => $level->semesters->count(),
                            };
                            $semesters = $level->semesters->values();
                        @endphp
                        <div class="mb-4">
                            <h5 class="fw-bold">{{ $level->name }}</h5>
                            @if ($semesterCount > 0)
                                @for ($i = 1; $i <= $semesterCount; $i++)
                                    @php
                                        $semester = $semesters->get($i - 1);
                                        $semesterName = $semester?->name ?? "Semester {$i}";
                                        $semId = 'semester-'.$course->id.'-'.$level->id.'-'.$i;
                                    @endphp
                                    <div class="mb-2 ps-3">
                                        <button class="btn semester-btn w-100 text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $semId }}" aria-expanded="false">
                                            <span>{{ $semesterName }}</span><i class="bi bi-chevron-down"></i>
                                        </button>
                                        <div class="collapse mt-2" id="{{ $semId }}">
                                            @if ($semester && $semester->subjects->count())
                                                <ul class="list-group subject-list mb-2">
                                                    @foreach ($semester->subjects as $subject)
                                                        <li class="list-group-item py-2">
                                                            <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                                                                <span>{{ $subject->code }} - {{ $subject->name }}</span><i class="bi bi-chevron-right"></i>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="text-muted ps-3 mb-2">No subjects found for this semester.</p>
                                            @endif
                                        </div>
                                    </div>
                                @endfor
                            @else
                                <p class="text-muted ps-3">No semesters available for this level.</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">No levels configured for this course.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state"><i class="bi bi-collection"></i>No courses available for this faculty yet.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function openCourseStructure(id) {
    var target = document.getElementById(id);
    if (!target) return;
    bootstrap.Collapse.getOrCreateInstance(target, { toggle: false }).show();
    setTimeout(function () { target.closest('.structure-card').scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 150);
}
var search = document.getElementById('searchInput');
if (search) search.addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('.course-item').forEach(function (el) { el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none'; });
});
</script>
@endpush
