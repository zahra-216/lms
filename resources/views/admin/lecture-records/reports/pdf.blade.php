<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: sans-serif; font-size: 11px; color:#012147; }
    h2 { font-size:16px; margin:4px 0 2px; }
    .meta { margin-bottom:18px; color:#555; }
    table { width:100%; border-collapse:collapse; margin-bottom:16px; table-layout:fixed; }
    th, td { border:1px solid #ccc; padding:6px 8px; text-align:left; vertical-align:top; word-wrap:break-word; overflow-wrap:break-word; }
    th { background:#012147; color:#fff; font-size:11px; }
    .modules-cell { font-weight:600; }
</style>
</head>
<body>
    @include('partials.pdf-header')

    <h2>Lecture Report — {{ $lecturer->name }}</h2>
    <div class="meta">{{ $month->format('F Y') }}</div>

    <table>
        <thead>
            <tr>
                <th style="width:22%;">Content Covered</th>
                <th style="width:43%;">Module(s)</th>
                <th style="width:20%;">Dates</th>
                <th style="width:15%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grouped as $group)
                <tr>
                    <td>{{ $group['content'] ?: '—' }}</td>
                    <td class="modules-cell">
                        @forelse($group['modules'] as $mod)
                            {{ $mod }}@if(!$loop->last)<br>@endif
                        @empty
                            —
                        @endforelse
                    </td>
                    <td>
                        @php
                            $uniqueDates = $group['records']->pluck('date')->filter()->unique()->values();
                        @endphp
                        @forelse($uniqueDates as $date)
                            {{ \Carbon\Carbon::parse($date)->format('d M Y') }}@if(!$loop->last)<br>@endif
                        @empty
                            —
                        @endforelse
                    </td>
                    <td>
                        @foreach($group['records'] as $record)
                            {{ $record->remarks ?? '—' }}@if(!$loop->last)<br>@endif
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center; color:#888;">No records found for this lecturer in this month.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>