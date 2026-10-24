@extends('layouts.admin')
@section('title')
{{ $subject->name }} - Attendance History
@endsection
@section('back', route('admin.attendance.show', $subject->id))
@push('styles')
<style>
.month-card {
        background:#fff; border-radius:14px; padding:18px 20px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); text-decoration:none; color:#012147;
        display:flex; justify-content:space-between; align-items:center;
        transition:0.15s;
}
.month-card:hover { background:#eef2f9; color:#012147;
}
.month-card .icons a { color:#012147; margin-left:14px; font-size:18px;
}
.month-card .icons button { background:none; border:none; color:#dc2626; margin-left:14px; font-size:18px; cursor:pointer;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h2><i class="bi bi-clock-history"></i> {{ $subject->code }} - Attendance History</h2>
    </div>

    

    <div class="d-flex flex-column gap-3">
        @forelse($months as $month)
            <div class="month-card">
                <span><i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</span>
                <span class="icons d-flex align-items-center">
                    <a href="{{ route('admin.attendance.monthly.pdf', ['id' => $subject->id, 'month' => $month]) }}" target="_blank" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.attendance.monthly.pdf', ['id' => $subject->id, 'month' => $month]) }}?download=1" title="Download">
                        <i class="bi bi-download"></i>
                    </a>
                    <form action="{{ route('admin.attendance.monthly.destroy', ['id' => $subject->id, 'month' => $month]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete all attendance records for {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Delete"><i class="bi bi-trash"></i></button>
                    </form>
                </span>
            </div>
        @empty
            <div class="month-card justify-content-center text-muted">No attendance records yet.</div>
        @endforelse
    </div>
</div>
@endsection
