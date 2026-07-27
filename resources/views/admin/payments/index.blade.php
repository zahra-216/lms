<!DOCTYPE html>
<html>
<head>
    <title>Student Payments | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI'; background: #f4f6f9; color:#012147; padding:40px; }
        .container { max-width:1200px; margin:auto; }
        h1 { text-align:center; margin-bottom:20px; }
        .search-box { max-width:400px; margin:0 auto 25px; }
        .level-card { background:#012147; color:#fff; border-radius:12px; margin-bottom:18px; overflow:hidden; }
        .level-card .level-header { padding:16px 22px; cursor:pointer; display:flex; justify-content:space-between; align-items:center; }
        .level-card .level-body { background:#f4f6f9; color:#012147; padding:20px; display:none; }
        .level-card .level-body.show { display:block; }
        .faculty-block { background:#fff; border-radius:10px; margin-bottom:14px; box-shadow:0 2px 8px rgba(0,0,0,.06); }
        .faculty-header { padding:12px 18px; font-weight:600; cursor:pointer; display:flex; justify-content:space-between; align-items:center; background:#e9edf5; border-radius:10px 10px 0 0; }
        .faculty-body { display:none; padding:15px; }
        .faculty-body.show { display:block; }
        .course-block { margin-bottom:16px; }
        .course-title { font-weight:600; color:#0d6efd; margin-bottom:8px; }
        table { width:100%; border-collapse:collapse; margin-bottom:10px; }
        th, td { padding:10px; border:1px solid #ddd; text-align:center; font-size:14px; }
        th { background:#f0f2f7; }
        .btn { padding:6px 12px; border:none; border-radius:6px; cursor:pointer; text-decoration:none; margin:2px; display:inline-flex; align-items:center; gap:5px; }
        .view-btn { background:#012147; color:#fff; }
        .view-btn:hover { background:#021634; color:#fff; }
        .empty-note { text-align:center; color:#6b7280; padding:10px; }
        .chevron { transition:.2s; }
        .rotate { transform:rotate(180deg); }
    </style>
</head>
<body>
<div class="container">
    <h1>Student Payments</h1>

    @if(session('success'))
        <p class="text-success text-center">{{ session('success') }}</p>
    @endif

    <form method="GET" action="{{ route('admin.payments.index') }}" class="search-box">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name or reg no..." value="{{ $search }}">
            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
            @if($search)
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    @php $levelIcons = ['Diploma' => 'fa-certificate', 'HND' => 'fa-graduation-cap', 'Top-up' => 'fa-arrow-up', 'Degree' => 'fa-award']; @endphp

    @foreach($grouped as $levelName => $faculties)
        @php $levelId = \Illuminate\Support\Str::slug($levelName); @endphp
        <div class="level-card">
            <div class="level-header" onclick="toggleBlock('level-{{ $levelId }}', this)">
                <span><i class="fa {{ $levelIcons[$levelName] ?? 'fa-layer-group' }}"></i> {{ $levelName }}</span>
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
                                <div class="course-block">
                                    <div class="course-title"><i class="fa fa-book"></i> {{ $courseName }}</div>
                                    <table>
                                        <tr><th>Reg No</th><th>Full Name</th><th>Action</th></tr>
                                        @foreach($students as $student)
                                            <tr>
                                                <td>{{ $student->registration_no }}</td>
                                                <td>{{ $student->name }}</td>
                                                <td>
                                                    <a href="{{ route('admin.payments.show', $student->id) }}" class="btn view-btn">
                                                        <i class="fa fa-eye"></i> View Payments
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
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
    headerEl.querySelector('.chevron').classList.toggle('rotate');
}
@if($search)
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.level-body, .faculty-body').forEach(el => el.classList.add('show'));
    document.querySelectorAll('.chevron').forEach(el => el.classList.add('rotate'));
});
@endif
</script>
</body>
</html>