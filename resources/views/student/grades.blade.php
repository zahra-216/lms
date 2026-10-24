@extends('layouts.student')
@section('title')
My Grades
@endsection
@section('back', route('dashboard'))

@push('styles')
<style>
    .grade-badge { display:inline-block; min-width:64px; text-align:center; padding:5px 14px; border-radius:999px; font-weight:700; font-size:13px; }
    .grade-A { background:#dcfce7; color:#15803d; }
    .grade-B { background:#dbeafe; color:#1d4ed8; }
    .grade-C { background:#fef3c7; color:#b45309; }
    .grade-F { background:#fee2e2; color:#b91c1c; }
    .grade-none { background:#e2e8f0; color:#64748b; }
    .current-tag { background:var(--brand-700); color:#fff; font-size:11px; font-weight:700; letter-spacing:.08em; padding:4px 12px; border-radius:999px; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-clipboard-data"></i> My grades</h2>
            <small>{{ $student->name }} &middot; {{ $student->course->name ?? 'N/A' }}</small>
        </div>
    </div>

    @forelse ($semesterGroups as $group)
        <div class="card-box p-0 overflow-hidden">
            <div class="d-flex justify-content-between align-items-center px-4 py-3" style="background:var(--surface);border-bottom:1px solid var(--line)">
                <h3 class="section-title m-0"><i class="bi bi-journal-bookmark text-primary"></i> {{ $group['semester']->name }}</h3>
                @if ($loop->first)<span class="current-tag">CURRENT</span>@endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th class="ps-4">Subject</th><th class="text-end pe-4">Final grade</th></tr></thead>
                    <tbody>
                        @forelse ($group['subjects'] as $subject)
                            @php $sm = $subject->subjectMarks->first(); @endphp
                            <tr>
                                <td class="ps-4 fw-semibold"><i class="bi bi-book text-primary me-2"></i>{{ $subject->name }}</td>
                                <td class="text-end pe-4">
                                    @if ($sm && $sm->final_grade)
                                        <span class="grade-badge grade-{{ $sm->final_grade }}">{{ $sm->final_grade }}</span>
                                    @else
                                        <span class="grade-badge grade-none">Not graded</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-4">No modules found for this semester.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="empty-state"><i class="bi bi-clipboard-data"></i>No semesters found for your enrollment.</div>
    @endforelse
</div>
@endsection
