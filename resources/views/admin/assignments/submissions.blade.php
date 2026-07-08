<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Assignment Submissions</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6fb;
    font-family:'Segoe UI', sans-serif;
}

/* HEADER */
.page-header{
    background: linear-gradient(135deg,#1e3a8a,#2563eb);
    color:white;
    padding:20px;
    border-radius:12px;
    margin-bottom:20px;
}

/* CARD */
.card-box{
    background:white;
    padding:20px;
    border-radius:14px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
}

/* TABLE */
.table thead{
    background:#1e3a8a;
    color:white;
}

.table tbody tr:hover{
    background:#f1f5ff;
}

/* BUTTON */
.download-btn{
    background:#2563eb;
    color:white;
    padding:6px 12px;
    border-radius:6px;
    text-decoration:none;
    font-size:14px;
}

.download-btn:hover{
    background:#1e40af;
}

/* EMPTY */
.empty-box{
    text-align:center;
    padding:40px;
    color:#6b7280;
}
</style>

</head>
<body>

<div class="container mt-5">

    <!-- HEADER -->
    <div class="page-header">
        <h4 class="mb-0">
            📄 {{ $assignment->title }} - Submissions
        </h4>
    </div>

    <!-- CARD -->
    <div class="card-box">

        @if($assignment->submissions->count() > 0)

        <div class="table-responsive">
            <table class="table align-middle table-bordered">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>File</th>
                        <th>Submitted Date</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($assignment->submissions as $key => $sub)
                    <tr>
                        <td>{{ $key + 1 }}</td>

                        <td>
                            <b>{{ $sub->student->name }}</b>
                        </td>

                        <td>
                            <a href="{{ asset('storage/'.$sub->file) }}"
                               target="_blank"
                               class="download-btn">
                               ⬇ Download
                            </a>
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y h:i A') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        @else

        <!-- EMPTY -->
        <div class="empty-box">
            <h5>😕 No submissions yet</h5>
            <p>Students haven't submitted this assignment.</p>
        </div>

        @endif

    </div>

</div>

</body>
</html>