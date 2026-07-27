<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Payment</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background:#f4f6f9; font-family:'Segoe UI', sans-serif; padding:40px; }
    .container { max-width:600px; margin:auto; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.payments.show', $payment->student_id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2 class="mb-4">Edit Payment</h2>

    <form method="POST" action="{{ route('admin.payments.payment.update', $payment->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Type</label>
            <select name="type_of_payment" class="form-control" required>
                <option value="cash" {{ $payment->type_of_payment === 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="card" {{ $payment->type_of_payment === 'card' ? 'selected' : '' }}>Card</option>
                <option value="bank_transfer" {{ $payment->type_of_payment === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $payment->amount }}" required>
        </div>
        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" value="{{ $payment->date }}" required>
        </div>
        <div class="mb-3">
            <label>Invoice No</label>
            <input type="text" name="invoice_no" class="form-control" value="{{ $payment->invoice_no }}">
        </div>
        <div class="mb-3">
            <label>Remarks</label>
            <input type="text" name="remarks" class="form-control" value="{{ $payment->remarks }}">
        </div>

        <button type="submit" class="btn btn-primary">Update Payment</button>
    </form>
</div>
</body>
</html>