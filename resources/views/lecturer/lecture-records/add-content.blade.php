@extends('layouts.lecturer')
@section('title')
Add Content
@endsection
@section('back', route('lecturer.lecture-records.index'))
@push('styles')
<style>
.form-control[readonly] { background:#f1f5f9; color:#64748b;
}
.row-2 { display:flex; gap:14px;
}
@media (max-width:576px) {
.row-2 { flex-direction:column;
}
}
.row-2 > div { flex:1;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Add Content</h3>
        <small>Fill in what was covered in this lecture</small>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-box">
        <form action="{{ route('lecturer.lecture-records.add-content.store', $record->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Lecturer Name</label>
                <input type="text" class="form-control" value="{{ auth()->guard('lecturer')->user()->name }}" readonly>
            </div>

            <div class="row-2 mb-3">
                <div>
                    <label class="form-label">Date</label>
                    <input type="text" class="form-control" value="{{ $record->date ? \Carbon\Carbon::parse($record->date)->format('d M Y') : 'Not scheduled yet' }}" readonly>
                </div>
                <div>
                    <label class="form-label">Duration</label>
                    <input type="text" class="form-control" value="{{ $record->duration ?? '-' }}" readonly>
                </div>
            </div>

            <div class="row-2 mb-4">
                <div>
                    <label class="form-label">Start Time</label>
                    <input type="text" class="form-control" value="{{ $record->start_time ? \Carbon\Carbon::parse($record->start_time)->format('h:i A') : 'Not scheduled yet' }}" readonly>
                </div>
                <div>
                    <label class="form-label">End Time</label>
                    <input type="text" class="form-control" value="{{ $record->end_time ? \Carbon\Carbon::parse($record->end_time)->format('h:i A') : 'Not scheduled yet' }}" readonly>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Content Covered</label>
                <textarea name="content_covered" class="form-control" rows="4" required>{{ old('content_covered', $record->content_covered) }}</textarea>
            </div>

            <button class="btn btn-navy w-100">Save Content</button>
        </form>
    </div>
</div>
@endsection
