@extends('layouts.lecturer')
@section('title')
{{ $subject->name }} - Grades
@endsection
@section('back', route('lecturer.subject.show', $subject->id))
@push('styles')
<style>
@media (max-width:576px) {
.assignment-row { flex-direction:column; align-items:flex-start !important; gap:10px;
}
.assignment-row a { width:100%; text-align:center;
}
}
.assignment-row {
        display:flex; justify-content:space-between; align-items:center;
        padding:14px 18px; border:1px solid #e5e7eb; border-radius:12px;
        margin-bottom:10px; background:#fff; transition:0.2s;
}
.assignment-row:hover { box-shadow:0 6px 16px rgba(0,0,0,0.06);
}
#studentSearch {
        border-radius:10px; padding:10px 14px; border:1px solid #e2e8f0;
}
table.marks-table { border-collapse:separate; border-spacing:0;
}
table.marks-table thead th {
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap;
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
.mark-input { border-radius:8px;
}
.mark-input:read-only { background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0;
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
.grade-D { background:#ffedd5; color:#c2410c;
}
.grade-F { background:#fee2e2; color:#b91c1c;
}
.grade-none { background:#e2e8f0; color:#64748b;
}
#addEditBtn i { margin-right:6px;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <div>
            <h2><i class="bi bi-journal-check"></i> {{ $subject->code }} - {{ $subject->name }}</h2>
            <small>Grades Overview</small>
        </div>
        <i class="bi bi-clipboard-data" style="font-size:44px; opacity:0.85;"></i>
    </div>

    

    <div class="card-box">
        <div class="section-title"><i class="bi bi-journal-text"></i> Assignments</div>

        @if($subject->assignments->count())
            @foreach($subject->assignments as $assignment)
                <div class="assignment-row">
                    <div>
                        <strong>{{ $assignment->title }}</strong><br>
                        <small class="text-muted">
                            {{ $assignment->submissions->count() }} submission(s) ·
                            {{ $assignment->marks->count() }} graded
                        </small>
                    </div>
                    <a href="{{ route('lecturer.marks.create', $assignment->id) }}" class="btn btn-sm btn-navy">
                        Enter / Edit Marks
                    </a>
                </div>
            @endforeach
        @else
            <p class="text-muted mb-0">No assignments found for this subject.</p>
        @endif
    </div>

    <div class="card-box">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="section-title mb-0"><i class="bi bi-bar-chart-steps"></i> All Marks</div>
            <button type="button" id="addEditBtn" class="btn btn-sm btn-navy" onclick="unlockMarks()">
                <i class="bi bi-pencil-square"></i>Add/Edit
            </button>
        </div>

        <div class="mb-3">
            <input type="text" id="studentSearch" class="form-control" placeholder="Search by name or reg no...">
        </div>

        @if($students->count())
        <form method="POST" action="{{ route('lecturer.subject.marks.update', $subject->id) }}" id="marksForm">
            @csrf
            <div class="table-responsive">
            <table class="table marks-table align-middle">
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Student Name</th>
                        <th>Assignment Marks</th>
                        <th>Mid Marks</th>
                        <th>Practical Marks</th>
                        <th>Final Exam</th>
                        <th>Final Mark</th>
                        <th>Final Grade</th>
                    </tr>
                </thead>
                <tbody id="marksTableBody">
                    @foreach($students as $student)
                        @php $sm = $subjectMarks->get($student->id); @endphp
                        <tr>
                            <td>{{ $student->registration_no }}</td>
                            <td>{{ $student->name }}</td>
                            <td>
                                <input type="text" class="form-control form-control-sm mark-input" readonly
                                    name="marks[{{ $student->id }}][assignment_marks]"
                                    value="{{ $sm->assignment_marks ?? '' }}">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm mark-input" readonly
                                    name="marks[{{ $student->id }}][mid_marks]"
                                    value="{{ $sm->mid_marks ?? '' }}">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm mark-input" readonly
                                    name="marks[{{ $student->id }}][practical_marks]"
                                    value="{{ $sm->practical_marks ?? '' }}">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm mark-input" readonly
                                    name="marks[{{ $student->id }}][final_exam_marks]"
                                    value="{{ $sm->final_exam_marks ?? '' }}">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm mark-input" readonly
                                    name="marks[{{ $student->id }}][final_marks]"
                                    value="{{ $sm->final_marks ?? '' }}"
                                    oninput="updateGradePreview(this)">
                            </td>
                            <td>
                                @php
                                    $fg = $sm->final_grade ?? null;
                                    $badgeClass = $fg ? ($fg === 'AB' ? 'grade-none' : 'grade-'.$fg[0]) : 'grade-none';
                                @endphp
                                <span class="grade-badge {{ $badgeClass }}">
                                    {{ $fg ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            <button type="submit" class="btn btn-navy" id="saveMarksBtn" style="display:none;">
                <i class="bi bi-save"></i> Save Marks
            </button>
        </form>

        
        @else
            <p class="text-muted mb-0">No students found for this subject.</p>
        @endif
    </div>
</div>
@endsection
@push('scripts')
<script>
        function unlockMarks() {
            document.querySelectorAll('.mark-input').forEach(function (input) {
                input.readOnly = false;
            });
            document.getElementById('saveMarksBtn').style.display = 'inline-block';
            document.getElementById('addEditBtn').style.display = 'none';
        }

        function updateGradePreview(input) {
            const row = input.closest('tr');
            const badge = row.querySelector('.grade-badge');
            const raw = input.value.trim();

            badge.classList.remove('grade-A','grade-B','grade-C','grade-D','grade-F','grade-none');

            if (raw === '') {
                badge.textContent = '-';
                badge.classList.add('grade-none');
                return;
            }

            if (raw.toUpperCase() === 'AB') {
                badge.textContent = 'AB';
                badge.classList.add('grade-none');
                return;
            }

            const val = parseFloat(raw);
            if (isNaN(val)) {
                badge.textContent = '-';
                badge.classList.add('grade-none');
                return;
            }

            let grade;
            if (val >= 85) grade = 'A+';
            else if (val >= 75) grade = 'A';
            else if (val >= 70) grade = 'A-';
            else if (val >= 65) grade = 'B+';
            else if (val >= 60) grade = 'B';
            else if (val >= 55) grade = 'B-';
            else if (val >= 50) grade = 'C+';
            else if (val >= 45) grade = 'C';
            else if (val >= 40) grade = 'C-';
            else if (val >= 35) grade = 'D+';
            else if (val >= 30) grade = 'D';
            else grade = 'F';

            badge.textContent = grade;
            badge.classList.add('grade-' + grade.charAt(0));
        }

        document.getElementById('studentSearch').addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#marksTableBody tr');

            rows.forEach(row => {
                const regNo = row.children[0].textContent.toLowerCase();
                const name = row.children[1].textContent.toLowerCase();

                row.style.display = (regNo.includes(query) || name.includes(query)) ? '' : 'none';
            });
        });
        </script>
@endpush
