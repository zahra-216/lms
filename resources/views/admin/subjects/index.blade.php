<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Subjects | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
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
        border-radius:18px; padding:26px 30px; margin-bottom:26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h2 { margin:0; font-weight:700; font-size:22px; }

    .card-box { background:#fff; border-radius:16px; padding:26px; box-shadow:0 8px 26px rgba(0,0,0,0.06); margin-bottom:26px; }
    .section-title { font-weight:700; color:#012147; margin-bottom:18px; font-size:16px; }

    .form-label { font-weight:600; color:#012147; font-size:14px; margin-bottom:6px; }
    .form-control, .form-select { border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px; }
    .form-control:focus, .form-select:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }

    .btn-navy { background:#012147; color:#fff; border:none; padding:11px 26px; border-radius:10px; font-weight:600; }
    .btn-navy:hover { background:#0b2d5a; color:#fff; }

    table.subject-table { border-collapse:separate; border-spacing:0; width:100%; }
    table.subject-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 14px; white-space:nowrap; }
    table.subject-table thead th:first-child { border-top-left-radius:10px; }
    table.subject-table thead th:last-child { border-top-right-radius:10px; }
    table.subject-table tbody td { vertical-align:middle; padding:12px 14px; }
    table.subject-table tbody tr:nth-child(even) { background:#f8fafc; }
    table.subject-table tbody tr:hover { background:#eef2f9; }

    .action-btn { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:8px; text-decoration:none; font-size:13px; font-weight:600; border:none; }
    .edit-btn { background:#eef2f9; color:#012147; }
    .edit-btn:hover { background:#012147; color:#fff; }
    .delete-btn { background:#fee2e2; color:#991b1b; }
    .delete-btn:hover { background:#dc2626; color:#fff; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        table.subject-table { display:block; overflow-x:auto; white-space:nowrap; }
    }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.dashboard') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h2><i class="bi bi-book"></i> Subjects Management</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif
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
        <div class="section-title">{{ isset($editSubject) ? 'Update Subject' : 'Add New Subject' }}</div>

        <form method="POST" action="{{ isset($editSubject) ? route('admin.subjects.update',$editSubject->id) : route('admin.subjects.store') }}" class="row g-3">
            @csrf
            @if(isset($editSubject)) @method('PUT') @endif

            <div class="col-md-4">
                <label class="form-label">Course</label>
                <select class="form-select" name="course_id" id="course" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (isset($editSubject) && $editSubject->course_id==$course->id) ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Level</label>
                <select class="form-select" name="level_id" id="level" required>
                    <option value="">Select Level</option>
                    @if(isset($levels))
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}" {{ (isset($editSubject) && $editSubject->level_id==$level->id) ? 'selected' : '' }}>
                                {{ $level->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Semester</label>
                <select class="form-select" name="semester_id" id="semester" required>
                    <option value="">Select Semester</option>
                    @if(isset($semesters))
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ (isset($editSubject) && $editSubject->semester_id==$sem->id) ? 'selected' : '' }}>
                                {{ $sem->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Subject Code</label>
                <input type="text" class="form-control" name="code" placeholder="Code" value="{{ $editSubject->code ?? '' }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Subject Name</label>
                <input type="text" class="form-control" name="name" placeholder="Name" value="{{ $editSubject->name ?? '' }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Credits</label>
                <input type="number" class="form-control" name="credits" placeholder="Credits" value="{{ $editSubject->credits ?? '' }}">
            </div>

            <div class="col-12 text-center mt-2">
                <button type="submit" class="btn-navy">
                    <i class="bi {{ isset($editSubject) ? 'bi-pencil' : 'bi-plus-circle' }}"></i>
                    {{ isset($editSubject) ? 'Update' : 'Save' }}
                </button>
            </div>
        </form>
    </div>

    <div class="card-box">
        <div class="section-title">All Subjects</div>
        <div class="table-responsive">
            <table class="table subject-table align-middle">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Level</th>
                        <th>Semester</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Credits</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $s)
                    <tr>
                        <td>{{ $s->course->name }}</td>
                        <td>{{ $s->level->name }}</td>
                        <td>{{ $s->semester->name }}</td>
                        <td>{{ $s->code }}</td>
                        <td>{{ $s->name }}</td>
                        <td>{{ $s->credits }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.subjects.edit',$s->id) }}" class="action-btn edit-btn">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('admin.subjects.destroy',$s->id) }}" method="POST" onsubmit="return confirm('Delete this subject?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $subjects->links() }}
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function(){

    function loadLevels(courseId, selectedLevel = null){
        $('#level').html('<option value="">Loading Levels...</option>');
        $('#semester').html('<option value="">Select Semester</option>');

        if(courseId){
            $.ajax({
                url: "{{ url('/admin/get-levels') }}/" + courseId,
                type: "GET",
                success: function(data){
                    $('#level').html('<option value="">Select Level</option>');
                    data.forEach(function(l){
                        let selected = (selectedLevel == l.id) ? 'selected' : '';
                        $('#level').append(`<option value="${l.id}" ${selected}>${l.name}</option>`);
                    });
                },
                error: function(){ alert("Levels load failed"); }
            });
        }
    }

    function loadSemesters(levelId, selectedSemester = null){
        $('#semester').html('<option value="">Loading Semesters...</option>');

        if(levelId){
            $.ajax({
                url: "{{ url('/admin/get-semesters') }}/" + levelId,
                type: "GET",
                success: function(data){
                    $('#semester').html('<option value="">Select Semester</option>');
                    data.forEach(function(s){
                        let selected = (selectedSemester == s.id) ? 'selected' : '';
                        $('#semester').append(`<option value="${s.id}" ${selected}>${s.name}</option>`);
                    });
                },
                error: function(){ alert("Semesters load failed"); }
            });
        }
    }

    $('#course').on('change', function(){ loadLevels($(this).val()); });
    $('#level').on('change', function(){ loadSemesters($(this).val()); });

    @if(isset($editSubject))
        loadLevels({{ $editSubject->course_id }}, {{ $editSubject->level_id }});
        loadSemesters({{ $editSubject->level_id }}, {{ $editSubject->semester_id }});
    @endif
});
</script>
</body>
</html>