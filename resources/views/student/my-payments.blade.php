<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Payments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background:#f4f6f9; font-family:'Segoe UI', sans-serif; padding:40px; }
    .container { max-width:900px; margin:auto; }
    .card-box { background:#fff; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.06); padding:28px; margin-bottom:24px; }
    .stat { text-align:center; padding:14px; }
    .stat h6 { color:#64748b; font-size:13px; text-transform:uppercase; }
    .stat h3 { color:#012147; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:10px; border-bottom:1px solid #eef0f4; text-align:center; font-size:14px; }
    th { background:#012147; color:#fff; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back to Dashboard</a>
    <h2 class="mb-4">My Payments</h2>

    @if($plan)
        <div class="card-box">
            <div class="row">
                <div class="col-md-3 stat"><h6>Total Installments</h6><h3>{{ $plan->total_installments }}</h3></div>
                <div class="col-md-3 stat"><h6>Total Fee</h6><h3>{{ number_format($plan->total_fee, 2) }}</h3></div>
                <div class="col-md-3 stat"><h6>Total Paid</h6><h3 class="text-success">{{ number_format($totalPaid, 2) }}</h3></div>
                <div class="col-md-3 stat"><h6>Balance Due</h6><h3 class="text-danger">{{ number_format($plan->total_fee - $totalPaid, 2) }}</h3></div>
            </div>
        </div>
    @else
        <div class="card-box text-center text-muted">No payment plan has been set up yet.</div>
    @endif

    <div class="card-box">
        <h5 class="mb-3">Payment History</h5>
        <table>
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
                    <tr><td colspan="5" class="text-muted py-3">No payments recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>