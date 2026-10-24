@extends('layouts.student')
@section('title')
{{ $subject->name }} - Notes
@endsection
@push('styles')
<style>
table.marks-table { border-collapse:separate; border-spacing:0;
}
table.marks-table thead th {
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap; text-align:left;
}
table.marks-table thead th:first-child { border-top-left-radius:10px;
}
table.marks-table thead th:last-child { border-top-right-radius:10px;
}
table.marks-table tbody td { vertical-align:middle; padding:10px;
}
table.marks-table tbody tr:nth-child(even) { background:#f8fafc;
}
table.marks-table tbody tr:hover { background:#eef2f9;
}
.note-link {
        display:inline-flex; align-items:center; gap:6px;
        color:#012147; font-weight:600; text-decoration:none;
        padding:6px 14px; border-radius:20px; background:#eef2f9; font-size:13px;
}
.note-link:hover { background:#012147; color:#fff;
}
</style>
@endpush
@section('content')
<div class="container">
    <a href="{{ route('student.subject.portal.show', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <div>
            <h2><i class="bi bi-file-earmark-text"></i> {{ $subject->code }} — {{ $subject->name }}</h2>
            <small>Notes</small>
        </div>
        <i class="bi bi-journal-bookmark" style="font-size:44px; opacity:0.85;"></i>
    </div>

    <div class="card-box">
        <div class="section-title"><i class="bi bi-folder2-open"></i> Notes & Resources</div>

        <div class="table-responsive">
        <table class="table marks-table align-middle mb-0">
            <thead>
                <tr><th>Title</th><th>File / Link</th></tr>
            </thead>
            <tbody>
                @forelse($subject->notes as $note)
                    @if($note->is_published)
                    <tr>
                        <td>{{ $note->title }}</td>
                        <td>
                            @if($note->file_path)
                                <a href="{{ route('student.note.download', $note->id) }}" class="note-link">
                                    <i class="bi bi-download"></i> Download
                                </a>
                            @elseif($note->url)
                                <a href="{{ $note->url }}" target="_blank" class="note-link">
                                    <i class="bi bi-box-arrow-up-right"></i> Open Link
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="2" class="empty-state">No notes uploaded yet for this subject.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
