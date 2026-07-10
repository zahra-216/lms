<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Assignment</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width:700px;">
    <a href="{{ route('lecturer.subject.assignments', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h3>Create Assignment — {{ $subject->name }}</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('lecturer.assignments.store', $subject->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Due Date</label>
            <input type="datetime-local" name="due_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Total Points</label>
            <input type="number" name="total_points" class="form-control" min="0">
        </div>

        <div class="mb-3">
            <label class="form-label">Submission Type</label>
            <select name="submission_type" class="form-select">
                <option value="file">File Upload</option>
                <option value="text">Text</option>
                <option value="link">Link</option>
            </select>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="allow_late" value="1" class="form-check-input" id="allowLate">
            <label class="form-check-label" for="allowLate">Allow late submissions</label>
        </div>

        <div class="mb-3">
            <label class="form-label">Late Penalty (%)</label>
            <input type="number" name="late_penalty" class="form-control" min="0" max="100">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="is_published" value="1" class="form-check-input" id="isPublished" checked>
            <label class="form-check-label" for="isPublished">Publish immediately</label>
        </div>

        <div class="mb-3">
            <label class="form-label">Attachment (assignment brief / instructions document)</label>
            <input type="file" name="assignment_file" class="form-control">
        </div>

        <button class="btn btn-primary w-100">Create Assignment</button>
    </form>
</div>
</body>
</html>