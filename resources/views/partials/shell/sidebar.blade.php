@php
    // [label, route name, icon, active pattern(s)]
    $navGroups = match ($role) {
        'admin' => [
            'Overview' => [
                ['Dashboard', 'admin.dashboard', 'speedometer2', ['admin.dashboard']],
            ],
            'People' => [
                ['Students', 'admin.students.index', 'people', ['admin.students.*']],
                ['Lecturers', 'admin.lecturers.index', 'person-workspace', ['admin.lecturers.*']],
            ],
            'Academics' => [
                ['Faculties', 'admin.faculties.index', 'building', ['admin.faculties.*']],
                ['Courses', 'admin.courses.index', 'mortarboard', ['admin.courses.*']],
            ],
            'Teaching' => [
                ['Assignments', 'admin.assignments.browse', 'journal-text', ['admin.assignments.*']],
                ['Marks', 'admin.marks.index', 'award', ['admin.marks.*']],
                ['Lecture Records', 'admin.lecture-records.index', 'journal-plus', ['admin.lecture-records.*']],
                ['Attendance', 'admin.attendance.index', 'calendar-check', ['admin.attendance.*']],
                ['Scan Attendance', 'admin.scan.index', 'qr-code-scan', ['admin.scan.*']],
                ['Gate Log', 'admin.gate-log.index', 'door-open', ['admin.gate-log.*']],
            ],
            'Finance' => [
                ['Student Payments', 'admin.payments.index', 'cash-coin', ['admin.payments.*']],
                ['Lecturer Payments', 'admin.lecturer-payments.index', 'wallet2', ['admin.lecturer-payments.*']],
            ],
            'Records & Messages' => [
                ['Certificates', 'admin.certificates.index', 'patch-check', ['admin.certificates.*']],
                ['Chats', 'admin.chats.index', 'chat-dots', ['admin.chats.*'], 'unreadChats'],
                ['Send Notification', 'admin.notifications.create', 'megaphone', ['admin.notifications.*']],
            ],
        ],
        'lecturer' => [
            'My Teaching' => [
                ['Dashboard', 'lecturer.dashboard', 'speedometer2', ['lecturer.dashboard', 'lecturer.subject.*', 'lecturer.notes.*', 'lecturer.videos.*', 'lecturer.assignments.*', 'lecturer.quizzes.*', 'lecturer.marks.*']],
                ['Lecture Records', 'lecturer.lecture-records.index', 'journal-plus', ['lecturer.lecture-records.*']],
            ],
            'Account' => [
                ['My Payments', 'lecturer.my.payments', 'cash-coin', ['lecturer.my.payments']],
                ['Messages', 'lecturer.chat.index', 'chat-dots', ['lecturer.chat.*']],
                ['Notifications', 'lecturer.notifications.index', 'bell', ['lecturer.notifications.*']],
            ],
        ],
        'student' => [
            'My Learning' => [
                ['Dashboard', 'dashboard', 'speedometer2', ['dashboard', 'student.subject.*']],
                ['My Grades', 'student.grades', 'award', ['student.grades']],
                ['Recordings', null, 'camera-video', ['student.recordings.*']],
            ],
            'Account' => [
                ['Payments', 'student.my.payments', 'cash-coin', ['student.my.payments']],
                ['Messages', 'student.chat.index', 'chat-dots', ['student.chat.*']],
                ['My Profile', 'student.profile', 'person-circle', ['student.profile', 'student.profile.*']],
            ],
        ],
    };
    $roleLabel = ['admin' => 'Administrator', 'lecturer' => 'Lecturer', 'student' => 'Student'][$role];
@endphp

<aside class="lms-sidebar" id="lmsSidebar" aria-label="Main navigation">
    <a href="{{ $homeUrl }}" class="lms-brand">
        <img src="{{ asset('images/logo.jpeg') }}" alt="TT Metro Campus">
        <span><b>TT METRO</b><small>Learning Portal</small></span>
    </a>
    <span class="lms-role-pill"><i class="bi bi-shield-check"></i> {{ $roleLabel }}</span>

    <nav class="lms-nav">
        @foreach ($navGroups as $title => $items)
            <div class="lms-nav-title">{{ $title }}</div>
            @foreach ($items as $item)
                @php
                    [$label, $routeName, $icon, $patterns] = $item;
                    $badgeVar = $item[4] ?? null;
                    $badge = $badgeVar ? (int) ($lmsBadges[$badgeVar] ?? 0) : 0;
                @endphp
                @continue(! $routeName || ! Route::has($routeName))
                <a href="{{ route($routeName) }}" class="{{ request()->routeIs(...$patterns) ? 'active' : '' }}">
                    <i class="bi bi-{{ $icon }}"></i> {{ $label }}
                    @if ($badge > 0)<span class="badge bg-danger rounded-pill">{{ $badge }}</span>@endif
                </a>
            @endforeach
        @endforeach
    </nav>

    <div class="lms-sidebar-foot">
        Need help? <a href="https://ttmetrocampus.com/contact" target="_blank" rel="noopener">Contact the campus</a>
    </div>
</aside>
