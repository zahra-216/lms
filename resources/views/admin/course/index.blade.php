<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Courses | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
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
        border-radius:18px; padding:26px 30px; margin-bottom:22px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2 { margin:0; font-weight:700; font-size:22px; }

    .add-btn {
        background:#fff; color:#012147; font-weight:600; padding:10px 20px;
        border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;
    }
    .add-btn:hover { background:#e2e8f0; color:#012147; }

    .card-box { background:#fff; border-radius:16px; padding:20px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }

    table.course-table { border-collapse:separate; border-spacing:0; width:100%; }
    table.course-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 14px; white-space:nowrap; }
    table.course-table thead th:first-child { border-top-left-radius:10px; }
    table.course-table thead th:last-child { border-top-right-radius:10px; }
    table.course-table tbody td { vertical-align:middle; padding:12px 14px; text-align:center; }
    table.course-table tbody tr:nth-child(even) { background:#f8fafc; }
    table.course-table tbody tr:hover { background:#eef2f9; }

    img.course-thumb { width:70px; height:52px; object-fit:cover; border-radius:8px; }

    .action-btn { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:8px; text-decoration:none; font-size:13px; font-weight:600; border:none; }
    .edit-btn { background:#eef2f9; color:#012147; }
    .edit-btn:hover { background:#012147; color:#fff; }
    .delete-btn { background:#fee2e2; color:#991b1b; }
    .delete-btn:hover { background:#dc2626; color:#fff; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header { flex-direction:column; align-items:flex-start; }
        table.course-table { display:block; overflow-x:auto; white-space:nowrap; }
    }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.dashboard') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h2><i class="bi bi-journal-bookmark"></i> Courses Management</h2>
        <a href="{{ route('admin.courses.create') }}" class="add-btn">
            <i class="bi bi-plus-circle"></i> Add Course
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

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
                            $imgPath = $course->image && file_exists(storage_path('app/public/courses/'.$course->image))
                                       ? 'storage/courses/'.$course->image
                                       : 'https://via.placeholder.com/80x60?text=No+Image';
                        @endphp
                        <img src="{{ asset($imgPath) }}" class="course-thumb" alt="{{ $course->name }}">
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
</body>
</html>