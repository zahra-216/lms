@extends('layouts.lecturer')
@section('title')
{{ $subject->name }} - Notes
@endsection
@section('back', route('lecturer.subject.show', $subject->id))
@push('styles')
<style>
@media (max-width:576px) {
.page-header .add-btn { width:100%; text-align:center;
}
}
table.notes-table { border-collapse:separate; border-spacing:0;
}
table.notes-table thead th {
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap;
}
table.notes-table thead th:first-child { border-top-left-radius:10px;
}
table.notes-table thead th:last-child { border-top-right-radius:10px;
}
table.notes-table tbody td { vertical-align:middle; padding:10px;
}
table.notes-table tbody tr:nth-child(even) { background:#f8fafc;
}
table.notes-table tbody tr:hover { background:#eef2f9;
}
.badge-pill { padding:6px 12px; border-radius:20px; font-weight:600;
}
.badge-yes { background:#dcfce7; color:#15803d;
}
.badge-no { background:#e2e8f0; color:#64748b;
}
.btn-edit { background:#fef9c3; color:#a16207; border:none;
}
.btn-edit:hover { background:#fde68a; color:#a16207;
}
.btn-delete { background:#fee2e2; color:#b91c1c; border:none;
}
.btn-delete:hover { background:#fecaca; color:#b91c1c;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h2><i class="bi bi-file-earmark-text"></i> {{ $subject->code }} - {{ $subject->name }} - Notes</h2>
        <a href="{{ route('lecturer.notes.create', $subject->id) }}" class="add-btn">
            <i class="bi bi-plus-circle"></i> Add Note
        </a>
    </div>

    

    <div class="card-box">
    @if($subject->notes && $subject->notes->count())
    <div class="table-responsive">
        <table class="table notes-table align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Published</th>
                    <th>File / Link</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subject->notes as $note)
                <tr>
                    <td>{{ $note->title }}</td>
                    <td>{{ ucfirst($note->type) }}</td>
                    <td>
                        <span class="badge-pill {{ $note->is_published ? 'badge-yes' : 'badge-no' }}">
                            {{ $note->is_published ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        @if($note->file_path)
                            <a href="{{ route('lecturer.notes.download', $note->id) }}"><i class="bi bi-download"></i> Download</a>
                        @elseif($note->url)
                            <a href="{{ $note->url }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Open Link</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('lecturer.notes.edit', [$subject->id, $note->id]) }}" class="btn btn-sm btn-edit">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form action="{{ route('lecturer.notes.destroy', [$subject->id, $note->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this note?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-delete">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <p class="text-muted mb-0">No notes uploaded yet for this subject.</p>
    @endif
    </div>
</div>
@endsection
