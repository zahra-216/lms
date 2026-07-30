<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Payment | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    * { box-sizing:border-box; }
    body { font-family:'Segoe UI', sans-serif; background:#f4f6fb; margin:0; padding:40px 20px; color:#012147; }
    .container { max-width:600px; margin:auto; }

    .back-btn {
        border:none; background:#fff; color:#012147; font-weight:600;
        padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06);
        text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px;
    }
    .back-btn:hover { background:#012147; color:#fff; }

    .page-header {
        background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff;
        border-radius:18px; padding:24px 28px; margin-bottom:26px;
        box-shadow:0 10px 30px rgba(1,33,71,0.25);
    }
    .page-header h3 { margin:0; font-weight:700; font-size:20px; }

    .card-box { background:#fff; border-radius:16px; padding:28px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }

    .form-label { font-weight:600; color:#012147; font-size:14px; margin-bottom:6px; display:block; }
    .form-control, .form-select { border-radius:10px; border:1px solid #e2e8f0; padding:11px 14px; width:100%; margin-bottom:16px; }
    .form-control:focus, .form-select:focus { border-color:#012147; box-shadow:0 0 0 3px rgba(1,33,71,0.1); outline:none; }

    .course-check-row { display:flex; flex-wrap:wrap; gap:14px; margin-bottom:18px; }
    .course-check-row label { font-size:14px; }

    .btn-navy { width:100%; background:#012147; color:#fff; border:none; padding:12px; border-radius:12px; font-weight:600; }
    .btn-navy:hover { background:#0b2d5a; }

    @media (max-width:480px){ .card-box{ padding:20px; } }
</style>
</head>
<body>
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
</body>
</html>