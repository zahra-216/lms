<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>My Payments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }

    @media (max-width:576px){
        body { padding:20px 12px; }
    }

    .container { max-width:1100px; margin:auto; }

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
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }

    .card-box{
        background:#fff; padding:20px; border-radius:14px;
        box-shadow:0 6px 20px rgba(0,0,0,0.06);
    }

    table.payments-table { width:100%; border-collapse:separate; border-spacing:0; background:#fff; }
    table.payments-table th, table.payments-table td { padding:12px 10px; text-align:center; font-size:14px; }
    table.payments-table thead th { background:#012147; color:#fff; font-weight:600; white-space:nowrap; }
    table.payments-table thead th:first-child{ border-top-left-radius:10px; }
    table.payments-table thead th:last-child{ border-top-right-radius:10px; }
    table.payments-table tbody tr:nth-child(even){ background:#f8fafc; }
    table.payments-table tbody tr:hover{ background:#eef2f9; }
    table.payments-table tbody td{ border-bottom:1px solid #eef0f4; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.dashboard') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>

    <div class="page-header">
        <h2><i class="bi bi-wallet2"></i> My Payments</h2>
    </div>

    <div class="card-box">
        <div class="table-responsive">
        <table class="payments-table">
            <thead>
                <tr>
                    <th>Course(s)</th><th>Type</th><th>Date</th><th>Hours</th>
                    <th>Pay Type</th><th>Rate</th><th>Total</th><th>Completed</th>
                    <th>Due</th><th>Paid Date</th><th>Invoice</th><th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->courses->pluck('name')->implode(', ') }}</td>
                        <td>{{ ucfirst($payment->type_of_lecture) }}</td>
                        <td>{{ $payment->date }}</td>
                        <td>{{ $payment->total_hours ?? '—' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</td>
                        <td>{{ number_format($payment->rate_amount, 2) }}</td>
                        <td>{{ number_format($payment->total_payment, 2) }}</td>
                        <td>{{ number_format($payment->completed_payment, 2) }}</td>
                        <td>{{ number_format($payment->payment_due, 2) }}</td>
                        <td>{{ $payment->paid_date ?? '—' }}</td>
                        <td>{{ $payment->invoice_no ?? '—' }}</td>
                        <td>{{ $payment->remarks ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="text-muted py-3">No payment records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
</body>
</html>