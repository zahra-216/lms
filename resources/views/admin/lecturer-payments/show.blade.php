<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $lecturer->name }} - Payments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:1200px; margin:auto; }

    .back-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover { background:#012147; color:#fff; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:26px 30px; margin-bottom:22px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h2 { margin:0 0 4px; font-weight:700; font-size:22px; }
    .page-header small { opacity:0.85; font-size:14px; }

    .card-box {
        background:#fff; border-radius:16px; box-shadow:0 8px 26px rgba(0,0,0,0.06);
        padding:26px; margin-bottom:26px;
    }
    .card-box h5 {
        color:#012147; font-weight:700; margin-bottom:20px;
        display:flex; align-items:center; gap:8px; font-size:16px;
    }

    .course-pills { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:24px; }
    .course-pill {
        display:inline-flex; align-items:center; gap:6px; background:#f1f5f9;
        border:1px solid #e2e8f0; border-radius:20px; padding:6px 14px;
        cursor:pointer; font-size:13px; transition:0.15s;
    }
    .course-pill:hover { background:#e2e8f0; }
    .course-pill input { display:none; }
    .course-pill:has(input:checked) { background:#012147; color:#fff; border-color:#012147; }

    .form-label { font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.03em; margin-bottom:4px; }
    .form-control, .form-select { border-radius:10px; border:1px solid #e2e8f0; padding:9px 12px; }
    .form-control:focus, .form-select:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); }

    .btn-add { background:#012147; color:#fff; border:none; border-radius:10px; padding:11px 26px; font-weight:600; }
    .btn-add:hover { background:#0b2d5a; color:#fff; }

    table.history { width:100%; border-collapse:separate; border-spacing:0; }
    table.history thead th {
        background:#012147; color:#fff; padding:12px 10px; font-size:13px; font-weight:600;
        text-align:center; white-space:nowrap;
    }
    table.history thead th:first-child { border-top-left-radius:10px; }
    table.history thead th:last-child { border-top-right-radius:10px; }
    table.history tbody td { padding:10px; text-align:center; font-size:13px; vertical-align:middle; }
    table.history tbody tr:nth-child(even) { background:#f8fafc; }
    table.history tbody tr:hover { background:#eef2f9; }

    .badge-lecture { padding:5px 10px; border-radius:6px; font-size:12px; font-weight:600; }
    .badge-online { background:#dbeafe; color:#1d4ed8; }
    .badge-physical { background:#fef3c7; color:#92400e; }

    .due-positive { color:#dc2626; font-weight:700; }
    .due-zero { color:#16a34a; font-weight:700; }

    .action-btn { display:inline-flex; align-items:center; gap:4px; border:none; border-radius:8px; padding:6px 12px; font-size:12px; font-weight:600; margin:2px; text-decoration:none; }
    .btn-edit { background:#eef2f9; color:#012147; }
    .btn-edit:hover { background:#012147; color:#fff; }
    .btn-delete { background:#fee2e2; color:#991b1b; }
    .btn-delete:hover { background:#dc2626; color:#fff; }

    @media (max-width:576px){
        body { padding:20px 12px; }
        table.history { display:block; overflow-x:auto; white-space:nowrap; }
    }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.dashboard') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h2>{{ $lecturer->name }}</h2>
        <small>{{ $lecturer->email }}</small>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <div class="card-box">
        <h5><i class="bi bi-plus-circle"></i> Add Payment Record</h5>

        <form method="POST" action="{{ route('admin.lecturer-payments.store', $lecturer->id) }}">
            @csrf

            <label class="form-label">Course(s)</label>
            <div class="course-pills">
                @foreach($courses as $course)
                    <label class="course-pill">
                        <input type="checkbox" name="course_ids[]" value="{{ $course->id }}">
                        {{ $course->name }}
                    </label>
                @endforeach
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-2">
                    <label class="form-label">Type of Lecture</label>
                    <select name="type_of_lecture" class="form-select" required>
                        <option value="online">Online</option>
                        <option value="physical">Physical</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total Hours</label>
                    <input type="number" step="0.01" name="total_hours" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Payment Type</label>
                    <select name="payment_type" class="form-select" required>
                        <option value="per_month">Per Month</option>
                        <option value="per_hour">Per Hour</option>
                        <option value="per_day">Per Day</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Rate Amount</label>
                    <input type="number" step="0.01" name="rate_amount" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total Payment</label>
                    <input type="number" step="0.01" name="total_payment" class="form-control" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Completed Payment</label>
                    <input type="number" step="0.01" name="completed_payment" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Paid Date</label>
                    <input type="date" name="paid_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Invoice No</label>
                    <input type="text" name="invoice_no" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Remarks</label>
                    <input type="text" name="remarks" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn-add">
                <i class="bi bi-check-circle"></i> Add Payment
            </button>
        </form>
    </div>

    <div class="card-box">
        <h5><i class="bi bi-clock-history"></i> Payment History</h5>

        <div class="table-responsive">
            <table class="history">
                <thead>
                    <tr>
                        <th>Course(s)</th><th>Type</th><th>Date</th><th>Hours</th>
                        <th>Pay Type</th><th>Rate</th><th>Total</th><th>Completed</th>
                        <th>Due</th><th>Paid Date</th><th>Invoice</th><th>Remarks</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->courses->pluck('name')->implode(', ') }}</td>
                            <td>
                                <span class="badge-lecture {{ $payment->type_of_lecture === 'online' ? 'badge-online' : 'badge-physical' }}">
                                    {{ ucfirst($payment->type_of_lecture) }}
                                </span>
                            </td>
                            <td>{{ $payment->date }}</td>
                            <td>{{ $payment->total_hours ?? '—' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</td>
                            <td>{{ number_format($payment->rate_amount, 2) }}</td>
                            <td>{{ number_format($payment->total_payment, 2) }}</td>
                            <td>{{ number_format($payment->completed_payment, 2) }}</td>
                            <td class="{{ $payment->payment_due > 0 ? 'due-positive' : 'due-zero' }}">
                                {{ number_format($payment->payment_due, 2) }}
                            </td>
                            <td>{{ $payment->paid_date ?? '—' }}</td>
                            <td>{{ $payment->invoice_no ?? '—' }}</td>
                            <td>{{ $payment->remarks ?? '—' }}</td>
                            <td>
                                <a href="{{ route('admin.lecturer-payments.edit', $payment->id) }}" class="action-btn btn-edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.lecturer-payments.destroy', $payment->id) }}" style="display:inline;" onsubmit="return confirm('Delete this record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="13" class="text-center text-muted py-4">No payment records yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>