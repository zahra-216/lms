<!DOCTYPE html>
<html>
<head>
    <title>Assignments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background: linear-gradient(135deg,#f4f6ff,#eef2ff);
            font-family: 'Segoe UI', sans-serif;
        }

        .card-box{
            max-width: 1000px;
            margin: 50px auto;
            background: #fff;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        h2{
            text-align:center;
            font-weight:700;
            margin-bottom:25px;
        }

        .btn-create{
            float:right;
            border-radius:10px;
            font-weight:600;
        }

        table th{
            background:#f3f4f6;
        }
    </style>
</head>

<body>

<div class="card-box">

    <h2>📚 Assignments</h2>

    <a href="{{ route('admin.assignments.create') }}" class="btn btn-primary btn-create mb-3">
        + Create Assignment
    </a>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Course</th>
                <th>Level</th>
                <th>Subject</th>
                <th>Due Date</th>
                <th>Attachment</th> <!-- ⭐ NEW -->
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse($assignments as $a)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $a->title }}</td>
                <td>{{ $a->subject->course->name ?? '-' }}</td>
                <td>{{ $a->subject->level->name ?? '-' }}</td>
                <td>{{ $a->subject->name ?? '-' }}</td>
                <td>{{ $a->due_date }}</td>

                <!-- ⭐ FILE SHOW -->
                <td>
                    
                      @if($a->file_path)
    <a href="{{ asset('storage/' . $a->file_path) }}"
       target="_blank"
       class="btn btn-sm btn-success">
        View File
    </a>
@else
    <span class="text-muted">No File</span>
@endif
                   
                      
                </td>

                <td>
                    
                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No Assignments Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>

</body>
</html>