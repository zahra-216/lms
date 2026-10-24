@extends('layouts.admin')
@section('title')
Dashboard
@endsection
@section('no_back', '1')

@push('styles')
<style>
    .tile { display:flex; align-items:center; gap:16px; text-decoration:none; color:inherit; height:100%; transition:transform .15s, box-shadow .15s; }
    .tile:hover { transform:translateY(-3px); box-shadow:var(--shadow-lift); color:inherit; }
    .tile .stat-icon { margin:0; flex-shrink:0; }
    .quick a { display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:14px; background:var(--surface); color:var(--navy-900); text-decoration:none; font-weight:600; font-size:14px; margin-bottom:10px; border:1px solid var(--line); transition:all .15s; }
    .quick a:hover { background:var(--sky-soft); border-color:var(--sky); }
    .quick a i { width:34px; height:34px; border-radius:10px; background:#fff; display:grid; place-items:center; color:var(--brand-600); }
    .calendar-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; font-weight:700; color:var(--navy-900); }
    .calendar-nav-btn { border:1px solid var(--line); background:#fff; width:34px; height:34px; border-radius:10px; }
    .calendar-nav-btn:hover { background:var(--sky-soft); }
    .calendar-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:6px; text-align:center; }
    .calendar-day-name { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); padding:4px 0; }
    .calendar-day { padding:8px 0; border-radius:10px; font-size:14px; }
    .calendar-day.today { background:var(--brand-700); color:#fff; font-weight:700; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2>Welcome back, {{ \Illuminate\Support\Str::words(auth('admin')->user()->name ?? 'Admin', 1, '') }}</h2>
            <small>{{ now()->format('l, d F Y') }} &middot; here's what is happening at the campus.</small>
        </div>
        <a href="{{ route('admin.students.create') }}" class="add-btn"><i class="bi bi-person-plus"></i> Add Student</a>
    </div>

    <div class="row g-3 mb-4">
        @foreach ($stats as [$label, $value, $icon, $url])
            <div class="col-6 col-lg-4 col-xl-2">
                <a href="{{ $url }}" class="stat-card tile">
                    <div class="stat-icon"><i class="bi bi-{{ $icon }}"></i></div>
                    <div>
                        <div class="stat-value">{{ number_format($value) }}</div>
                        <div class="stat-label">{{ $label }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-box">
                <h3 class="section-title"><i class="bi bi-person-plus text-primary"></i> Recently added students</h3>
                @if ($recentStudents->isEmpty())
                    <div class="empty-state"><i class="bi bi-people"></i>No students yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead><tr><th>Name</th><th>Registration no.</th><th>Course</th><th>Status</th><th></th></tr></thead>
                            <tbody>
                            @foreach ($recentStudents as $s)
                                <tr>
                                    <td class="fw-semibold">{{ $s->name }}</td>
                                    <td class="text-muted">{{ $s->registration_no }}</td>
                                    <td>{{ $s->course->name ?? '-' }}</td>
                                    <td><span class="badge {{ $s->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($s->status ?? 'n/a') }}</span></td>
                                    <td class="text-end"><a href="{{ route('admin.students.edit', $s->id) }}" class="icon-edit" title="Edit"><i class="bi bi-pencil"></i></a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="card-box">
                <div class="calendar-header">
                    <button id="prevMonth" class="calendar-nav-btn" type="button" aria-label="Previous month"><i class="bi bi-chevron-left"></i></button>
                    <div id="monthYear"></div>
                    <button id="nextMonth" class="calendar-nav-btn" type="button" aria-label="Next month"><i class="bi bi-chevron-right"></i></button>
                </div>
                <div class="calendar-grid" id="calendarDays"></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-box quick">
                <h3 class="section-title"><i class="bi bi-lightning-charge text-warning"></i> Quick actions</h3>
                <a href="{{ route('admin.students.create') }}"><i class="bi bi-person-plus"></i> Add a student</a>
                <a href="{{ route('admin.lecturers.create') }}"><i class="bi bi-person-workspace"></i> Add a lecturer</a>
                <a href="{{ route('admin.scan.index') }}"><i class="bi bi-qr-code-scan"></i> Scan attendance</a>
                <a href="{{ route('admin.notifications.create') }}"><i class="bi bi-megaphone"></i> Send a notification</a>
                <a href="{{ route('admin.payments.index') }}"><i class="bi bi-cash-coin"></i> Student payments</a>
                <a href="{{ route('admin.certificates.index') }}"><i class="bi bi-patch-check"></i> Certificates</a>
            </div>

            <div class="card-box">
                <h3 class="section-title"><i class="bi bi-activity text-success"></i> This week</h3>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Assignment submissions</span><strong>{{ $newSubmissions }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span class="text-muted">Unread chat messages</span><strong>{{ $lmsBadges['unreadChats'] ?? 0 }}</strong></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var grid = document.getElementById('calendarDays'), label = document.getElementById('monthYear'), now = new Date();
    function render(d) {
        grid.innerHTML = '';
        var y = d.getFullYear(), m = d.getMonth();
        label.textContent = d.toLocaleString('default', { month: 'long', year: 'numeric' });
        ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(function (n) {
            var c = document.createElement('div'); c.className = 'calendar-day-name'; c.textContent = n; grid.appendChild(c);
        });
        for (var i = 0; i < new Date(y, m, 1).getDay(); i++) grid.appendChild(document.createElement('div'));
        for (var day = 1; day <= new Date(y, m + 1, 0).getDate(); day++) {
            var c = document.createElement('div'); c.className = 'calendar-day'; c.textContent = day;
            if (day === now.getDate() && m === now.getMonth() && y === now.getFullYear()) c.classList.add('today');
            grid.appendChild(c);
        }
    }
    var shown = new Date();
    document.getElementById('prevMonth').onclick = function () { shown.setMonth(shown.getMonth() - 1); render(shown); };
    document.getElementById('nextMonth').onclick = function () { shown.setMonth(shown.getMonth() + 1); render(shown); };
    render(shown);
})();
</script>
@endpush
