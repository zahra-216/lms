@extends('layouts.admin')
@section('title')
Enter Marks
@endsection
@push('styles')
<style>
table.marks-entry-table { border-collapse:separate; border-spacing:0;
}
table.marks-entry-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 10px; white-space:nowrap;
}
table.marks-entry-table thead th:first-child { border-top-left-radius:10px;
}
table.marks-entry-table thead th:last-child { border-top-right-radius:10px;
}
table.marks-entry-table tbody td { vertical-align:middle; padding:10px;
}
table.marks-entry-table tbody tr:nth-child(even) { background:#f8fafc;
}
table.marks-entry-table tbody tr:hover { background:#eef2f9;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h4><i class="bi bi-pencil-square"></i> {{ $assignment->title }} - Marks Entry</h4>
        <small>{{ $assignment->subject->name }}</small>
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
    @if($submissions->count())
    <form method="POST" action="{{ route('admin.marks.store') }}">
        @csrf
        <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

        <div class="table-responsive">
        <table class="table marks-entry-table align-middle">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Reg No</th>
                    <th>File</th>
                    <th>Marks (0-100)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $sub)
                <tr>
                    <td>{{ $sub->student->name ?? 'Unknown' }}</td>
                    <td>{{ $sub->student->registration_no ?? '-' }}</td>
                    <td>
                        @if($sub->file)
                            <a href="{{ asset('storage/'.$sub->file) }}" target="_blank"><i class="bi bi-download"></i> View</a>
                        @else
                            <span class="text-muted">No file</span>
                        @endif
                    </td>
                    <td>
                        <input type="number"
                               name="marks[{{ $sub->student_id }}]"
                               class="form-control"
                               min="0"
                               max="100"
                               value="{{ $existingMarks[$sub->student_id] ?? '' }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        <button class="btn btn-navy w-100">
            <i class="bi bi-save"></i> Save Marks
        </button>
    </form>
    @else
        <p class="text-muted mb-0">No students have submitted this assignment yet - marks can't be entered until there's a submission.</p>
    @endif
    </div>
</div>
@endsection
