<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Student | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:560px; margin:auto; }

    .back-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover { background:#012147; color:#fff; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:24px 28px; margin-bottom:26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h3 { margin:0; font-weight:700; font-size:20px; }

    .card-box { background:#fff; border-radius:16px; padding:28px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }

    .form-label { font-weight:600; color:#012147; font-size:14px; margin-bottom:6px; }
    .form-control, .form-select { border-radius:10px; border:1px solid #e2e8f0; padding:11px 14px; }
    .form-control:focus, .form-select:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }

    .password-wrap { position:relative; }
    .password-wrap .form-control { padding-right:42px; }
    .toggle-eye { position:absolute; top:38px; right:14px; cursor:pointer; color:#64748b; }

    .btn-navy { width:100%; background:#012147; color:#fff; border:none; padding:12px; border-radius:12px; font-weight:600; }
    .btn-navy:hover { background:#0b2d5a; }

    @media (max-width:480px){ .card-box{ padding:20px; } }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.students.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <h3><i class="bi bi-person-plus"></i> Add Student</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-box">
        <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Registration Number</label>
                <input type="text" name="registration_no" class="form-control" placeholder="e.g. TTMC/ML/UG/CSE/25/006"
                       style="text-transform:uppercase;" oninput="this.value = this.value.toUpperCase();" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Branch</label>
                <select name="branch" class="form-select" required>
                    <option value="">Select Branch</option>
                    <option value="Head Office – Mount Lavinia">Head Office – Mount Lavinia</option>
                    <option value="Sammanthurai Branch">Sammanthurai Branch</option>
                    <option value="Batticaloa Branch">Batticaloa Branch</option>
                    <option value="Trincomalee Branch">Trincomalee Branch</option>
                    <option value="Nuwara Eliya Branch">Nuwara Eliya Branch</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Course</label>
                <select name="course_id" id="course_id" class="form-select" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Level</label>
                <select name="level_id" id="level_id" class="form-select" required>
                    <option value="">Select Level</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Semester</label>
                <select name="semester_id" id="semester_id" class="form-select" required>
                    <option value="">Select Semester</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Photo (optional)</label>
                <input type="file" name="photo" class="form-control" accept="image/png, image/jpeg">
            </div>

            <div class="mb-4 password-wrap">
                <label class="form-label">Password</label>
                <input type="password" name="password" id="studentPassword" class="form-control" required minlength="15"
                       autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');">
                <i class="bi bi-eye toggle-eye" onclick="togglePassword('studentPassword', this)"></i>
            </div>

            <button type="submit" class="btn-navy">Add Student</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

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
});
</script>
</body>
</html>