@extends('layouts.lecturer')
@section('title')
My Payments
@endsection
@push('styles')
<style>
table.payments-table { width:100%; border-collapse:separate; border-spacing:0; background:#fff;
}
table.payments-table th, table.payments-table td { padding:12px 10px; text-align:center; font-size:14px;
}
table.payments-table thead th { background:#012147; color:#fff; font-weight:600; white-space:nowrap;
}
table.payments-table thead th:first-child { border-top-left-radius:10px;
}
table.payments-table thead th:last-child { border-top-right-radius:10px;
}
table.payments-table tbody tr:nth-child(even) { background:#f8fafc;
}
table.payments-table tbody tr:hover { background:#eef2f9;
}
table.payments-table tbody td { border-bottom:1px solid #eef0f4;
}
</style>
@endpush
@section('content')
<div class="container">
    

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
                        <td>{{ $payment->total_hours ?? '-' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</td>
                        <td>{{ number_format($payment->rate_amount, 2) }}</td>
                        <td>{{ number_format($payment->total_payment, 2) }}</td>
                        <td>{{ number_format($payment->completed_payment, 2) }}</td>
                        <td>{{ number_format($payment->payment_due, 2) }}</td>
                        <td>{{ $payment->paid_date ?? '-' }}</td>
                        <td>{{ $payment->invoice_no ?? '-' }}</td>
                        <td>{{ $payment->remarks ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="text-muted py-3">No payment records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
