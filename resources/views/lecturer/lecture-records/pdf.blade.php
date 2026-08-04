<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: sans-serif; font-size: 11px; color:#012147; }
    h2 { margin-bottom:2px; font-size:16px; }
    .meta { margin-bottom:14px; color:#555; }
    table { width:100%; border-collapse:collapse; table-layout:fixed; }
    th, td { border:1px solid #ccc; padding:6px 8px; text-align:left; word-wrap:break-word; overflow-wrap:break-word; }
    th { background:#012147; color:#fff; font-size:11px; }
</style>
</head>
<body>
    @include('partials.pdf-header')
    <h2>My Lecture Records</h2>
    <div class="meta">{{ $month->format('F Y') }}</div>

    <table>
        <thead>
            <tr>
                <th style="width:13%;">Date</th>
                <th style="width:11%;">Start</th>
                <th style="width:11%;">End</th>
                <th style="width:12%;">Duration</th>
                <th style="width:33%;">Content Covered</th>
                <th style="width:10%;">Status</th>
                <th style="width:10%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grouped as $group)
                @php $first = $group->first(); @endphp
                <tr>
                    <td>{{ $first->date ? \Carbon\Carbon::parse($first->date)->format('d M Y') : '—' }}</td>
                    <td>{{ $first->start_time ? \Carbon\Carbon::parse($first->start_time)->format('h:i A') : '—' }}</td>
                    <td>{{ $first->end_time ? \Carbon\Carbon::parse($first->end_time)->format('h:i A') : '—' }}</td>
                    <td>{{ $first->duration ?? '—' }}</td>
                    <td>{{ $first->content_covered ?? '—' }}</td>
                    <td>{{ ($first->content_covered && $first->date) ? 'Complete' : 'Pending' }}</td>
                    <td>{{ $first->remarks ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center; color:#888;">No records found for this month.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>