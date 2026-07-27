<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Payments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background:#f4f6f9; font-family:'Segoe UI', sans-serif; padding:40px; }
    .container { max-width:1100px; margin:auto; }
    table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; }
    th, td { padding:10px; border-bottom:1px solid #eef0f4; text-align:center; font-size:14px; }
    th { background:#012147; color:#fff; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('lecturer.dashboard') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back to Dashboard</a>
    <h2 class="mb-4">My Payments</h2>

    <table>
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
</body>
</html>