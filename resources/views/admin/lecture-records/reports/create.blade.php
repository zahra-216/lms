<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Generate Lecture Report</title>
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
    .form-control{ border-radius:10px; border:1px solid #e2e8f0; padding:10px 14px; }
    .form-control:focus{ border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }
    .btn-navy{ background:#012147; color:#fff; border:none; padding:12px; font-weight:600; border-radius:10px; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }

    .lecturer-search-box{ position:relative; }
    .lecturer-results{
        position:absolute; top:100%; left:0; right:0; background:#fff;
        border:1px solid #e2e8f0; border-radius:10px; margin-top:4px;
        max-height:200px; overflow-y:auto; box-shadow:0 8px 20px rgba(0,0,0,0.08);
        z-index:20; display:none;
    }
    .lecturer-results.show{ display:block; }
    .lecturer-result-item{ padding:10px 14px; cursor:pointer; font-size:14px; }
    .lecturer-result-item:hover{ background:#eef2f9; }
    .lecturer-selected{
        margin-top:8px; padding:8px 14px; background:#eef2f9; border-radius:10px;
        display:flex; justify-content:space-between; align-items:center; font-size:14px; color:#012147;
    }
    .lecturer-selected button{ border:none; background:none; color:#ef4444; font-weight:700; cursor:pointer; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.lecture-records.reports.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <h3><i class="bi bi-file-earmark-pdf"></i> Generate Lecture Report</h3>
        <small>Pick a lecturer and a month</small>
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
        <form action="{{ route('admin.lecture-records.reports.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Lecturer</label>
                <div class="lecturer-search-box">
                    <input type="text" id="lecturerSearch" class="form-control" placeholder="Search lecturer by name or username...">
                    <div id="lecturerResults" class="lecturer-results">
                        @foreach($lecturers as $lecturer)
                            <div class="lecturer-result-item"
                                 data-id="{{ $lecturer->id }}"
                                 data-name="{{ $lecturer->name }}"
                                 data-search="{{ strtolower($lecturer->name.' '.$lecturer->username) }}">
                                {{ $lecturer->name }} <span class="text-muted">({{ $lecturer->username }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div id="lecturerSelectedBox" style="display:none;" class="lecturer-selected">
                    <span id="lecturerSelectedName"></span>
                    <button type="button" onclick="clearLecturer()">&times;</button>
                </div>
                <input type="hidden" name="lecturer_id" id="lecturer_id" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Month</label>
                <input type="month" name="month" class="form-control" value="{{ old('month') }}" required>
            </div>

            <button class="btn btn-navy w-100">Generate Report</button>
        </form>
    </div>
</div>

<script>
const searchInput = document.getElementById('lecturerSearch');
const resultsBox = document.getElementById('lecturerResults');
const lecturerIdInput = document.getElementById('lecturer_id');
const selectedBox = document.getElementById('lecturerSelectedBox');
const selectedName = document.getElementById('lecturerSelectedName');

searchInput.addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();
    const items = resultsBox.querySelectorAll('.lecturer-result-item');
    let anyVisible = false;

    items.forEach(item => {
        const match = item.dataset.search.includes(query);
        item.style.display = match ? 'block' : 'none';
        if (match) anyVisible = true;
    });

    resultsBox.classList.toggle('show', query.length > 0 && anyVisible);
});

resultsBox.addEventListener('click', function (e) {
    const item = e.target.closest('.lecturer-result-item');
    if (!item) return;

    lecturerIdInput.value = item.dataset.id;
    selectedName.textContent = item.dataset.name;
    selectedBox.style.display = 'flex';
    searchInput.value = '';
    resultsBox.classList.remove('show');
});

function clearLecturer() {
    lecturerIdInput.value = '';
    selectedBox.style.display = 'none';
}

document.addEventListener('click', function (e) {
    if (!e.target.closest('.lecturer-search-box')) {
        resultsBox.classList.remove('show');
    }
});
</script>
</body>
</html>