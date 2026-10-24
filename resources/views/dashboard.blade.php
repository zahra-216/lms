@extends('layouts.student')
@section('title')
Dashboard
@endsection
@section('no_back', '1')

@push('styles')
<style>
    .tile { display:flex; align-items:center; gap:14px; text-decoration:none; color:var(--navy-900); font-weight:700; transition:transform .15s, box-shadow .15s; }
    .tile:hover { transform:translateY(-3px); box-shadow:var(--shadow-lift); color:var(--navy-900); }
    .tile .stat-icon { margin:0; }
    .welcome-pill { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,.16); color:#fff; padding:5px 14px; border-radius:999px; font-size:13px; font-weight:600; margin:8px 8px 0 0; }
    .subject-row { cursor:pointer; transition:background .15s; }
    .subject-row:hover { background:var(--sky-soft); }
    .accordion-button:not(.collapsed) { background:var(--sky-soft); color:var(--navy-900); box-shadow:none; }
    .accordion-item { border:1px solid var(--line); border-radius:14px !important; overflow:hidden; }
    .upcoming-item { display:flex; align-items:center; gap:14px; padding:12px 14px; border:1px solid var(--line); border-radius:14px; background:var(--surface); }
    .upcoming-icon { width:42px; height:42px; border-radius:12px; background:var(--sky-soft); color:var(--brand-700); display:grid; place-items:center; font-size:18px; flex-shrink:0; }
    .upcoming-day { background:var(--brand-700); color:#fff; border-radius:999px; padding:4px 12px; font-size:12px; font-weight:700; }
    #calendar table { width:100%; text-align:center; border-collapse:separate; border-spacing:4px; }
    #calendar th { font-size:11px; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); background:transparent !important; padding:4px 0; border:0; }
    #calendar td { padding:8px 0; border-radius:10px; font-size:14px; }
    #calendar td.today { background:var(--brand-700); color:#fff; font-weight:700; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2>Welcome back, {{ \Illuminate\Support\Str::words($student->name, 2, '') }}!</h2>
            <div>
                <span class="welcome-pill"><i class="bi bi-mortarboard"></i> {{ $course->name ?? 'N/A' }}</span>
                <span class="welcome-pill"><i class="bi bi-bar-chart-steps"></i> {{ $level->name ?? 'N/A' }}</span>
                <span class="welcome-pill"><i class="bi bi-person-badge"></i> {{ $student->registration_no }}</span>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4"><a href="{{ route('student.grades') }}" class="stat-card tile"><div class="stat-icon"><i class="bi bi-clipboard-data"></i></div> My Grades</a></div>
        <div class="col-12 col-md-4"><a href="{{ route('student.my.payments') }}" class="stat-card tile"><div class="stat-icon"><i class="bi bi-wallet2"></i></div> My Payments</a></div>
        <div class="col-12 col-md-4"><a href="{{ route('student.profile') }}" class="stat-card tile"><div class="stat-icon"><i class="bi bi-person-circle"></i></div> My Profile</a></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-box">
                <h3 class="section-title"><i class="bi bi-journal-bookmark text-primary"></i> My semesters</h3>
                @if ($semesters->count() > 0)
                    <div class="accordion" id="semesterAccordion">
                        @foreach ($semesters as $semester)
                            <div class="accordion-item mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sem{{ $semester->id }}" onclick="loadSubjects({{ $semester->id }})">
                                        <i class="bi bi-journal-text me-2"></i> {{ $semester->name }}
                                    </button>
                                </h2>
                                <div id="sem{{ $semester->id }}" class="accordion-collapse collapse" data-bs-parent="#semesterAccordion">
                                    <div class="accordion-body"><div id="subjectBox{{ $semester->id }}" class="text-muted small">Loading subjects...</div></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state"><i class="bi bi-journal"></i>No semesters found for your course yet.</div>
                @endif
            </div>

            <div class="card-box">
                <h3 class="section-title"><i class="bi bi-calendar-week text-primary"></i> Upcoming classes</h3>
                @if ($upcomingClasses->count() > 0)
                    <div class="d-flex flex-column gap-2">
                        @foreach ($upcomingClasses as $class)
                            <div class="upcoming-item">
                                <div class="upcoming-icon"><i class="bi bi-book"></i></div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $class->subject->name ?? 'N/A' }}</div>
                                    <div class="small text-muted"><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($class->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($class->end_time)->format('h:i A') }}</div>
                                </div>
                                <span class="upcoming-day">{{ $class->day }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted">No upcoming classes.</div>
                @endif
            </div>

            <div class="card-box">
                <h3 class="section-title"><i class="bi bi-clipboard-check text-primary"></i> Pending assignments</h3>
                @if ($pendingAssignments->count() > 0)
                    <div class="list-group">
                        @foreach ($pendingAssignments as $a)
                            <a href="{{ route('student.subject.portal.assignments', $a->subject_id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $a->subject->name ?? 'N/A' }}</div>
                                    <div class="text-muted small">{{ $a->title }}</div>
                                </div>
                                @if ($a->due_date->isPast())
                                    <span class="badge bg-danger">Overdue</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $a->due_date->diffForHumans(now(), true) }} left</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted">You're all caught up - no pending assignments.</div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-box">
                <h3 class="section-title"><i class="bi bi-calendar3 text-primary"></i> Calendar</h3>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

{{-- Subject code modal: a subject opens after its code has been entered once --}}
<div class="modal fade" id="subjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enter subject code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="subject_id">
                <label class="form-label" for="subject_code">Subject code</label>
                <input type="text" id="subject_code" class="form-control" autocomplete="off">
                <small class="text-danger" id="errorMsg"></small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-navy" onclick="checkSubject()">Open subject</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const loaded = {};

function loadSubjects(semesterId) {
    if (loaded[semesterId]) return;
    fetch(`/student/semester/${semesterId}/subjects`)
        .then(res => res.json())
        .then(data => {
            loaded[semesterId] = true;
            let html = '';
            if (!data.subjects || data.subjects.length === 0) {
                html = '<p class="mb-0">No subjects found.</p>';
            } else {
                html = '<div class="list-group">';
                data.subjects.forEach(sub => {
                    html += `<div class="list-group-item subject-row d-flex justify-content-between align-items-center" onclick="openSubject(${Number(sub.id)}, ${sub.unlocked ? 'true' : 'false'})">
                        <div><b>${esc(sub.code)}</b> ${esc(sub.name)}</div>
                        <span>${sub.unlocked ? '<i class="bi bi-unlock text-success"></i>' : '<i class="bi bi-lock text-muted"></i>'}</span>
                    </div>`;
                });
                html += '</div>';
            }
            document.getElementById('subjectBox' + semesterId).innerHTML = html;
        })
        .catch(() => { document.getElementById('subjectBox' + semesterId).textContent = 'Could not load subjects. Please try again.'; });
}

function openSubject(subjectId, unlocked) {
    if (unlocked) { window.location.href = '/student/subject/' + subjectId + '/show'; return; }
    document.getElementById('subject_id').value = subjectId;
    document.getElementById('errorMsg').textContent = '';
    new bootstrap.Modal(document.getElementById('subjectModal')).show();
}

function checkSubject() {
    fetch('/student/verify-subject', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ subject_id: document.getElementById('subject_id').value, code: document.getElementById('subject_code').value })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status) {
            bootstrap.Modal.getInstance(document.getElementById('subjectModal')).hide();
            window.location.href = '/student/subject/' + data.subject_id + '/show';
        } else {
            document.getElementById('errorMsg').textContent = data.message;
        }
    })
    .catch(() => { document.getElementById('errorMsg').textContent = 'Server error. Please try again.'; });
}

(function () {
    const d = new Date(), y = d.getFullYear(), m = d.getMonth();
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const first = new Date(y, m, 1).getDay(), days = new Date(y, m + 1, 0).getDate();
    let html = `<h6 class="text-center mb-2">${months[m]} ${y}</h6><table><tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr><tr>`;
    let day = 1;
    for (let i = 0; i < 6 && day <= days; i++) {
        for (let j = 0; j < 7; j++) {
            if ((i === 0 && j < first) || day > days) html += '<td></td>';
            else { html += day === d.getDate() ? `<td class="today">${day}</td>` : `<td>${day}</td>`; day++; }
        }
        html += '</tr><tr>';
    }
    document.getElementById('calendar').innerHTML = html + '</tr></table>';
})();
</script>
@endpush
