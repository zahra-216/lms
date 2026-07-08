<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Levels Management</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{
    box-sizing:border-box;
    font-family:Inter,sans-serif;
}

body{
    background:#f4f6f9;
    padding:40px;
    color:#012147;
}

.container{
    max-width:900px;
    margin:auto;
    background:#fff;
    padding:35px 40px;
    border-radius:14px;
    box-shadow:0 6px 16px rgba(0,0,0,.08);
}

h1{
    text-align:center;
    margin-bottom:25px;
}

form{
    display:grid;
    grid-template-columns:1fr 1fr auto;
    gap:12px;
    margin-bottom:30px;
}

select,button{
    padding:12px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:15px;
}

button{
    background:#012147;
    color:white;
    border:none;
    cursor:pointer;
    font-weight:600;
}

button:hover{
    background:#021634;
}

.success{
    text-align:center;
    color:#1e7e34;
    font-weight:600;
    margin-bottom:15px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:14px;
    text-align:center;
    vertical-align:middle;
}

th{
    background:#012147;
    color:white;
}

tbody tr:hover{
    background:#f1f5ff;
}

td{
    border-bottom:1px solid #e6e6e6;
}

/* 🔥 FIXED ACTION ALIGNMENT */
.actions{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:12px;
}

.actions a,
.actions form{
    display:inline-flex;
    margin:0;
}

/* ICON BUTTONS */
.icon-btn{
    width:38px;
    height:38px;
    border:none;
    border-radius:9px;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:15px;
    transition:0.2s ease;
}

.icon-btn:hover{
    transform:scale(1.1);
}

.edit{
    background:#ffc107;
    color:#012147;
}

.delete{
    background:#dc3545;
    color:white;
}

.edit:hover{background:#e0a800}
.delete:hover{background:#b02a37}

@media(max-width:700px){
    form{
        grid-template-columns:1fr;
    }
}
</style>
</head>
<body>

<div class="container">

<h1>Course Levels</h1>

@if(session('success'))
<div class="success">{{ session('success') }}</div>
@endif

<form method="POST"
action="{{ isset($editLevel) ? route('admin.levels.update',$editLevel->id) : route('admin.levels.store') }}">

@csrf
@if(isset($editLevel)) @method('PUT') @endif

<select name="course_id" required>
<option value="">Select Course</option>
@foreach($courses as $course)
<option value="{{ $course->id }}"
{{ isset($editLevel)&&$editLevel->course_id==$course->id?'selected':'' }}>
{{ $course->name }}
</option>
@endforeach
</select>

<select name="name" required>
<option value="">Select Level</option>
<option value="Diploma" {{ isset($editLevel)&&$editLevel->name=="Diploma"?'selected':'' }}>Diploma</option>
<option value="HND" {{ isset($editLevel)&&$editLevel->name=="HND"?'selected':'' }}>HND</option>
<option value="Top-up" {{ isset($editLevel)&&$editLevel->name=="Top-up"?'selected':'' }}>Top-Up</option>
<option value="Degree" {{ isset($editLevel)&&$editLevel->name=="Degree"?'selected':'' }}>Degree</option>
</select>

<button>
<i class="fa fa-save"></i>
{{ isset($editLevel) ? 'Update' : 'Add' }}
</button>

</form>

<table>
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
<button class="icon-btn edit">
<i class="fa fa-pen"></i>
</button>
</a>

<form method="POST" action="{{ route('admin.levels.destroy',$level->id) }}">
@csrf @method('DELETE')
<button class="icon-btn delete" onclick="return confirm('Delete this level?')">
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
