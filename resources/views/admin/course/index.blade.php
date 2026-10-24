@extends('layouts.admin')
@section('title')
Courses
@endsection
@push('styles')
<style>
table.course-table { border-collapse:separate; border-spacing:0; width:100%;
}
table.course-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 14px; white-space:nowrap;
}
table.course-table thead th:first-child { border-top-left-radius:10px;
}
table.course-table thead th:last-child { border-top-right-radius:10px;
}
table.course-table tbody td { vertical-align:middle; padding:12px 14px; text-align:center;
}
table.course-table tbody tr:nth-child(even) { background:#f8fafc;
}
table.course-table tbody tr:hover { background:#eef2f9;
}
img.course-thumb { width:70px; height:52px; object-fit:cover; border-radius:8px;
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
@media (max-width:576px) {
table.course-table { display:block; overflow-x:auto; white-space:nowrap;
}
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h2><i class="bi bi-journal-bookmark"></i> Courses Management</h2>
        <a href="{{ route('admin.courses.create') }}" class="add-btn">
            <i class="bi bi-plus-circle"></i> Add Course
        </a>
    </div>

    

    <div class="card-box">
        <div class="table-responsive">
        <table class="table course-table align-middle">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Code</th>
                    <th>Course Name</th>
                    <th>Faculty</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                <tr>
                    <td>
                        @php
                            $imgPath = \App\Support\StockImages::forCourse($course);
                        @endphp
                        <img src="{{ $imgPath }}" class="course-thumb" alt="{{ $course->name }}">
                    </td>
                    <td>{{ $course->code }}</td>
                    <td>{{ $course->name }}</td>
                    <td>{{ $course->faculty->name }}</td>
                    <td>{{ $course->description ?? '-' }}</td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('admin.courses.edit',$course->id) }}" class="action-btn edit-btn">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.courses.destroy',$course->id) }}" onsubmit="return confirm('Delete this course?')">
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
