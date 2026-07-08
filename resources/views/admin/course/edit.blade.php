<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Course | TT Metro Campus LMS</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* Same styling as create form */
* { box-sizing:border-box; margin:0; padding:0; font-family:'Segoe UI', sans-serif; }
body { background:#f9f9f9; min-height:100vh; display:flex; justify-content:center; padding:40px 10px; color:#012147; }
.container { background:#fff; border-radius:10px; padding:40px; max-width:500px; width:100%; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
h2 { text-align:center; margin-bottom:30px; color:#012147; }
form input, form select, form textarea { width:100%; padding:12px 15px; margin-bottom:20px; border:1px solid #ccc; border-radius:8px; font-size:16px; }
form textarea { resize: vertical; height:100px; }
.form-button-wrapper { text-align:center; }
form button { padding:10px 25px; background:#012147; color:#fff; border:none; border-radius:8px; font-size:16px; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:8px; transition:0.3s; }
form button:hover { background:#021634; }
img.preview { margin-top:10px; max-height:120px; border-radius:6px; }
.success-message, .error-message { padding:10px; border-radius:5px; margin-bottom:20px; text-align:center; }
.success-message { background:#d4edda; color:#155724; }
.error-message { background:#f8d7da; color:#721c24; }
.back-link{ display:block; text-align:center; margin-top:20px; text-decoration:none; color:#012147; font-weight:600; }
</style>
</head>
<body>

<div class="container">
    <h2>Edit Course</h2>

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

    <form method="POST" action="{{ route('admin.courses.update', $course->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="text" name="code" placeholder="Course Code" value="{{ old('code',$course->code) }}" required>
        <input type="text" name="name" placeholder="Course Name" value="{{ old('name',$course->name) }}" required>

        <select name="faculty_id" required>
            <option value="">Select Faculty</option>
            @foreach($faculties as $faculty)
                <option value="{{ $faculty->id }}" {{ (old('faculty_id',$course->faculty_id)==$faculty->id)?'selected':'' }}>
                    {{ $faculty->name }}
                </option>
            @endforeach
        </select>

        <textarea name="description" placeholder="Description">{{ old('description',$course->description) }}</textarea>

        <label>Course Image (optional)</label>
        <input type="file" name="image" accept="image/*">
        @if($course->image)
            <img src="{{ asset('storage/courses/'.$course->image) }}" class="preview" alt="{{ $course->name }}">
        @endif

        <div class="form-button-wrapper">
            <button type="submit"><i class="fa fa-save"></i> Update Course</button>
        </div>
    </form>

    <a href="{{ route('admin.courses.index') }}" class="back-link">← Back to Courses</a>
</div>

</body>
</html>