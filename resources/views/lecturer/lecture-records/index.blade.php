<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>My Lecture Records</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:576px){
        body { padding:20px 12px; }
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
        .top-actions{ flex-direction:column; align-items:stretch !important; }
    }
    .container { max-width:900px; margin:auto; }
    .back-btn, .action-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px;
        cursor:pointer;
    }
    .back-btn:hover, .action-btn:hover{ background:#012147; color:#fff; }
    .action-btn:disabled{ opacity:0.5; cursor:not-allowed; }
    .action-btn:disabled:hover{ background:#fff; color:#012147; }
    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:26px 30px; margin:18px 0 26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;
    }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }
    .top-actions{ display:flex; gap:10px; flex-wrap:wrap; }
    .card-box{
        background:#fff; padding:20px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06); margin-bottom:26px;
    }
    .month-picker{ display:flex; flex-wrap:wrap; gap:8px; margin-bottom:16px; }
    .month-btn{
        border:1px solid #e2e8f0; background:#fff; color:#012147; font-weight:600;
        padding:8px 14px; border-radius:10px; font-size:13px; cursor:pointer;
    }
    .month-btn:hover:not(:disabled){ background:#eef2f9; }
    .month-btn.active{ background:#012147; color:#fff; border-color:#012147; }
    .month-btn:disabled{ opacity:0.35; cursor:not-allowed; }
    .clear-btn{
        border:none; background:none; color:#ef4444; font-weight:600;
        font-size:13px; cursor:pointer; padding:8px 4px;
    }
    table.lr-table{ border-collapse:separate; border-spacing:0; }
    table.lr-table thead th{
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; white-space:nowrap;
    }
    table.lr-table thead th:first-child{ border-top-left-radius:10px; }
    table.lr-table thead th:last-child{ border-top-right-radius:10px; }
    table.lr-table tbody td{ vertical-align:middle; padding:10px; }
    table.lr-table tbody tr:nth-child(even){ background:#f8fafc; }
    table.lr-table tbody tr:hover{ background:#eef2f9; }
    .badge-pending{ background:#fef3c7; color:#92400e; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .badge-complete{ background:#d1fae5; color:#065f46; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .empty-hint{ color:#94a3b8; text-align:center; padding:30px 10px; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.dashboard') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <div>
            <h2><i class="bi bi-journal-text"></i> My Lecture Records</h2>
            <small>Your recorded and pending lectures</small>
        </div>
        <div class="top-actions">
            <a href="{{ route('lecturer.lecture-records.create') }}" class="action-btn">
                <i class="bi bi-plus-circle"></i> Add Record
            </a>
            <button type="button" id="downloadPdfBtn" class="action-btn" disabled>
                <i class="bi bi-download"></i> Download PDF
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    @endif

    <div class="card-box">
        <label class="form-label fw-semibold" style="color:#012147;">Select a month ({{ date('Y') }})</label>
        <div class="month-picker" id="monthPicker"></div>
        <button type="button" class="clear-btn" id="clearBtn">
            <i class="bi bi-x-circle"></i> Clear
        </button>

        <input type="text" id="contentSearch" class="form-control mt-3" style="display:none; border-radius:10px;" placeholder="Search by content covered...">

        <div id="tableWrap" style="display:none;" class="table-responsive mt-3">
            <table class="table lr-table align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Content Covered</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody id="recordsBody"></tbody>
            </table>
        </div>

        <div id="emptyHint" class="empty-hint">Select a month above to view your lecture records.</div>
    </div>
</div>

<script>
const currentYear = {{ date('Y') }};
const currentMonth = {{ (int) date('n') }};
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

const monthPicker = document.getElementById('monthPicker');
const tableWrap = document.getElementById('tableWrap');
const recordsBody = document.getElementById('recordsBody');
const emptyHint = document.getElementById('emptyHint');
const contentSearch = document.getElementById('contentSearch');
const downloadPdfBtn = document.getElementById('downloadPdfBtn');
const clearBtn = document.getElementById('clearBtn');

let selectedMonth = null;

months.forEach((label, idx) => {
    const monthNum = idx + 1;
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'month-btn';
    btn.textContent = label;
    btn.dataset.month = monthNum;
    if (monthNum > currentMonth) btn.disabled = true;
    btn.addEventListener('click', () => selectMonth(monthNum, btn));
    monthPicker.appendChild(btn);
});

async function selectMonth(monthNum, btn) {
    document.querySelectorAll('.month-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const monthStr = `${currentYear}-${String(monthNum).padStart(2, '0')}`;
    selectedMonth = monthStr;

    const res = await fetch(`{{ route('lecturer.lecture-records.by-month') }}?month=${monthStr}`);
    const data = await res.json();

    renderTable(data);
    downloadPdfBtn.disabled = false;
}

function renderTable(records) {
    if (!records.length) {
        recordsBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No lecture records for this month.</td></tr>';
    } else {
        recordsBody.innerHTML = records.map(r => `
            <tr data-content="${(r.content || '').toLowerCase()}">
                <td>${r.date}</td>
                <td>${r.start}</td>
                <td>${r.end}</td>
                <td>${r.content}</td>
                <td>${r.status === 'Complete' ? '<span class="badge-complete">Complete</span>' : '<span class="badge-pending">Pending</span>'}</td>
                <td>${r.remarks}</td>
            </tr>
        `).join('');
    }
    tableWrap.style.display = 'block';
    emptyHint.style.display = 'none';
    contentSearch.style.display = 'block';
    contentSearch.value = '';
}

clearBtn.addEventListener('click', () => {
    document.querySelectorAll('.month-btn').forEach(b => b.classList.remove('active'));
    selectedMonth = null;
    tableWrap.style.display = 'none';
    contentSearch.style.display = 'none';
    emptyHint.style.display = 'block';
    downloadPdfBtn.disabled = true;
});

contentSearch.addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();
    document.querySelectorAll('#recordsBody tr[data-content]').forEach(row => {
        row.style.display = row.dataset.content.includes(query) ? '' : 'none';
    });
});

downloadPdfBtn.addEventListener('click', () => {
    if (!selectedMonth) return;
    window.location.href = `{{ route('lecturer.lecture-records.pdf') }}?month=${selectedMonth}`;
});
</script>
</body>
</html>