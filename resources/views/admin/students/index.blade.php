<!DOCTYPE html>
<html>
<head>
    <title>Students | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI'; background: #f4f6f9; color:#012147; padding:40px;}
        .container { max-width:1000px; margin:auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,.1);}
        h1 { text-align:center; margin-bottom:20px;}
        table {width:100%; border-collapse:collapse; margin-top:20px;}
        th,td {padding:10px; border:1px solid #ddd; text-align:center;}
        .success {color:green; text-align:center;}
        .error {color:red; text-align:center;}
        .btn { padding:6px 12px; border:none; border-radius:6px; cursor:pointer; text-decoration:none; margin:2px; display:inline-flex; align-items:center; gap:5px;}
        .edit-btn {background:#ffc107; color:#012147;}
        .edit-btn:hover {background:#e0a800; color:#fff;}
        .delete-btn {background:#dc3545; color:#fff;}
        .delete-btn:hover {background:#a71d2a;}
        .add-btn { background:#012147;
        color:#fff;
        padding:10px 18px;
        border-radius:8px;
        text-decoration:none;
        font-weight:600;
        display:inline-flex;
        align-items:center;
        gap:8px;
        transition:.3s;}
        .add-btn:hover {background:#021634;}
    </style>
</head>
<body>
<div class="container">
    <h1>Students Details</h1>

    @if(session('success'))
    <p class="success">{{ session('success') }}</p>
    @endif

    <a href="{{ route('admin.students.create') }}" class="add-btn"><i class="fa fa-plus"></i> Add Student</a>
<table>
    <tr>
        <th>Reg No</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Branch</th>
        <th>Course</th>
        <th>Level</th>
        <th>Action</th>
    </tr>

    @foreach($students as $student)
    <tr>
        <td>{{ $student->registration_no }}</td>
        <td>{{ $student->name }}</td>
        <td>{{ $student->email }}</td>
        <td>{{ $student->branch }}</td>

        {{-- 🔥 SAFE DISPLAY --}}
        <td>{{ $student->course_id }}</td>
        <td>{{ $student->level_id }}</td>

        <td>
            <a href="{{ route('admin.students.edit', $student->id) }}" class="btn edit-btn">
                <i class="fa fa-edit"></i>
            </a>

            <form action="{{ route('admin.students.destroy', $student->id) }}"
                  method="POST"
                  style="display:inline;"
                  onsubmit="return confirm('Delete this student?')">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn delete-btn">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
</div>
</body>
</html>
