<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Subjects Management - LMS Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
.container { margin-top: 40px; }
.page-title { color: #021634; font-weight: 700; text-align: center; margin-bottom: 30px; font-size: 2rem; }
.card { border-radius: 12px; }
.btn-edit { background-color: #ffc107; color: #000; }
.btn-edit:hover { background-color: #e0a800; color: #fff; }
.btn-delete { background-color: #dc3545; color: #fff; }
.btn-delete:hover { background-color: #a71d2a; }
.table th { background-color: #021634; color: #fff; }
</style>
</head>
<body>

<div class="container">
    <h1 class="page-title"><i class="fa fa-book-open me-2"></i>Subjects Management</h1>

    <div class="card shadow-sm">
        <div class="card-body">

            <!-- Messages -->
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li><i class="fa fa-exclamation-triangle me-1"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Subject Form -->
            <form method="POST" action="{{ isset($editSubject) ? route('admin.subjects.update',$editSubject->id) : route('admin.subjects.store') }}" class="row g-3 mb-4">
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

                <div class="col-12 text-center mt-3">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fa {{ isset($editSubject) ? 'fa-pen' : 'fa-plus' }} me-2"></i>
                        {{ isset($editSubject) ? 'Update' : 'Save' }}
                    </button>
                </div>
            </form>

            <!-- Subjects Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
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
                            <td class="text-center">
                                <a href="{{ route('admin.subjects.edit',$s->id) }}" class="btn btn-edit btn-sm">
                                    <i class="fa fa-pen"></i> Edit
                                </a>
                                <form action="{{ route('admin.subjects.destroy',$s->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete btn-sm" onclick="return confirm('Delete this subject?')">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $subjects->links() }}
            </div>

        </div>
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
                        $('#level').append(
                            `<option value="${l.id}" ${selected}>${l.name}</option>`
                        );
                    });
                },
                error: function(){
                    alert("Levels load failed");
                }
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
                        $('#semester').append(
                            `<option value="${s.id}" ${selected}>${s.name}</option>`
                        );
                    });
                },
                error: function(){
                    alert("Semesters load failed");
                }
            });

        }
    }

    // COURSE CHANGE
    $('#course').on('change', function(){
        let courseId = $(this).val();
        loadLevels(courseId);
    });

    // LEVEL CHANGE
    $('#level').on('change', function(){
        let levelId = $(this).val();
        loadSemesters(levelId);
    });

    // EDIT MODE
    @if(isset($editSubject))
        loadLevels({{ $editSubject->course_id }}, {{ $editSubject->level_id }});
        loadSemesters({{ $editSubject->level_id }}, {{ $editSubject->semester_id }});
    @endif

});
</script>
</body>
</html>