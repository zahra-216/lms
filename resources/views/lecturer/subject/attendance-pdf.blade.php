<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: sans-serif; font-size: 12px; color:#012147; }
    h2 { margin-bottom:2px; }
    .meta { margin-bottom:14px; color:#555; }
    table { width:100%; border-collapse:collapse; }
    th, td { border:1px solid #ccc; padding:6px 8px; text-align:left; }
    th { background:#012147; color:#fff; }
    .present { color:green; font-weight:bold; }
    .absent { color:red; font-weight:bold; }
</style>
</head>
<body>
    <h2>{{ $subject->code }} - {{ $subject->name }}</h2>
    <div class="meta">Attendance for {{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}</div>

    <table>
        <thead>
            <tr>
                <th>Reg No</th>
                <th>Student Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                @php $status = $records[$student->id] ?? null; @endphp
                <tr>
                    <td>{{ $student->registration_no }}</td>
                    <td>{{ $student->name }}</td>
                    <td class="{{ $status }}">{{ $status ? ucfirst($status) : 'Not Marked' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>