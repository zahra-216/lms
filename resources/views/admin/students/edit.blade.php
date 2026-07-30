<!DOCTYPE html>
<html>
<head>
    <title>Edit Student | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {font-family: 'Segoe UI'; background:#f4f6f9; color:#012147; padding:40px;}
        .container {max-width:500px; margin:auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,.1);}
        h2 {text-align:center; margin-bottom:20px;}
        input, select {width:100%; padding:12px; margin-bottom:15px; border-radius:8px; border:1px solid #ccc;}
        button {background:#ffc107; color:#012147; border:none; padding:12px 20px; border-radius:8px; cursor:pointer; display:flex; align-items:center; gap:8px;}
        button:hover {background:#e0a800; color:#fff;}
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Student</h2>

    @if($errors->any())
    <div class="error">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT')

        <input
            type="text"
            name="registration_no"
            value="{{ $student->registration_no }}"
            placeholder="Enter registration number"
            style="text-transform: uppercase;"
            oninput="this.value = this.value.toUpperCase();"
            required
        >
        <input type="text" name="name" value="{{ $student->name }}" required>
        <input type="email" name="email" value="{{ $student->email }}">
        <select name="branch" required>
            <option value="">Select Branch</option>
            <option value="Head Office – Mount Lavinia" {{ $student->branch == 'Head Office – Mount Lavinia' ? 'selected' : '' }}>Head Office – Mount Lavinia</option>
            <option value="Sammanthurai Branch" {{ $student->branch == 'Sammanthurai Branch' ? 'selected' : '' }}>Sammanthurai Branch</option>
            <option value="Batticaloa Branch" {{ $student->branch == 'Batticaloa Branch' ? 'selected' : '' }}>Batticaloa Branch</option>
            <option value="Trincomalee Branch" {{ $student->branch == 'Trincomalee Branch' ? 'selected' : '' }}>Trincomalee Branch</option>
            <option value="Nuwara Eliya Branch" {{ $student->branch == 'Nuwara Eliya Branch' ? 'selected' : '' }}>Nuwara Eliya Branch</option>
        </select>

        <<select name="course_id" id="course_id" required>
            <option value="">Select Course</option>
            @foreach($courses as $c)
                <option value="{{ $c->id }}" {{ $student->course_id == $c->id ? 'selected' : '' }}>{{ $c->code }} - {{ $c->name }}</option>
            @endforeach
        </select>

        <select name="level_id" id="level_id" required>
            <option value="">Select Level</option>
            @foreach($levels as $l)
                <option value="{{ $l->id }}" {{ $student->level_id == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
            @endforeach
        </select>

        <select name="semester_id" id="semester_id" required>
            <option value="">Select Semester</option>
            @foreach($semesters as $s)
                <option value="{{ $s->id }}" {{ $student->semester_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
            @endforeach
        </select>

        <div style="position:relative; margin-bottom:15px;">
            <input type="password" name="password" id="studentNewPassword" placeholder="New Password (leave blank to keep unchanged)" style="width:100%; padding:12px; padding-right:42px; border-radius:8px; border:1px solid #ccc;">
            <i class="fa fa-eye" id="toggleStudentPw1" onclick="togglePassword('studentNewPassword', this)" style="position:absolute; top:14px; right:14px; cursor:pointer;"></i>
        </div>

        <div style="position:relative; margin-bottom:15px;">
            <input type="password" name="password_confirmation" id="studentConfirmPassword" placeholder="Confirm New Password" style="width:100%; padding:12px; padding-right:42px; border-radius:8px; border:1px solid #ccc;">
            <i class="fa fa-eye" id="toggleStudentPw2" onclick="togglePassword('studentConfirmPassword', this)" style="position:absolute; top:14px; right:14px; cursor:pointer;"></i>
        </div>
        <div style="font-size:12px; color:#6b7280; margin:-10px 0 15px;">Only fill this in if you want to reset the student's password.</div>

        <button type="submit"><i class="fa fa-edit"></i> Update Student</button>
    </form>
</div>
<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#course_id').on('change', function () {
            let courseId = $(this).val();
            $('#level_id').html('<option value="">Loading...</option>');

            if (courseId) {
                $.ajax({
                    url: '/admin/get-levels/' + courseId,
                    type: 'GET',
                    success: function (data) {
                        $('#level_id').empty().append('<option value="">Select Level</option>');
                        if (data.length === 0) {
                            $('#level_id').append('<option>No levels found</option>');
                            return;
                        }
                        data.forEach(function (l) {
                            $('#level_id').append(`<option value="${l.id}">${l.name}</option>`);
                        });
                    }
                });
            } else {
                $('#level_id').html('<option value="">Select Level</option>');
            }
        });
    });

    $('#level_id').on('change', function () {
        let levelId = $(this).val();
        $('#semester_id').html('<option value="">Loading...</option>');

        if (levelId) {
            $.ajax({
                url: '/admin/get-semesters/' + levelId,
                type: 'GET',
                success: function (data) {
                    $('#semester_id').empty().append('<option value="">Select Semester</option>');
                    if (data.length === 0) {
                        $('#semester_id').append('<option>No semesters found</option>');
                        return;
                    }
                    data.forEach(function (s) {
                        $('#semester_id').append(`<option value="${s.id}">${s.name}</option>`);
                    });
                }
            });
        } else {
            $('#semester_id').html('<option value="">Select Semester</option>');
        }
    });
</script>
</html>