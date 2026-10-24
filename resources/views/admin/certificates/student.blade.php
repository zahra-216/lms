@extends('layouts.admin')
@section('title')
{{ $student->name }} — Certificates
@endsection
@section('back', route('admin.certificates.index'))
@push('styles')
<style>
.cert-card { background:#fff; border-radius:14px; padding:20px; margin-bottom:16px; box-shadow:0 8px 26px rgba(0,0,0,0.06); display:flex; gap:18px; align-items:center; flex-wrap:wrap;
}
.cert-card img { width:70px; height:70px; border-radius:10px; object-fit:cover; border:2px solid #e2e8f0;
}
.cert-info { flex:1; min-width:200px;
}
.cert-info .cert-number { font-weight:700; color:#012147; font-size:15px;
}
.cert-info .cert-meta { font-size:13px; color:#64748b; margin-top:2px;
}
.status-badge { padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700;
}
.status-Distinction { background:#dcfce7; color:#166534;
}
.status-Merit { background:#dbeafe; color:#1e40af;
}
.status-Pass { background:#fef9c3; color:#854d0e;
}
.btn-sm-action { padding:7px 12px; border:none; border-radius:8px; cursor:pointer; text-decoration:none; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:5px;
}
.edit-btn { background:#eef2f9; color:#012147;
}
.edit-btn:hover { background:#012147; color:#fff;
}
.delete-btn { background:#fee2e2; color:#991b1b;
}
.delete-btn:hover { background:#dc2626; color:#fff;
}
.empty-note { text-align:center; color:#6b7280; padding:40px; background:#fff; border-radius:14px;
}
@media (max-width:576px) {
.cert-card { flex-direction:column; align-items:flex-start;
}
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <div>
            <h2><i class="bi bi-person-badge"></i> {{ $student->name }}</h2>
            <small>{{ $student->registration_no }} — Certificates on record</small>
        </div>
        <a href="{{ route('admin.certificates.create', $student->id) }}" class="add-btn"><i class="bi bi-plus-circle"></i> Add Certificate</a>
    </div>

    

    @forelse($certificates as $cert)
        <div class="cert-card">
            @if($cert->photo)
                <img src="{{ asset('storage/'.$cert->photo) }}" alt="">
            @endif
            <div class="cert-info">
                <div class="cert-number">Cert No: {{ $cert->certificate_number }}</div>
                <div class="cert-meta">{{ $cert->course }}</div>
                <div class="cert-meta">{{ $cert->course_start->format('d M Y') }} — {{ $cert->course_end->format('d M Y') }}</div>
            </div>
            <span class="status-badge status-{{ $cert->award_status }}">{{ $cert->award_status }}</span>
            <div>
                <a href="{{ route('admin.certificates.edit', $cert->id) }}" class="btn-sm-action edit-btn">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <form action="{{ route('admin.certificates.destroy', $cert->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this certificate?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-sm-action delete-btn"><i class="bi bi-trash"></i> Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="empty-note">No certificates added yet for this student.</div>
    @endforelse
</div>
@endsection
