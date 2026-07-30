<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: sans-serif; font-size: 12px; color:#012147; }
    h2 { margin-bottom:14px; }
    table { width:100%; border-collapse:collapse; }
    th, td { border:1px solid #ccc; padding:6px 8px; text-align:left; }
    th { background:#012147; color:#fff; }
</style>
</head>
<body>
    <h2>My Lecture Records</h2>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Lecturer</th>
                <th>Content Covered</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->date ? \Carbon\Carbon::parse($record->date)->format('d M Y') : '—' }}</td>
                    <td>{{ $record->start_time ? \Carbon\Carbon::parse($record->start_time)->format('h:i A') : '—' }}</td>
                    <td>{{ $record->end_time ? \Carbon\Carbon::parse($record->end_time)->format('h:i A') : '—' }}</td>
                    <td>{{ $record->lecturer->name ?? '—' }}</td>
                    <td>{{ $record->content_covered ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>