<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Courses | TT Metro Campus LMS</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* { box-sizing:border-box; font-family:'Segoe UI', Tahoma, sans-serif; }
body { background:#f4f6f9; padding:40px; color:#012147; }
.container{ max-width:1000px; margin:auto; background:#fff; padding:35px; border-radius:12px; box-shadow:0 6px 16px rgba(0,0,0,.08); }
h1{ text-align:center; margin-bottom:25px; }
.top-bar{ display:flex; justify-content:flex-end; margin-bottom:20px; }
.add-btn{ background:#012147; color:white; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:600; }
.add-btn:hover{ background:#021634; }
.success{ background:#d4edda; color:#155724; padding:10px; border-radius:6px; text-align:center; margin-bottom:15px; }
table{ width:100%; border-collapse:collapse; }
th{ background:#012147; color:white; padding:14px; }
td{ padding:14px; border-bottom:1px solid #e6e6e6; text-align:center; vertical-align:middle; }
tbody tr:hover{ background:#f2f5ff; }
.actions{ display:flex; justify-content:center; gap:12px; }
.icon-btn{ border:none; padding:8px 12px; border-radius:8px; cursor:pointer; color:white; font-size:14px; }
.edit{ background:#ffc107; color:#012147; }
.delete{ background:#dc3545; }
.edit:hover{background:#e0a800}
.delete:hover{background:#b02a37}
img.course-thumb{ width:80px; height:60px; object-fit:cover; border-radius:5px; }
</style>
</head>
<body>

<div class="container">

<h1>Courses Management</h1>

@if(session('success'))
<div class="success">{{ session('success') }}</div>
@endif

<div class="top-bar">
    <a href="{{ route('admin.courses.create') }}" class="add-btn">
        <i class="fa fa-plus"></i> Add Course
    </a>
</div>

<table>
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
            // Check if image exists in storage
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
    <td class="actions">
        <a href="{{ route('admin.courses.edit',$course->id) }}">
            <button class="icon-btn edit"><i class="fa fa-pen"></i></button>
        </a>

        <form method="POST" action="{{ route('admin.courses.destroy',$course->id) }}">
            @csrf
            @method('DELETE')
            <button class="icon-btn delete" onclick="return confirm('Delete this course?')">
                <i class="fa fa-trash"></i>
            </button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>

</div>

</body>
</html>