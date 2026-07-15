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

        {{-- password intentionally not shown/editable here --}}

        <button type="submit"><i class="fa fa-edit"></i> Update Student</button>
    </form>
</div>
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
</script>
</html>