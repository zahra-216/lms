<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>{{ $student->name }} - Payments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background:#f4f6f9; font-family:'Segoe UI', sans-serif; padding:40px; }
    .container { max-width:1000px; margin:auto; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h2 class="mb-3">{{ $student->name }} ({{ $student->registration_no }})</h2>
    <p>
        Intake: <b>{{ $student->intake }}</b> |
        Course: <b>{{ $student->course->name ?? '—' }}</b> |
        Level: <b>{{ $student->level->name ?? '—' }}</b> |
        Faculty: <b>{{ $student->course->faculty->name ?? '—' }}</b>
    </p>

    <h5 class="mt-4 mb-2">Payment Plan</h5>
    <form method="POST" action="{{ route('admin.payments.plan.store', $student->id) }}" class="row g-2 align-items-end mb-3">
        @csrf
        <div class="col-md-3">
            <label>Total Installments</label>
            <input type="number" name="total_installments" class="form-control" value="{{ $plan->total_installments ?? '' }}" required>
        </div>
        <div class="col-md-3">
            <label>Total Fee</label>
            <input type="number" step="0.01" name="total_fee" class="form-control" value="{{ $plan->total_fee ?? '' }}" required>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Save Plan</button>
        </div>
    </form>

    @if($plan)
        <p>
            Total Paid: <b>{{ number_format($totalPaid, 2) }}</b> |
            Payment Due: <b>{{ number_format($plan->total_fee - $totalPaid, 2) }}</b>
        </p>
    @endif

    <h5 class="mt-4 mb-2">Add Payment</h5>
    <form method="POST" action="{{ route('admin.payments.payment.store', $student->id) }}" class="row g-2 align-items-end mb-4">
        @csrf
        <div class="col-md-2">
            <label>Type</label>
            <select name="type_of_payment" class="form-control" required>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="bank_transfer">Bank Transfer</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label>Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label>Invoice No</label>
            <input type="text" name="invoice_no" class="form-control">
        </div>
        <div class="col-md-2">
            <label>Remarks</label>
            <input type="text" name="remarks" class="form-control">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success">Add</button>
        </div>
    </form>

    <h5 class="mb-2">Payment History</h5>
    <table class="table table-bordered bg-white">
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
                    <td>{{ $payment->invoice_no ?? '—' }}</td>
                    <td>{{ $payment->remarks ?? '—' }}</td>
                    <td>
                        <a href="{{ route('admin.payments.payment.edit', $payment->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" action="{{ route('admin.payments.payment.destroy', $payment->id) }}" style="display:inline;" onsubmit="return confirm('Delete this payment?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No payments recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>