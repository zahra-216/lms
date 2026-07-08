<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    

<div class="container mt-5">
    <h2 class="mb-4">Student Login</h2>

    {{-- Show validation errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('student.login.submit') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Registration Number</label>
            <input type="text" name="reg_no" class="form-control" required autofocus>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Faculty</label>
            <select name="faculty_id" class="form-control" required>
                <option value="">-- Select Faculty --</option>
                @foreach(\App\Models\Faculty::all() as $faculty)
                    <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                        {{ $faculty->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>

{{-- Optional: alert if redirected to login because of access restriction --}}
@if(session('redirected_from'))
<script>
    alert("Please login first to access that level.");
</script>
@endif


</body>
</html>