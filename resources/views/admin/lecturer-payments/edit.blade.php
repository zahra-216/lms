@extends('layouts.admin')
@section('title')
Edit Payment
@endsection
@push('styles')
<style>
.course-check-row { display:flex; flex-wrap:wrap; gap:14px; margin-bottom:18px;
}
.course-check-row label { font-size:14px;
}
</style>
@endpush
@section('content')
<div class="container">
    <a href="{{ route('admin.lecturer-payments.show', $payment->lecturer_id) }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h3><i class="bi bi-pencil-square"></i> Edit Payment Record</h3>
    </div>

    <div class="card-box">
        <form method="POST" action="{{ route('admin.lecturer-payments.update', $payment->id) }}">
            @csrf
            @method('PUT')

            <label class="form-label">Course(s)</label>
            <div class="course-check-row">
                @foreach($courses as $course)
                    <div class="form-check">
                        <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" class="form-check-input"
                            {{ in_array($course->id, $selectedCourseIds) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $course->name }}</label>
                    </div>
                @endforeach
            </div>

            <label class="form-label">Type of Lecture</label>
            <select name="type_of_lecture" class="form-select" required>
                <option value="online" {{ $payment->type_of_lecture === 'online' ? 'selected' : '' }}>Online</option>
                <option value="physical" {{ $payment->type_of_lecture === 'physical' ? 'selected' : '' }}>Physical</option>
            </select>

            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="{{ $payment->date }}" required>

            <label class="form-label">Total Hours</label>
            <input type="number" step="0.01" name="total_hours" class="form-control" value="{{ $payment->total_hours }}">

            <label class="form-label">Payment Type</label>
            <select name="payment_type" class="form-select" required>
                <option value="per_month" {{ $payment->payment_type === 'per_month' ? 'selected' : '' }}>Per Month</option>
                <option value="per_hour" {{ $payment->payment_type === 'per_hour' ? 'selected' : '' }}>Per Hour</option>
                <option value="per_day" {{ $payment->payment_type === 'per_day' ? 'selected' : '' }}>Per Day</option>
            </select>

            <label class="form-label">Rate Amount</label>
            <input type="number" step="0.01" name="rate_amount" class="form-control" value="{{ $payment->rate_amount }}" required>

            <label class="form-label">Total Payment</label>
            <input type="number" step="0.01" name="total_payment" class="form-control" value="{{ $payment->total_payment }}" required>

            <label class="form-label">Completed Payment</label>
            <input type="number" step="0.01" name="completed_payment" class="form-control" value="{{ $payment->completed_payment }}">

            <label class="form-label">Paid Date</label>
            <input type="date" name="paid_date" class="form-control" value="{{ $payment->paid_date }}">

            <label class="form-label">Invoice No</label>
            <input type="text" name="invoice_no" class="form-control" value="{{ $payment->invoice_no }}">

            <label class="form-label">Remarks</label>
            <input type="text" name="remarks" class="form-control" value="{{ $payment->remarks }}">

            <button type="submit" class="btn-navy">Update Payment</button>
        </form>
    </div>
</div>
@endsection
