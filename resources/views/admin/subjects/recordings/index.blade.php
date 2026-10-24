@extends('layouts.admin')
@section('title')
{{ $subject->name }} - Recordings
@endsection
@section('back', route('admin.subjects.show', $subject->id))
@push('styles')
<style>
.rec-card { background:#fff; border-radius:14px; padding:18px 20px; box-shadow:0 6px 20px rgba(0,0,0,0.06); display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;
}
.rec-title { font-weight:600; color:#012147; margin:0;
}
.rec-actions a { margin-left:10px; text-decoration:none; font-size:1.1rem;
}
.rec-actions .edit-link { color:#3b82f6;
}
.rec-actions .del-link { color:#ef4444;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <div>
            <h3><i class="bi bi-collection-play"></i> Recordings</h3>
            <small>{{ $subject->code }} - {{ $subject->name }}</small>
        </div>
        <a href="{{ route('admin.subjects.recordings.create', $subject->id) }}" class="add-btn">
            <i class="bi bi-plus-circle"></i> Add Recording
        </a>
    </div>

    

    @forelse($recordings as $recording)
        <div class="rec-card">
            <p class="rec-title"><i class="bi bi-play-circle text-secondary me-2"></i>{{ $recording->title }}</p>
            <div class="rec-actions">
                <a href="{{ route('admin.subjects.recordings.edit', [$subject->id, $recording->id]) }}" class="edit-link"><i class="bi bi-pencil-square"></i></a>
                <a href="#" class="del-link" onclick="event.preventDefault(); document.getElementById('del-{{ $recording->id }}').submit();"><i class="bi bi-trash"></i></a>
                <form id="del-{{ $recording->id }}" action="{{ route('admin.subjects.recordings.destroy', [$subject->id, $recording->id]) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @empty
        <p class="text-muted">No recordings added yet.</p>
    @endforelse
</div>
@endsection
