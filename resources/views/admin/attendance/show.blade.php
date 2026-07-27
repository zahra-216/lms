<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background:#f4f6f9; font-family:'Segoe UI', sans-serif; padding:40px; }
    .container { max-width:900px; margin:auto; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2 class="mb-4">{{ $subject->code }} - {{ $subject->name }} — Attendance</h2>

    <h5 class="mb-3">By Date</h5>

    <form method="GET" action="{{ route('admin.attendance.show', $subject->id) }}" class="mb-3 d-flex gap-2 align-items-end">
        <div>
            <label>Select Date</label>
            <input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}" class="form-control" onchange="this.form.submit()">
        </div>
    </form>

    <a href="{{ route('admin.attendance.pdf', ['id' => $subject->id, 'date' => $date]) }}" class="btn btn-sm btn-primary mb-3">
        Download This Date (PDF)
    </a>

    <table class="table table-bordered bg-white">
        <thead>
            <tr><th>Reg No</th><th>Student Name</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                @php $status = $attendance->get($student->id)?->status; @endphp
                <tr>
                    <td>{{ $student->registration_no }}</td>
                    <td>{{ $student->name }}</td>
                    <td>
                        @if($status === 'present') <span class="badge bg-success">Present</span>
                        @elseif($status === 'absent') <span class="badge bg-danger">Absent</span>
                        @else <span class="badge bg-secondary">Not Marked</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h5 class="mt-5 mb-3">Attendance Summary ({{ $totalClasses }} classes conducted)</h5>

    <a href="{{ route('admin.attendance.summary.pdf', $subject->id) }}" class="btn btn-sm btn-primary mb-3">
        Download Summary (PDF)
    </a>

    <table class="table table-bordered bg-white">
        <thead>
            <tr><th>Reg No</th><th>Student Name</th><th>Present</th><th>Total Classes</th><th>Percentage</th></tr>
        </thead>
        <tbody>
            @foreach($summary as $row)
                <tr>
                    <td>{{ $row->student->registration_no }}</td>
                    <td>{{ $row->student->name }}</td>
                    <td>{{ $row->present_count }}</td>
                    <td>{{ $row->total_classes }}</td>
                    <td>{{ $row->percentage }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>