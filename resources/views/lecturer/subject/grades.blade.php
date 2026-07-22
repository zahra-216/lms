<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Grades</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background:#f4f6f9; font-family:'Segoe UI', sans-serif; padding:40px; }
    .container { max-width:1000px; margin:auto; }
    h2 { color:#012147; }
    .assignment-row { display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border:1px solid #e5e7eb; border-radius:10px; margin-bottom:10px; background:#fff; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2 class="mb-4">{{ $subject->code }} - {{ $subject->name }} — Grades</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h5 class="mb-3">Assignments</h5>

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
                <a href="{{ route('lecturer.marks.create', $assignment->id) }}" class="btn btn-sm btn-primary">
                    Enter / Edit Marks
                </a>
            </div>
        @endforeach
    @else
        <p class="text-muted">No assignments found for this subject.</p>
    @endif


    <h5 class="mt-5 mb-3">All Marks</h5>
    <div class="mb-3">
        <input type="text" id="studentSearch" class="form-control" placeholder="Search by name or reg no...">
    </div>

    @if($students->count())
    <form method="POST" action="{{ route('lecturer.subject.marks.update', $subject->id) }}">
        @csrf
        <table class="table table-bordered bg-white align-middle">
            <thead>
                <tr>
                    <th>Reg No</th>
                    <th>Student Name</th>
                    <th>Assignment Marks</th>
                    <th>Mid Marks</th>
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
                            <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm"
                                name="marks[{{ $student->id }}][assignment_marks]"
                                value="{{ $sm->assignment_marks ?? '' }}">
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm"
                                name="marks[{{ $student->id }}][mid_marks]"
                                value="{{ $sm->mid_marks ?? '' }}">
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm"
                                name="marks[{{ $student->id }}][final_exam_marks]"
                                value="{{ $sm->final_exam_marks ?? '' }}">
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm final-mark-input"
                                name="marks[{{ $student->id }}][final_marks]"
                                value="{{ $sm->final_marks ?? '' }}"
                                oninput="updateGradePreview(this)">
                        </td>
                        <td>
                            <span class="badge bg-primary grade-badge">{{ $sm->final_grade ?? '—' }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Save Marks</button>
    </form>

    <script>
    function updateGradePreview(input) {
        const row = input.closest('tr');
        const badge = row.querySelector('.grade-badge');
        const val = parseFloat(input.value);

        if (isNaN(val) || input.value === '') {
            badge.textContent = '—';
            return;
        }

        let grade;
        if (val >= 80) grade = 'A';
        else if (val >= 60) grade = 'B';
        else if (val >= 40) grade = 'C';
        else grade = 'F';

        badge.textContent = grade;
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
        <p class="text-muted">No students found for this subject.</p>
    @endif
</div>
</body>
</html>