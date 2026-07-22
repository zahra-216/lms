<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject->name }} - Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6f9; font-family:'Segoe UI', sans-serif; padding:40px; }
    .container { max-width:900px; margin:auto; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.subject.show', $subject->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2 class="mb-4">{{ $subject->code }} - {{ $subject->name }} — Attendance</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('lecturer.subject.attendance', $subject->id) }}" class="mb-4 d-flex gap-2 align-items-end">
        <div>
            <label>Select Date</label>
            <input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}" class="form-control" onchange="this.form.submit()">
        </div>
    </form>

    <form method="POST" action="{{ route('lecturer.subject.attendance.store', $subject->id) }}">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="mb-4">
            <div class="input-group shadow-sm" style="max-width:400px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="studentSearch" class="form-control border-start-0 ps-0"
                    placeholder="Search by name or reg no..."
                    style="box-shadow:none;">
            </div>
        </div>

        <table class="table table-bordered bg-white align-middle">
            <thead>
                <tr>
                    <th>Reg No</th>
                    <th>Student Name</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Not Marked</th>
                </tr>
            </thead>
            <tbody id="attendanceTableBody">
                @foreach($students as $student)
                    <tr>
                        <td>{{ $student->registration_no }}</td>
                        <td>{{ $student->name }}</td>
                        <td>
                            <input type="radio" name="status[{{ $student->id }}]" value="present">
                        </td>
                        <td>
                            <input type="radio" name="status[{{ $student->id }}]" value="absent">
                        </td>
                        <td>
                            <input type="radio" name="status[{{ $student->id }}]" value="" checked>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Save Attendance</button>
    </form>
</div>

<script>
document.getElementById('studentSearch').addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#attendanceTableBody tr');

    rows.forEach(row => {
        const regNo = row.children[0].textContent.toLowerCase();
        const name = row.children[1].textContent.toLowerCase();
        row.style.display = (regNo.includes(query) || name.includes(query)) ? '' : 'none';
    });
});
</script>

</body>
</html>