<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject->name }} - Grades</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        .assignment-row{ flex-direction:column; align-items:flex-start !important; gap:10px; }
        .assignment-row a{ width:100%; text-align:center; }
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
    }

    .container { max-width:1000px; margin:auto; }

    .back-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .back-btn:hover{ background:#012147; color:#fff; }

    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:26px 30px; margin:18px 0 26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }
    .page-header small{ opacity:0.85; }

    .card-box{
        background:#fff; padding:20px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:26px;
    }
    .section-title{
        font-weight:700; color:#012147; margin-bottom:16px;
        display:flex; align-items:center; gap:8px; font-size:17px;
    }

    .assignment-row {
        display:flex; justify-content:space-between; align-items:center;
        padding:14px 18px; border:1px solid #e5e7eb; border-radius:12px;
        margin-bottom:10px; background:#fff; transition:0.2s;
    }
    .assignment-row:hover{ box-shadow:0 6px 16px rgba(0,0,0,0.06); }

    .btn-navy{ background:#012147; color:#fff; border:none; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }

    #studentSearch{
        border-radius:10px; padding:10px 14px; border:1px solid #e2e8f0;
    }

    table.marks-table{ border-collapse:separate; border-spacing:0; }
    table.marks-table thead th{
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap;
    }
    table.marks-table thead th:first-child{ border-top-left-radius:10px; }
    table.marks-table thead th:last-child{ border-top-right-radius:10px; }
    table.marks-table tbody td{ vertical-align:middle; padding:10px; }
    table.marks-table tbody tr:nth-child(even){ background:#f8fafc; }
    table.marks-table tbody tr:hover{ background:#eef2f9; }

    .mark-input{ border-radius:8px; }
    .mark-input:read-only{ background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; }

    .grade-badge{
        font-size:13px; padding:6px 12px; border-radius:20px; font-weight:600;
    }
    .grade-A{ background:#dcfce7; color:#15803d; }
    .grade-B{ background:#dbeafe; color:#1d4ed8; }
    .grade-C{ background:#fef9c3; color:#a16207; }
    .grade-F{ background:#fee2e2; color:#b91c1c; }
    .grade-none{ background:#e2e8f0; color:#64748b; }

    #addEditBtn i{ margin-right:6px; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <div>
            <h2><i class="bi bi-journal-check"></i> {{ $subject->code }} - {{ $subject->name }}</h2>
            <small>Grades Overview</small>
        </div>
        <i class="bi bi-clipboard-data" style="font-size:44px; opacity:0.85;"></i>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    @endif

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
                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm mark-input" readonly
                                    name="marks[{{ $student->id }}][assignment_marks]"
                                    value="{{ $sm->assignment_marks ?? '' }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm mark-input" readonly
                                    name="marks[{{ $student->id }}][mid_marks]"
                                    value="{{ $sm->mid_marks ?? '' }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm mark-input" readonly
                                    name="marks[{{ $student->id }}][practical_marks]"
                                    value="{{ $sm->practical_marks ?? '' }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm mark-input" readonly
                                    name="marks[{{ $student->id }}][final_exam_marks]"
                                    value="{{ $sm->final_exam_marks ?? '' }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm final-mark-input mark-input" readonly
                                    name="marks[{{ $student->id }}][final_marks]"
                                    value="{{ $sm->final_marks ?? '' }}"
                                    oninput="updateGradePreview(this)">
                            </td>
                            <td>
                                <span class="grade-badge {{ $sm->final_grade ? 'grade-'.$sm->final_grade : 'grade-none' }}">
                                    {{ $sm->final_grade ?? '—' }}
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
            const val = parseFloat(input.value);

            badge.classList.remove('grade-A','grade-B','grade-C','grade-F','grade-none');

            if (isNaN(val) || input.value === '') {
                badge.textContent = '—';
                badge.classList.add('grade-none');
                return;
            }

            let grade;
            if (val >= 80) grade = 'A';
            else if (val >= 60) grade = 'B';
            else if (val >= 40) grade = 'C';
            else grade = 'F';

            badge.textContent = grade;
            badge.classList.add('grade-' + grade);
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
        @else
            <p class="text-muted mb-0">No students found for this subject.</p>
        @endif
    </div>
</div>
</body>
</html>