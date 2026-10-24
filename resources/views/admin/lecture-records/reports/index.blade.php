@extends('layouts.admin')
@section('title')
Lecture Reports
@endsection
@section('back', route('admin.lecture-records.index'))
@push('styles')
<style>
@media (max-width:576px) {
.top-actions { flex-direction:column; align-items:stretch !important;
}
}
.action-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
        cursor:pointer;
}
.action-btn:hover { background:#012147; color:#fff;
}
.action-btn.danger:hover { background:#ef4444; color:#fff;
}
.top-actions { display:flex; gap:10px; flex-wrap:wrap;
}
table.lr-table { border-collapse:separate; border-spacing:0;
}
table.lr-table thead th {
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap;
}
table.lr-table thead th:first-child { border-top-left-radius:10px;
}
table.lr-table thead th:last-child { border-top-right-radius:10px;
}
table.lr-table tbody td { vertical-align:middle; padding:10px;
}
table.lr-table tbody tr:nth-child(even) { background:#f8fafc;
}
table.lr-table tbody tr:hover { background:#eef2f9;
}
.row-actions { display:flex; gap:8px;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <div>
            <h2><i class="bi bi-file-earmark-pdf"></i> Lecture Reports</h2>
            <small>Generated monthly reports per lecturer</small>
        </div>
        <div class="top-actions">
            <a href="{{ route('admin.lecture-records.reports.create') }}" class="action-btn">
                <i class="bi bi-plus-circle"></i> Generate Report
            </a>
        </div>
    </div>

    

    <div class="card-box">
        <div class="table-responsive">
        <table class="table lr-table align-middle">
            <thead>
                <tr>
                    <th>Lecturer</th>
                    <th>Month</th>
                    <th>Generated</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>{{ $report->lecturer->name ?? '-' }}</td>
                        <td>{{ $report->month->format('F Y') }}</td>
                        <td>{{ $report->generated_at?->format('d M Y, h:i A') ?? '-' }}</td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('admin.lecture-records.reports.download', $report->id) }}" class="action-btn">
                                    <i class="bi bi-download"></i> Download
                                </a>
                                <form action="{{ route('admin.lecture-records.reports.destroy', $report->id) }}" method="POST"
                                      onsubmit="return confirm('Delete this report? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">No reports generated yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
