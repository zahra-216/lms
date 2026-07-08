<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Semester Management | Admin Panel</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #f4f6f9; margin: 0; padding: 40px; color: #012147; }
        .container { max-width: 1100px; margin: auto; background: #fff; padding: 30px 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1 { text-align: center; margin-bottom: 30px; }

        /* Horizontal form */
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin-bottom: 30px;
        }
        form select, form input {
            flex: 1;
            min-width: 150px;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        form button {
            flex: 0 0 auto;
            padding: 10px 16px;
            background: #012147;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        form button:hover { background: #021634; }

        .success, .error { padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background: #012147; color: #fff; }
        td .btn { padding: 6px 12px; border-radius: 6px; font-weight: 600; text-decoration: none; color: #fff; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; }
        .btn-edit { background: #ffc107; color: #012147; }
        .btn-edit:hover { background: #e0a800; color: #fff; }
        .btn-delete { background: #dc3545; }
        .btn-delete:hover { background: #a71d2a; }

        @media (max-width: 768px) {
            form { flex-direction: column; }
            form select, form input, form button { width: 100%; }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Semester Management</h1>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Add / Edit Semester --}}
    <form action="{{ isset($editSemester) ? route('admin.semesters.update',$editSemester->id) : route('admin.semesters.store') }}" method="POST">
        @csrf
        @if(isset($editSemester)) @method('PUT') @endif

        <select name="level_id" required>
            <option value="">Select Level</option>
            @foreach($levels as $level)
                <option value="{{ $level->id }}" @if(isset($editSemester) && $editSemester->level_id==$level->id) selected @endif>{{ $level->name }}</option>
            @endforeach
        </select>

        <select name="course_id" required>
            <option value="">Select Course</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}" @if(isset($editSemester) && $editSemester->course_id==$course->id) selected @endif>{{ $course->name }}</option>
            @endforeach
        </select>

        <select name="name" required>
            <option value="">Select Semester</option>
            <option value="Semester 1">Semester 1</option>
            <option value="Semester 2">Semester 2</option>
            <option value="Semester 3">Semester 3</option>
            <option value="Semester 4">Semester 4</option>
            <option value="Semester 5">Semester 5</option>
            <option value="Semester 6">Semester 6</option>
        </select>

        <button type="submit"><i class="fa fa-plus"></i> {{ isset($editSemester) ? 'Update' : 'Add' }}</button>
    </form>

    {{-- List Semesters --}}
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Level</th>
                <th>Course</th>
                <th>Semester</th>
                <th>Actions</th>
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
                        <a class="btn btn-edit" href="{{ route('admin.semesters.edit', $semester->id) }}"><i class="fa fa-edit"></i> </a>
                        <form action="{{ route('admin.semesters.destroy', $semester->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-delete" type="submit" onclick="return confirm('Delete this semester?')"><i class="fa fa-trash"></i> </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</body>
</html>
