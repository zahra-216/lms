@extends('layouts.admin')
@section('title')
Levels Management
@endsection
@push('styles')
<style>
.form-row { display:grid; grid-template-columns:1fr 1fr auto; gap:12px;
}
table.level-table { border-collapse:separate; border-spacing:0; width:100%;
}
table.level-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 14px; white-space:nowrap;
}
table.level-table thead th:first-child { border-top-left-radius:10px;
}
table.level-table thead th:last-child { border-top-right-radius:10px;
}
table.level-table tbody td { vertical-align:middle; padding:12px 14px; text-align:center;
}
table.level-table tbody tr:nth-child(even) { background:#f8fafc;
}
table.level-table tbody tr:hover { background:#eef2f9;
}
.actions { display:flex; justify-content:center; align-items:center; gap:10px;
}
.icon-btn { width:36px; height:36px; border:none; border-radius:9px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:14px; transition:0.2s;
}
.edit { background:#eef2f9; color:#012147;
}
.edit:hover { background:#012147; color:#fff;
}
.delete { background:#fee2e2; color:#991b1b;
}
.delete:hover { background:#dc2626; color:#fff;
}
@media (max-width:700px) {
.form-row { grid-template-columns:1fr;
}
}
@media (max-width:576px) {
table.level-table { display:block; overflow-x:auto; white-space:nowrap;
}
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h2><i class="bi bi-bar-chart-steps"></i> Course Levels</h2>
    </div>

    

    <div class="card-box">
        <div class="section-title">{{ isset($editLevel) ? 'Update Level' : 'Add Level' }}</div>

        <form method="POST" action="{{ isset($editLevel) ? route('admin.levels.update',$editLevel->id) : route('admin.levels.store') }}" class="form-row">
            @csrf
            @if(isset($editLevel)) @method('PUT') @endif

            <div>
                <label class="form-label">Course</label>
                <select name="course_id" class="form-select" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ isset($editLevel)&&$editLevel->course_id==$course->id?'selected':'' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Level</label>
                <select name="name" class="form-select" required>
                    <option value="">Select Level</option>
                    <option value="Diploma" {{ isset($editLevel)&&$editLevel->name=="Diploma"?'selected':'' }}>Diploma</option>
                    <option value="HND" {{ isset($editLevel)&&$editLevel->name=="HND"?'selected':'' }}>HND</option>
                    <option value="Top-up" {{ isset($editLevel)&&$editLevel->name=="Top-up"?'selected':'' }}>Top-Up</option>
                    <option value="Degree" {{ isset($editLevel)&&$editLevel->name=="Degree"?'selected':'' }}>Degree</option>
                </select>
            </div>

            <button type="submit" class="btn-navy">
                <i class="bi {{ isset($editLevel) ? 'bi-pencil' : 'bi-plus-circle' }}"></i>
                {{ isset($editLevel) ? 'Update' : 'Add' }}
            </button>
        </form>
    </div>

    <div class="card-box">
        <div class="section-title">All Levels</div>
        <div class="table-responsive">
        <table class="table level-table align-middle">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($levels as $level)
                    <tr>
                        <td>{{ $level->course->name }}</td>
                        <td>{{ $level->name }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.levels.edit',$level->id) }}">
                                <button type="button" class="icon-btn edit"><i class="bi bi-pencil"></i></button>
                            </a>
                            <form method="POST" action="{{ route('admin.levels.destroy',$level->id) }}" onsubmit="return confirm('Delete this level?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="icon-btn delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
