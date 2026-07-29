<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Select Subject - Lecture Records</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){ body { padding:20px 12px; } }
    .container { max-width:600px; margin:auto; }
    .back-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .back-btn:hover{ background:#012147; color:#fff; }
    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:24px 28px; margin:18px 0 26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h3{ margin:0; font-weight:700; font-size:20px; }
    .card-box{
        background:#fff; padding:26px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06);
    }
    .form-label{ font-weight:600; color:#012147; font-size:14px; }
    .form-select{ border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px; }
    .form-select:focus{ border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }
    .btn-navy{ background:#012147; color:#fff; border:none; padding:12px; font-weight:600; border-radius:10px; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }
    .btn-navy:disabled{ opacity:0.5; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.dashboard') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <h3><i class="bi bi-journal-text"></i> Lecture Records</h3>
        <small>Select the module to continue</small>
    </div>

    <div class="card-box">
        <div class="mb-3">
            <label class="form-label">Faculty</label>
            <select id="faculty" class="form-select">
                <option value="">-- Select Faculty --</option>
                @foreach($faculties as $faculty)
                    <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Course</label>
            <select id="course" class="form-select" disabled>
                <option value="">-- Select Course --</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Level</label>
            <select id="level" class="form-select" disabled>
                <option value="">-- Select Level --</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Semester</label>
            <select id="semester" class="form-select" disabled>
                <option value="">-- Select Semester --</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label">Module</label>
            <select id="subject" class="form-select" disabled>
                <option value="">-- Select Module --</option>
            </select>
        </div>

        <button id="goBtn" class="btn btn-navy w-100" disabled>Continue</button>
    </div>
</div>

<script>
const facultySel = document.getElementById('faculty');
const courseSel = document.getElementById('course');
const levelSel = document.getElementById('level');
const semesterSel = document.getElementById('semester');
const subjectSel = document.getElementById('subject');
const goBtn = document.getElementById('goBtn');

function resetSelect(sel, placeholder) {
    sel.innerHTML = `<option value="">${placeholder}</option>`;
    sel.disabled = true;
}

facultySel.addEventListener('change', async function () {
    resetSelect(courseSel, '-- Select Course --');
    resetSelect(levelSel, '-- Select Level --');
    resetSelect(semesterSel, '-- Select Semester --');
    resetSelect(subjectSel, '-- Select Module --');
    goBtn.disabled = true;

    if (!this.value) return;

    const res = await fetch(`/lecturer/lecture-records/get-courses/${this.value}`);
    const data = await res.json();
    data.forEach(c => courseSel.innerHTML += `<option value="${c.id}">${c.name}</option>`);
    courseSel.disabled = false;
});

courseSel.addEventListener('change', async function () {
    resetSelect(levelSel, '-- Select Level --');
    resetSelect(semesterSel, '-- Select Semester --');
    resetSelect(subjectSel, '-- Select Module --');
    goBtn.disabled = true;

    if (!this.value) return;

    const res = await fetch(`/lecturer/lecture-records/get-levels/${this.value}`);
    const data = await res.json();
    data.forEach(l => levelSel.innerHTML += `<option value="${l.id}">${l.name}</option>`);
    levelSel.disabled = false;
});

levelSel.addEventListener('change', async function () {
    resetSelect(semesterSel, '-- Select Semester --');
    resetSelect(subjectSel, '-- Select Module --');
    goBtn.disabled = true;

    if (!this.value) return;

    const res = await fetch(`/lecturer/lecture-records/get-semesters/${this.value}`);
    const data = await res.json();
    data.forEach(s => semesterSel.innerHTML += `<option value="${s.id}">${s.name}</option>`);
    semesterSel.disabled = false;
});

semesterSel.addEventListener('change', async function () {
    resetSelect(subjectSel, '-- Select Module --');
    goBtn.disabled = true;

    if (!this.value) return;

    const res = await fetch(`/lecturer/lecture-records/get-subjects/${this.value}`);
    const data = await res.json();
    data.forEach(s => subjectSel.innerHTML += `<option value="${s.id}">${s.code} - ${s.name}</option>`);
    subjectSel.disabled = false;
});

subjectSel.addEventListener('change', function () {
    goBtn.disabled = !this.value;
});

goBtn.addEventListener('click', function () {
    if (subjectSel.value) {
        window.location.href = `/lecturer/subject/${subjectSel.value}/lecture-records`;
    }
});
</script>
</body>
</html>