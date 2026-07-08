<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Semester | TT Metro Campus</title>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    * { box-sizing: border-box; margin:0; padding:0; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body { background-color: #f4f6f9; min-height: 100vh; display:flex; justify-content:center; align-items:flex-start; padding:40px 10px; color:#012147; }
    .container { background:#fff; border-radius:12px; padding:40px; max-width:500px; width:100%; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
    h2 { text-align:center; margin-bottom:30px; color:#012147; }
    form input, form select { width:100%; padding:12px 15px; margin-bottom:20px; border:1px solid #ccc; border-radius:8px; font-size:16px; }
    form button { padding:10px 25px; background:#012147; color:#fff; border:none; border-radius:8px; font-size:16px; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:8px; transition:0.3s; }
    form button:hover { background:#021634; }

    .success-message { background:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:20px; text-align:center; }
    .error-message { background:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:20px; text-align:center; }

    /* Small responsive tweaks */
    @media (max-width: 480px) {
        body { padding:20px 5px; }
        .container { padding:30px; }
    }
</style>
</head>
<body>

<div class="container">
    <h2>Edit Semester</h2>

    {{-- Success message --}}
    @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.semesters.update', $semester->id) }}">
        @csrf
        @method('PUT')

        <select name="course_id" required>
            <option value="">Select Course</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}" {{ $semester->course_id == $course->id ? 'selected' : '' }}>
                    {{ $course->name }}
                </option>
            @endforeach
        </select>

        <input type="text" name="name" placeholder="Semester 1 / Semester 2" value="{{ $semester->name }}" required>

        <div style="text-align:center;">
            <button type="submit">
                <i class="fa fa-edit"></i> Update Semester
            </button>
        </div>
    </form>
</div>

</body>
</html>
