<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $editGroup ? 'Edit' : 'Add' }} Timetable - {{ $subject->name }} | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    body { background:#f4f6fb; font-family:'Segoe UI', sans-serif; padding:40px 15px; }
    @media (max-width:480px){ body{ padding:20px 12px; } }
    .container { max-width:800px; margin:auto; }
    .back-btn{ border:none; background:#fff; color:#012147; font-weight:600; padding:8px 16px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.06); text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:18px; }
    .back-btn:hover{ background:#012147; color:#fff; }
    .page-header{ background:linear-gradient(120deg,#012147,#1e3a6e); color:#fff; border-radius:18px; padding:26px 30px; margin-bottom:30px; box-shadow:0 10px 30px rgba(1,33,71,0.25); }
    .page-header h2{ margin:0; font-weight:700; font-size:22px; }
    .card-box{ background:#fff; border-radius:15px; box-shadow:0 6px 20px rgba(0,0,0,0.06); padding:26px; }
    label{ font-weight:600; color:#012147; font-size:14px; margin-bottom:6px; }
    .form-control, .form-select, .select2-selection{ border-radius:10px !important; }
    .select2-container .select2-selection--single{ height:40px !important; padding:5px 8px; }
    .day-check-group{ display:flex; flex-wrap:wrap; gap:10px; margin-bottom:10px; }
    .day-check{ background:#f4f6fb; border-radius:10px; padding:8px 14px; cursor:pointer; user-select:none; }
    .day-check input{ margin-right:6px; }
    .day-time-row{ display:none; background:#f8fafc; border-radius:10px; padding:14px 16px; margin-bottom:10px; border:1px solid #e2e8f0; }
    .day-time-row.active{ display:flex; gap:14px; align-items:center; flex-wrap:wrap; }
    .day-time-row .day-label{ font-weight:700; color:#012147; min-width:90px; }
    .btn-navy{ background:#012147; color:#fff; border:none; padding:10px 26px; border-radius:10px; font-weight:600; }
    .btn-navy:hover{ background:#1e3a6e; color:#fff; }
</style>
</head>
<body>
<div class="container">
    <a href="{{ route('admin.subjects.timetables.index', $subject->id) }}" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

    <div class="page-header">
        <h2><i class="bi bi-calendar-week"></i> {{ $editGroup ? 'Edit' : 'Add' }} Timetable</h2>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-box">
        <form method="POST" action="{{ $editGroup ? route('admin.subjects.timetables.update', [$subject->id, $editGroup]) : route('admin.subjects.timetables.store', $subject->id) }}">
            @csrf
            @if($editGroup)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label>Module</label>
                <input type="text" class="form-control" value="{{ $subject->code }} - {{ $subject->name }}" readonly>
            </div>

            <div class="mb-3">
                <label>Lecturer</label>
                <select name="lecturer_id" id="lecturerSelect" class="form-select" style="width:100%" required>
                    <option value=""></option>
                    @foreach($lecturers as $lecturer)
                        <option value="{{ $lecturer->id }}" {{ (old('lecturer_id', $existingRows->first()->lecturer_id ?? '') == $lecturer->id) ? 'selected' : '' }}>
                            {{ $lecturer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Days</label>
                <div class="day-check-group">
                    @php
                        $allDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                        $selectedDays = old('days', $existingRows->pluck('day')->toArray());
                        $rowsByDay = $existingRows->keyBy('day');
                    @endphp
                    @foreach($allDays as $day)
                        <label class="day-check">
                            <input type="checkbox" name="days[]" value="{{ $day }}" class="day-toggle"
                                data-target="row-{{ $day }}"
                                {{ in_array($day, $selectedDays) ? 'checked' : '' }}>
                            {{ $day }}
                        </label>
                    @endforeach
                </div>

                @foreach($allDays as $day)
                    <div class="day-time-row {{ in_array($day, $selectedDays) ? 'active' : '' }}" id="row-{{ $day }}">
                        <span class="day-label">{{ $day }}</span>
                        <div>
                            <label class="mb-1">Start</label>
                            <input type="time" name="start_time[{{ $day }}]" class="form-control"
                                value="{{ old('start_time.'.$day, $rowsByDay[$day]->start_time ?? '') !== '' ? \Illuminate\Support\Carbon::parse(old('start_time.'.$day, $rowsByDay[$day]->start_time ?? ''))->format('H:i') : '' }}">
                        </div>
                        <div>
                            <label class="mb-1">End</label>
                            <input type="time" name="end_time[{{ $day }}]" class="form-control"
                                value="{{ old('end_time.'.$day, $rowsByDay[$day]->end_time ?? '') !== '' ? \Illuminate\Support\Carbon::parse(old('end_time.'.$day, $rowsByDay[$day]->end_time ?? ''))->format('H:i') : '' }}">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mb-4">
                <label>Content Covered <span class="text-muted fw-normal">(optional)</span></label>
                <textarea name="content_covered" class="form-control" rows="3">{{ old('content_covered', $existingRows->first()->content_covered ?? '') }}</textarea>
            </div>

            <button type="submit" class="btn btn-navy"><i class="bi bi-check-circle"></i> {{ $editGroup ? 'Update' : 'Save' }} Timetable</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#lecturerSelect').select2({
            placeholder: 'Search and select a lecturer',
            allowClear: true
        });

        $('.day-toggle').on('change', function () {
            const target = document.getElementById($(this).data('target'));
            if (this.checked) {
                target.classList.add('active');
            } else {
                target.classList.remove('active');
                target.querySelectorAll('input[type="time"]').forEach(i => i.value = '');
            }
        });
    });
</script>
</body>
</html>