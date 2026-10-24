@extends('layouts.admin')
@section('title')
Edit Payment
@endsection
@section('back', route('admin.payments.show', $payment->student_id))
@section('content')
<div class="container">
    

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Payment</h3>
    </div>

    <div class="card-box">
        <form method="POST" action="{{ route('admin.payments.payment.update', $payment->id) }}">
            @csrf
            @method('PUT')

            <label class="form-label">Type</label>
            <select name="type_of_payment" class="form-select" required>
                <option value="cash" {{ $payment->type_of_payment === 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="card" {{ $payment->type_of_payment === 'card' ? 'selected' : '' }}>Card</option>
                <option value="bank_transfer" {{ $payment->type_of_payment === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
            </select>

            <label class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $payment->amount }}" required>

            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="{{ $payment->date }}" required>

            <label class="form-label">Invoice No</label>
            <input type="text" name="invoice_no" class="form-control" value="{{ $payment->invoice_no }}">

            <label class="form-label">Remarks</label>
            <input type="text" name="remarks" class="form-control" value="{{ $payment->remarks }}">

            <button type="submit" class="btn-navy">Update Payment</button>
        </form>
    </div>
</div>
@endsection
