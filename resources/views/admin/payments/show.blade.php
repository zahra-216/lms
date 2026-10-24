@extends('layouts.admin')
@section('title')
{{ $student->name }} - Payments
@endsection
@section('back', route('admin.payments.index'))
@push('styles')
<style>
.page-header .meta { font-size:13.5px; opacity:0.9;
}
.page-header .meta b { font-weight:600;
}
.btn-green { background:#10b981; color:#fff; border:none; padding:10px 20px; border-radius:10px; font-weight:600;
}
.btn-green:hover { background:#059669; color:#fff;
}
.summary-pill { display:inline-flex; gap:20px; background:#eef2f9; border-radius:12px; padding:12px 18px; font-size:14px; margin-bottom:20px; flex-wrap:wrap;
}
.summary-pill b { color:#012147;
}
table.pay-table { border-collapse:separate; border-spacing:0; width:100%;
}
table.pay-table thead th { background:#012147; color:#fff; font-weight:600; border:none; padding:12px 14px; white-space:nowrap;
}
table.pay-table thead th:first-child { border-top-left-radius:10px;
}
table.pay-table thead th:last-child { border-top-right-radius:10px;
}
table.pay-table tbody td { vertical-align:middle; padding:12px 14px;
}
table.pay-table tbody tr:nth-child(even) { background:#f8fafc;
}
.action-btn { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:8px; text-decoration:none; font-size:13px; font-weight:600; border:none;
}
.edit-btn { background:#eef2f9; color:#012147;
}
.edit-btn:hover { background:#012147; color:#fff;
}
.delete-btn { background:#fee2e2; color:#991b1b;
}
.delete-btn:hover { background:#dc2626; color:#fff;
}
@media (max-width:576px) {
table.pay-table { display:block; overflow-x:auto; white-space:nowrap;
}
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h2>{{ $student->name }} ({{ $student->registration_no }})</h2>
        <div class="meta">
            Intake: <b>{{ $student->intake }}</b> &nbsp;|&nbsp;
            Course: <b>{{ $student->course->name ?? '-' }}</b> &nbsp;|&nbsp;
            Level: <b>{{ $student->level->name ?? '-' }}</b> &nbsp;|&nbsp;
            Faculty: <b>{{ $student->course->faculty->name ?? '-' }}</b>
        </div>
    </div>

    

    <div class="card-box">
        <div class="section-title"><i class="bi bi-receipt"></i> Payment Plan</div>
        <form method="POST" action="{{ route('admin.payments.plan.store', $student->id) }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-3">
                <label class="form-label">Total Installments</label>
                <input type="number" name="total_installments" class="form-control" value="{{ $plan->total_installments ?? '' }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Total Fee</label>
                <input type="number" step="0.01" name="total_fee" class="form-control" value="{{ $plan->total_fee ?? '' }}" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn-navy">Save Plan</button>
            </div>
        </form>

        @if($plan)
            <div class="summary-pill mt-3">
                <span>Total Paid: <b>{{ number_format($totalPaid, 2) }}</b></span>
                <span>Payment Due: <b>{{ number_format($plan->total_fee - $totalPaid, 2) }}</b></span>
            </div>
        @endif
    </div>

    <div class="card-box">
        <div class="section-title"><i class="bi bi-plus-circle"></i> Add Payment</div>
        <form method="POST" action="{{ route('admin.payments.payment.store', $student->id) }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="type_of_payment" class="form-select" required>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Invoice No</label>
                <input type="text" name="invoice_no" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Remarks</label>
                <input type="text" name="remarks" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-green w-100">Add</button>
            </div>
        </form>
    </div>

    <div class="card-box">
        <div class="section-title"><i class="bi bi-clock-history"></i> Payment History</div>
        <div class="table-responsive">
        <table class="table pay-table align-middle">
            <thead>
                <tr>
                    <th>Type</th><th>Amount</th><th>Date</th><th>Invoice No</th><th>Remarks</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->type_of_payment)) }}</td>
                        <td>{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->date }}</td>
                        <td>{{ $payment->invoice_no ?? '-' }}</td>
                        <td>{{ $payment->remarks ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.payments.payment.edit', $payment->id) }}" class="action-btn edit-btn">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.payments.payment.destroy', $payment->id) }}" onsubmit="return confirm('Delete this payment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No payments recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
