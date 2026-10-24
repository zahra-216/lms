@extends('layouts.admin')
@section('title')
Semesters
@endsection
@push('styles')
<style>
.form-row { display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;
}
.form-row .form-group { flex:1; min-width:160px;
}
table.sem-table { border-collapse:separate; border-spacing:0; width:100%;
}
table.sem-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 14px; white-space:nowrap;
}
table.sem-table thead th:first-child { border-top-left-radius:10px;
}
table.sem-table thead th:last-child { border-top-right-radius:10px;
}
table.sem-table tbody td { vertical-align:middle; padding:12px 14px;
}
table.sem-table tbody tr:nth-child(even) { background:#f8fafc;
}
table.sem-table tbody tr:hover { background:#eef2f9;
}
.action-btn { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:8px; text-decoration:none; font-size:13px; font-weight:600; border:none;
}
.edit-btn { background:#eef2f9; color:#012147;
}
.edit-btn:hover { background:#012147; color:#fff;
}
.delete-btn { background:#fee2e2; color:#991b1b;
}
.delete-btn:hover { background:#dc2626; color:#fff;
}
@media (max-width:768px) {
.form-row { flex-direction:column; align-items:stretch;
}
}
@media (max-width:576px) {
table.sem-table { display:block; overflow-x:auto; white-space:nowrap;
}
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h2><i class="bi bi-calendar3"></i> Semester Management</h2>
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
        <div class="section-title">{{ isset($editSemester) ? 'Update Semester' : 'Add Semester' }}</div>

        <form action="{{ isset($editSemester) ? route('admin.semesters.update',$editSemester->id) : route('admin.semesters.store') }}" method="POST" class="form-row">
            @csrf
            @if(isset($editSemester)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label">Level</label>
                <select name="level_id" class="form-select" required>
                    <option value="">Select Level</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" @if(isset($editSemester) && $editSemester->level_id==$level->id) selected @endif>{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Course</label>
                <select name="course_id" class="form-select" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @if(isset($editSemester) && $editSemester->course_id==$course->id) selected @endif>{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Semester</label>
                <select name="name" class="form-select" required>
                    <option value="">Select Semester</option>
                    <option value="Semester 1">Semester 1</option>
                    <option value="Semester 2">Semester 2</option>
                    <option value="Semester 3">Semester 3</option>
                    <option value="Semester 4">Semester 4</option>
                    <option value="Semester 5">Semester 5</option>
                    <option value="Semester 6">Semester 6</option>
                </select>
            </div>

            <button type="submit" class="btn-navy">
                <i class="bi {{ isset($editSemester) ? 'bi-pencil' : 'bi-plus-circle' }}"></i>
                {{ isset($editSemester) ? 'Update' : 'Add' }}
            </button>
        </form>
    </div>

    <div class="card-box">
        <div class="section-title">All Semesters</div>
        <div class="table-responsive">
            <table class="table sem-table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Level</th>
                        <th>Course</th>
                        <th>Semester</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($semesters as $semester)
                        <tr>
                            <td>{{ $semester->id }}</td>
                            <td>{{ $semester->level->name }}</td>
                            <td>{{ $semester->course->name }}</td>
                            <td>{{ $semester->name }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.semesters.edit', $semester->id) }}" class="action-btn edit-btn">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.semesters.destroy', $semester->id) }}" method="POST" onsubmit="return confirm('Delete this semester?')">
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
    </div>
</div>
@endsection
