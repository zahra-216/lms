@extends('layouts.admin')
@section('title')
Assignment Submissions
@endsection
@push('styles')
<style>
.table thead {
    background:#1e3a8a;
    color:white;
}
.table tbody tr:hover {
    background:#f1f5ff;
}
.download-btn {
    background:#2563eb;
    color:white;
    padding:6px 12px;
    border-radius:6px;
    text-decoration:none;
    font-size:14px;
}
.download-btn:hover {
    background:#1e40af;
}
</style>
@endpush
@section('content')
<div class="container mt-5">

    <!-- HEADER -->
    <div class="page-header">
        <h4 class="mb-0">
            📄 {{ $assignment->title }} - Submissions
        </h4>
    </div>

    <!-- CARD -->
    <div class="card-box">

        @if($assignment->submissions->count() > 0)

        <div class="table-responsive">
            <table class="table align-middle table-bordered">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>File</th>
                        <th>Submitted Date</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($assignment->submissions as $key => $sub)
                    <tr>
                        <td>{{ $key + 1 }}</td>

                        <td>
                            <b>{{ $sub->student->name }}</b>
                        </td>

                        <td>
                            <a href="{{ asset('storage/'.$sub->file) }}"
                               target="_blank"
                               class="download-btn">
                               ⬇ Download
                            </a>
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y h:i A') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        @else

        <!-- EMPTY -->
        <div class="empty-box">
            <h5>😕 No submissions yet</h5>
            <p>Students haven't submitted this assignment.</p>
        </div>

        @endif

    </div>

</div>
@endsection
