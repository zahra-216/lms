@extends('layouts.admin')
@section('title')
Lecturer Payments
@endsection
@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush
@push('styles')
<style>
.search-box { max-width:420px; margin:0 auto 25px;
}
table.lp-table { border-collapse:separate; border-spacing:0; width:100%;
}
table.lp-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 14px; white-space:nowrap;
}
table.lp-table thead th:first-child { border-top-left-radius:10px;
}
table.lp-table thead th:last-child { border-top-right-radius:10px;
}
table.lp-table tbody td { vertical-align:middle; padding:12px 14px; text-align:center;
}
table.lp-table tbody tr:nth-child(even) { background:#f8fafc;
}
table.lp-table tbody tr:hover { background:#eef2f9;
}
.view-btn {
        background:#012147; color:#fff; padding:8px 16px; border-radius:8px;
        text-decoration:none; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px;
}
.view-btn:hover { background:#1e3a6e; color:#fff;
}
@media (max-width:576px) {
table.lp-table { display:block; overflow-x:auto; white-space:nowrap;
}
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h2><i class="bi bi-wallet2"></i> Lecturer Payments</h2>
    </div>

    <form method="GET" action="{{ route('admin.lecturer-payments.index') }}" class="search-box">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name or username..." value="{{ $search }}">
            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
            @if($search)
                <a href="{{ route('admin.lecturer-payments.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    <div class="card-box">
        <div class="table-responsive">
        <table class="table lp-table align-middle">
            <thead>
                <tr><th>Name</th><th>Username</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse($lecturers as $lecturer)
                    <tr>
                        <td>{{ $lecturer->name }}</td>
                        <td>{{ $lecturer->username }}</td>
                        <td>
                            <a href="{{ route('admin.lecturer-payments.show', $lecturer->id) }}" class="view-btn">
                                <i class="bi bi-eye"></i> View Payments
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted py-4">No lecturers found.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
