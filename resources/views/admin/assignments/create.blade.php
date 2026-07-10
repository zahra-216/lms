<!DOCTYPE html>
<html>
<head>
    <title>Create Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{ margin:0; font-family:'Segoe UI', sans-serif; background:#f3f6fb; }
        .top-bar{ background:#012147; color:white; padding:15px 20px; font-size:18px; font-weight:600; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
        .card-box{ max-width:900px; margin:40px auto; background:#fff; border-radius:18px; padding:35px; box-shadow:0 10px 30px rgba(0,0,0,0.08); border-top:5px solid #012147; }
        h2{ text-align:center; font-weight:700; margin-bottom:25px; color:#012147; }
        .section-title{ font-size:13px; color:#6b7280; margin-bottom:6px; font-weight:600; }
        .form-control, .form-select{ border-radius:10px; padding:11px; border:1px solid #e5e7eb; transition:0.2s; }
        .form-control:focus, .form-select:focus{ border-color:#012147; box-shadow:0 0 0 0.15rem rgba(1,33,71,0.15); }
        .btn-submit{ width:100%; padding:12px; border-radius:12px; font-weight:600; background:#012147; border:none; transition:0.2s; }
        .btn-submit:hover{ background:#023b7a; transform:translateY(-2px); }
        .row{ margin-bottom:10px; }
    </style>
</head>
<body>

<div class="top-bar">📚 Create Assignment - LMS Portal</div>

<div class="card-box">
<h2>Assignment Form</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.assignments.store') }}" method="POST" enctype="multipart/form-data">
@csrf

<!-- COURSE + LEVEL -->
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="section-title">Course</div>
        <select name="course_id" id="course_id" class="form-select">
            <option value="">Select Course</option>
            @foreach($courses as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <div class="section-title">Level</div>
        <select name="level_id" id="level_id" class="form-select">
            <option value="">Select Level</option>
            @foreach($levels as $l)
                <option value="{{ $l->id }}">{{ $l->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- SUBJECT -->
<div class="mb-3">
    <div class="section-title">Subject</div>
    <select name="subject_id" id="subject_id" class="form-select" required>
        <option value="">Select Subject</option>
    </select>
</div>

<!-- TITLE -->
<div class="mb-3">
    <div class="section-title">Title</div>
    <input type="text" name="title" class="form-control" required>
</div>

<!-- DESCRIPTION -->
<div class="mb-3">
    <div class="section-title">Description</div>
    <textarea name="description" class="form-control" rows="3" required></textarea>
</div>

<!-- DUE DATE -->
<div class="mb-3">
    <div class="section-title">Due Date</div>
    <input type="datetime-local" name="due_date" class="form-control" required>
</div>

<!-- TOTAL POINTS -->
<div class="mb-3">
    <div class="section-title">Total Points</div>
    <input type="number" name="total_points" class="form-control" min="0">
</div>

<!-- SUBMISSION TYPE -->
<div class="mb-3">
    <div class="section-title">Submission Type</div>
    <select name="submission_type" class="form-select">
        <option value="file">File Upload</option>
        <option value="text">Text</option>
        <option value="link">Link</option>
    </select>
</div>

<!-- ALLOW LATE -->
<div class="mb-3 form-check">
    <input type="checkbox" name="allow_late" value="1" class="form-check-input" id="allowLate">
    <label class="form-check-label" for="allowLate">Allow late submissions</label>
</div>

<!-- LATE PENALTY -->
<div class="mb-3">
    <div class="section-title">Late Penalty (%)</div>
    <input type="number" name="late_penalty" class="form-control" min="0" max="100">
</div>

<!-- PUBLISH -->
<div class="mb-3 form-check">
    <input type="checkbox" name="is_published" value="1" class="form-check-input" id="isPublished" checked>
    <label class="form-check-label" for="isPublished">Publish immediately</label>
</div>

<!-- ATTACHMENT -->
<div class="mb-3">
    <div class="section-title">Attachment (assignment brief / instructions document)</div>
    <input type="file" name="assignment_file" class="form-control">
</div>

<button class="btn btn-primary btn-submit mt-3">🚀 Create Assignment</button>

</form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

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
                if(data.length === 0){
                    $('#subject_id').append('<option>No Subjects Found</option>');
                    return;
                }
                data.forEach(function(s){
                    $('#subject_id').append(`<option value="${s.id}">${s.name}</option>`);
                });
            }
        });
    }
});

});
</script>

</body>
</html>