<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: sans-serif; font-size: 12px; color:#012147; }
    h1 { font-size:18px; margin-bottom:20px; }
    h2 { font-size:15px; margin:18px 0 4px; border-bottom:2px solid #012147; padding-bottom:3px; }
    h3 { font-size:13px; margin:10px 0 4px; color:#1e3a6e; }
    h4 { font-size:12px; margin:8px 0 4px; color:#334155; }
    table { width:100%; border-collapse:collapse; margin-bottom:10px; }
    th, td { border:1px solid #ccc; padding:5px 7px; text-align:left; }
    th { background:#012147; color:#fff; }
</style>
</head>
<body>
    @include('partials.pdf-header')
    <h1>Lecture Records — Grouped Report</h1>

    @foreach($grouped as $facultyName => $courses)
        <h2>{{ $facultyName }}</h2>

        @foreach($courses as $courseName => $levels)
            <h3>{{ $courseName }}</h3>

            @foreach($levels as $levelName => $modules)
                <h4>{{ $levelName }}</h4>

                @foreach($modules as $moduleLabel => $records)
                    <strong>{{ $moduleLabel }}</strong>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Duration</th>
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
                                    <td>{{ $record->duration ?? '—' }}</td>
                                    <td>{{ $record->lecturer->name ?? '—' }}</td>
                                    <td>{{ $record->content_covered ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            @endforeach
        @endforeach
    @endforeach
</body>
</html>