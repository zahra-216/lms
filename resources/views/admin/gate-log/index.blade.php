@extends('layouts.admin')
@section('title')
Gate Log
@endsection
@push('styles')
<style>
.filter-bar { display:flex; gap:12px; align-items:center; flex-wrap:wrap;
}
table.gl-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 10px; white-space:nowrap;
}
table.gl-table thead th:first-child { border-top-left-radius:10px;
}
table.gl-table thead th:last-child { border-top-right-radius:10px;
}
table.gl-table tbody td { vertical-align:middle; padding:10px;
}
table.gl-table tbody tr:nth-child(even) { background:#f8fafc;
}
.badge-in { background:#d1fae5; color:#065f46; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;
}
.badge-out { background:#fef3c7; color:#92400e; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;
}
.badge-missing { background:#f1f5f9; color:#94a3b8; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;
}
.type-pill { background:#eef2f9; color:#012147; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;
}
.icon-btn {
        border:none; background:#fff; color:#ef4444; width:34px; height:34px; border-radius:8px;
        box-shadow:0 2px 8px rgba(0,0,0,0.06); display:inline-flex; align-items:center; justify-content:center;
}
.icon-btn:hover { background:#ef4444; color:#fff;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <div>
            <h2><i class="bi bi-door-open"></i> Gate Log</h2>
            <small>Campus check-in / check-out records</small>
        </div>
        <a href="{{ route('admin.scan.index') }}" class="btn btn-light fw-semibold"><i class="bi bi-qr-code-scan"></i> Scan attendance</a>
    </div>

    

    <div class="card-box">
        <form method="GET" class="filter-bar">
            <label class="form-label mb-0 fw-semibold">Date:</label>
            <input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}"
                   class="form-control" style="max-width:200px;" onchange="this.form.submit()">
            <input type="text" id="nameSearch" class="form-control" placeholder="Search by name or reg no..." style="max-width:260px;">
        </form>
    </div>

    <div class="card-box">
        <div class="table-responsive">
        <table class="table gl-table align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Reg No</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($grouped as $row)
                    <tr data-name="{{ strtolower($row->name . ' ' . $row->ref) }}">
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->ref }}</td>
                        <td>
                            @if($row->check_in)
                                <span class="badge-in">{{ \Carbon\Carbon::parse($row->check_in)->format('h:i A') }}</span>
                            @else
                                <span class="badge-missing">-</span>
                            @endif
                        </td>
                        <td>
                            @if($row->check_out)
                                <span class="badge-out">{{ \Carbon\Carbon::parse($row->check_out)->format('h:i A') }}</span>
                            @else
                                <span class="badge-missing">Not yet</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.gate-log.destroy', [$row->user_type, $row->user_id]) }}?date={{ $date }}"
                                  method="POST" onsubmit="return confirm('Delete this record for {{ $row->name }} on {{ $date }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-btn" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No gate log entries for this date.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('nameSearch').addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();
    document.querySelectorAll('table.gl-table tbody tr[data-name]').forEach(row => {
        row.style.display = row.dataset.name.includes(query) ? '' : 'none';
    });
});
</script>
@endpush
