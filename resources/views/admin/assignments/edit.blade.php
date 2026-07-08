<!DOCTYPE html>
<html>
<head>
    <title>Edit Assignment</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background: linear-gradient(135deg,#f4f6ff,#eef2ff);
            font-family: 'Segoe UI', sans-serif;
        }

        .card-box{
            max-width: 850px;
            margin: 50px auto;
            background: #fff;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        h2{
            text-align:center;
            font-weight:700;
            margin-bottom:25px;
            color:#3b3b3b;
        }

        label{
            font-weight:600;
            margin-bottom:6px;
        }

        .form-control{
            border-radius:10px;
            padding:10px;
        }

        .form-control:focus{
            box-shadow: 0 0 0 0.2rem rgba(13,110,253,.15);
        }

        .btn-submit{
            width:100%;
            padding:12px;
            border-radius:12px;
            font-weight:600;
            background: linear-gradient(90deg,#f59e0b,#f97316);
            border:none;
            color:white;
        }

        .btn-submit:hover{
            transform: scale(1.02);
            transition: 0.2s;
        }

        .section-title{
            font-size:14px;
            color:#6b7280;
            margin-bottom:5px;
        }

        .back-btn{
            text-decoration:none;
            display:inline-block;
            margin-bottom:15px;
            color:#4f46e5;
            font-weight:600;
        }
    </style>
</head>

<body>

<div class="card-box">

    <a href="{{ route('admin.assignments.index') }}" class="back-btn">
        ⬅ Back
    </a>

    <h2>✏️ Edit Assignment</h2>

    <form action="{{ route('admin.assignments.update', $assignment->id) }}" method="POST" enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="row">

            <!-- Course -->
            <div class="col-md-6 mb-3">
                <div class="section-title">Course</div>
                <select name="course_id" id="course_id" class="form-control">
                    <option value="">Select Course</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}"
                            {{ $assignment->course_id == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Level -->
            <div class="col-md-6 mb-3">
                <div class="section-title">Level</div>
                <select name="level_id" id="level_id" class="form-control">
                    <option value="">Select Level</option>
                    @foreach($levels as $l)
                        <option value="{{ $l->id }}"
                            {{ $assignment->level_id == $l->id ? 'selected' : '' }}>
                            {{ $l->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="row">

            <!-- Subject -->
            <div class="col-md-6 mb-3">
                <div class="section-title">Subject</div>
                <select name="subject_id" id="subject_id" class="form-control">
                    <option value="">Select Subject</option>

                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}"
                            {{ $assignment->subject_id == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Note -->
            <div class="col-md-6 mb-3">
                <div class="section-title">Note</div>
                <select name="note_id" id="note_id" class="form-control">
                    <option value="">Select Note</option>

                    @foreach($notes as $n)
                        <option value="{{ $n->id }}"
                            {{ $assignment->note_id == $n->id ? 'selected' : '' }}>
                            {{ $n->title ?? $n->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <!-- Title -->
        <div class="mb-3">
            <div class="section-title">Title</div>
            <input type="text" name="title" class="form-control"
                   value="{{ $assignment->title }}" required>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <div class="section-title">Description</div>
            <textarea name="description" class="form-control" rows="3">{{ $assignment->description }}</textarea>
        </div>

        <!-- Due Date -->
        <div class="mb-3">
            <div class="section-title">Due Date</div>
            <input type="datetime-local" name="due_date" class="form-control"
                   value="{{ \Carbon\Carbon::parse($assignment->due_date)->format('Y-m-d\TH:i') }}">
        </div>

        <!-- File -->
        <div class="mb-3">
            <div class="section-title">Attachment</div>

            @if($assignment->file)
                <p class="text-muted">Current File: {{ $assignment->file }}</p>
            @endif

            <input type="file" name="assignment_file" class="form-control">
        </div>

        <!-- Submit -->
        <button class="btn btn-submit mt-3">
            💾 Update Assignment
        </button>

    </form>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// OPTIONAL AJAX (same like create page)

// SUBJECT LOAD
$('#course_id, #level_id').on('change', function(){

    let course = $('#course_id').val();
    let level = $('#level_id').val();

    $('#subject_id').html('<option>Loading...</option>');

    if(course && level){

        $.ajax({
            url: '/admin/get-subjects',
            type: 'GET',
            data: { course_id: course, level_id: level },

            success: function(data){

                $('#subject_id').empty().append('<option value="">Select Subject</option>');

                data.forEach(function(s){
                    $('#subject_id').append(`<option value="${s.id}">${s.name}</option>`);
                });
            }
        });
    }
});

// NOTES LOAD
$('#subject_id').on('change', function(){

    let id = $(this).val();

    $('#note_id').html('<option>Loading...</option>');

    if(id){

        $.ajax({
            url: '/admin/get-notes/' + id,
            type: 'GET',

            success: function(data){

                $('#note_id').empty().append('<option value="">Select Note</option>');

                data.forEach(function(n){
                    let title = n.title ?? n.name;
                    $('#note_id').append(`<option value="${n.id}">${title}</option>`);
                });
            }
        });
    }
});
</script>

</body>
</html>