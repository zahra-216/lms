<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Payment</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background:#f4f6f9; font-family:'Segoe UI', sans-serif; padding:40px; }
    .container { max-width:700px; margin:auto; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.lecturer-payments.show', $payment->lecturer_id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>
    <h2 class="mb-4">Edit Payment Record</h2>

    <form method="POST" action="{{ route('admin.lecturer-payments.update', $payment->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Course(s)</label><br>
            @foreach($courses as $course)
                <div class="form-check form-check-inline">
                    <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" class="form-check-input"
                        {{ in_array($course->id, $selectedCourseIds) ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $course->name }}</label>
                </div>
            @endforeach
        </div>

        <div class="mb-3">
            <label>Type of Lecture</label>
            <select name="type_of_lecture" class="form-control" required>
                <option value="online" {{ $payment->type_of_lecture === 'online' ? 'selected' : '' }}>Online</option>
                <option value="physical" {{ $payment->type_of_lecture === 'physical' ? 'selected' : '' }}>Physical</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" value="{{ $payment->date }}" required>
        </div>
        <div class="mb-3">
            <label>Total Hours</label>
            <input type="number" step="0.01" name="total_hours" class="form-control" value="{{ $payment->total_hours }}">
        </div>
        <div class="mb-3">
            <label>Payment Type</label>
            <select name="payment_type" class="form-control" required>
                <option value="per_month" {{ $payment->payment_type === 'per_month' ? 'selected' : '' }}>Per Month</option>
                <option value="per_hour" {{ $payment->payment_type === 'per_hour' ? 'selected' : '' }}>Per Hour</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Rate Amount</label>
            <input type="number" step="0.01" name="rate_amount" class="form-control" value="{{ $payment->rate_amount }}" required>
        </div>
        <div class="mb-3">
            <label>Total Payment</label>
            <input type="number" step="0.01" name="total_payment" class="form-control" value="{{ $payment->total_payment }}" required>
        </div>
        <div class="mb-3">
            <label>Completed Payment</label>
            <input type="number" step="0.01" name="completed_payment" class="form-control" value="{{ $payment->completed_payment }}">
        </div>
        <div class="mb-3">
            <label>Paid Date</label>
            <input type="date" name="paid_date" class="form-control" value="{{ $payment->paid_date }}">
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