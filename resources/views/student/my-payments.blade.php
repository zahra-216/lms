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
        .page-header{ flex-direction:column; align-items:flex-start !important; gap:14px; }
        .stat{ padding:10px 0; }
    }

    .container { max-width:900px; margin:auto; }

    .back-btn{
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover{ background:#012147; color:#fff; }

    .page-header{
        background:linear-gradient(120deg,#012147,#1e3a6e);
        color:#fff; border-radius:18px; padding:26px 30px; margin-bottom:26px;
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

    .stat { text-align:center; padding:14px; }
    .stat h6 { color:#64748b; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; }
    .stat h3 { color:#012147; font-weight:700; margin:0; }
    .stat h3.text-success{ color:#15803d !important; }
    .stat h3.text-danger{ color:#b91c1c !important; }

    table.marks-table{ border-collapse:separate; border-spacing:0; width:100%; }
    table.marks-table thead th{
        background:#012147; color:#fff; font-weight:600; border:none;
        padding:12px 10px; text-align:center; white-space:nowrap;
    }
    table.marks-table thead th:first-child{ border-top-left-radius:10px; }
    table.marks-table thead th:last-child{ border-top-right-radius:10px; }
    table.marks-table tbody td{ vertical-align:middle; padding:10px; text-align:center; font-size:14px; }
    table.marks-table tbody tr:nth-child(even){ background:#f8fafc; }
    table.marks-table tbody tr:hover{ background:#eef2f9; }

    .empty-state{ text-align:center; color:#94a3b8; padding:20px 0; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('dashboard') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="page-header">
        <div>
            <h2><i class="bi bi-wallet2"></i> My Payments</h2>
            <small>Track your fee plan and payment history</small>
        </div>
        <i class="bi bi-cash-coin" style="font-size:44px; opacity:0.85;"></i>
    </div>

    @if($plan)
        <div class="card-box">
            <div class="section-title"><i class="bi bi-pie-chart"></i> Payment Summary</div>
            <div class="row">
                <div class="col-md-3 col-6 stat"><h6>Total Installments</h6><h3>{{ $plan->total_installments }}</h3></div>
                <div class="col-md-3 col-6 stat"><h6>Total Fee</h6><h3>{{ number_format($plan->total_fee, 2) }}</h3></div>
                <div class="col-md-3 col-6 stat"><h6>Total Paid</h6><h3 class="text-success">{{ number_format($totalPaid, 2) }}</h3></div>
                <div class="col-md-3 col-6 stat"><h6>Balance Due</h6><h3 class="text-danger">{{ number_format($plan->total_fee - $totalPaid, 2) }}</h3></div>
            </div>
        </div>
    @else
        <div class="card-box">
            <div class="empty-state">No payment plan has been set up yet.</div>
        </div>
    @endif

    <div class="card-box">
        <div class="section-title"><i class="bi bi-receipt"></i> Payment History</div>
        <div class="table-responsive">
        <table class="table marks-table align-middle mb-0">
            <thead>
                <tr><th>Type</th><th>Amount</th><th>Date</th><th>Invoice No</th><th>Remarks</th></tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->type_of_payment)) }}</td>
                        <td>{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->date }}</td>
                        <td>{{ $payment->invoice_no ?? '—' }}</td>
                        <td>{{ $payment->remarks ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No payments recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
</body>
</html>