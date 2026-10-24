@extends('layouts.admin')
@section('title')
Certificate Verification
@endsection
@push('styles')
<style>
.search-box { max-width:420px; margin:0 0 25px;
}
.search-box .form-control { border-radius:10px 0 0 10px;
}
.search-box .btn { border-radius:0 10px 10px 0;
}
tbody tr:nth-child(even) { background:#f8fafc;
}
tbody tr:hover { background:#eef2f9;
}
.cert-badge { background:#eef2f9; color:#012147; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;
}
.manage-btn { background:#012147; color:#fff; padding:7px 14px; border-radius:8px; text-decoration:none; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px;
}
.manage-btn:hover { background:#1e3a6e; color:#fff;
}
.pagination { margin-top:16px;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h2><i class="bi bi-patch-check"></i> Certificate Verification</h2>
        <small>Search a student and manage their certificates.</small>
    </div>

    

    <form method="GET" action="{{ route('admin.certificates.index') }}" class="search-box">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name or reg no..." value="{{ $search }}">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
            @if($search)
                <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    @if($search)
        <div class="card-box">
            <div class="table-responsive">
            <table>
                <tr>
                    <th>Reg No</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Certificates</th>
                    <th>Action</th>
                </tr>
                @forelse($students as $student)
                    <tr>
                        <td>{{ $student->registration_no }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->course->name ?? '—' }}</td>
                        <td><span class="cert-badge">{{ $student->certificates_count }}</span></td>
                        <td>
                            <a href="{{ route('admin.certificates.student', $student->id) }}" class="manage-btn">
                                <i class="bi bi-folder2-open"></i> Manage
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No students found matching "{{ $search }}".</td></tr>
                @endforelse
            </table>
            </div>
        </div>
    @else
        <div class="card-box text-center py-5 text-muted">
            <i class="bi bi-search" style="font-size:32px;"></i>
            <p class="mt-3 mb-0">Search for a student by name or registration number to get started.</p>
        </div>
    @endif
</div>
@endsection
