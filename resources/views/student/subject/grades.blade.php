@extends('layouts.student')
@section('title')
{{ $subject->name }} - Grades
@endsection
@section('back', route('student.subject.portal.show', $subject->id))
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
.grade-badge {
        font-size:13px; padding:6px 12px; border-radius:20px; font-weight:600;
}
.grade-A { background:#dcfce7; color:#15803d;
}
.grade-B { background:#dbeafe; color:#1d4ed8;
}
.grade-C { background:#fef9c3; color:#a16207;
}
.grade-F { background:#fee2e2; color:#b91c1c;
}
.grade-none { background:#e2e8f0; color:#64748b;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <div>
            <h2><i class="bi bi-bar-chart-line"></i> {{ $subject->code }} — {{ $subject->name }}</h2>
            <small>Grades</small>
        </div>
        <i class="bi bi-clipboard-data" style="font-size:44px; opacity:0.85;"></i>
    </div>

    <div class="card-box">
        <div class="section-title"><i class="bi bi-journal-text"></i> Assignments</div>

        <div class="table-responsive">
        <table class="table marks-table align-middle mb-0">
            <thead>
                <tr><th>Assignment</th><th>Marks</th><th>Grade</th></tr>
            </thead>
            <tbody>
                @forelse($subject->assignments as $assignment)
                    @php $mark = $assignment->marks->first(); @endphp
                    <tr>
                        <td>{{ $assignment->title }}</td>
                        <td>{{ $mark->marks ?? 'Not graded yet' }}</td>
                        <td>
                            @if($mark && $mark->grade)
                                <span class="grade-badge grade-{{ $mark->grade }}">{{ $mark->grade }}</span>
                            @else
                                <span class="grade-badge grade-none">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-state">No assignments for this subject yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="card-box">
        <div class="section-title"><i class="bi bi-graph-up"></i> Overall Subject Marks</div>

        <div class="table-responsive">
        <table class="table marks-table align-middle mb-0">
            <thead>
                <tr>
                    <th>Mid Marks</th>
                    <th>Final Exam</th>
                    <th>Final Grade</th>
                </tr>
            </thead>
            <tbody>
                @if($subjectMark)
                <tr>
                    <td>{{ $subjectMark->mid_marks }}</td>
                    <td>{{ $subjectMark->final_exam_marks }}</td>
                    <td>
                        @if($subjectMark->final_grade)
                            <span class="grade-badge grade-{{ $subjectMark->final_grade }}">{{ $subjectMark->final_grade }}</span>
                        @else
                            <span class="grade-badge grade-none">-</span>
                        @endif
                    </td>
                </tr>
                @else
                <tr><td colspan="3" class="empty-state">No marks recorded yet for this subject.</td></tr>
                @endif
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
