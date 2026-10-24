@php
    // Who is signed in, whichever guard / session the area uses.
    $lmsUser = match ($role) {
        'admin' => auth('admin')->user(),
        'lecturer' => auth('lecturer')->user(),
        'student' => \App\Models\Student::find(session('student_id')),
    };
    $lmsName = $lmsUser->name ?? ($lmsUser->full_name ?? ucfirst($role));
    $initials = collect(preg_split('/\s+/', trim((string) $lmsName)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $photo = match ($role) {
        'admin' => ($lmsUser->photo ?? null) ? asset('uploads/admin/'.$lmsUser->photo) : null,
        'student' => ($lmsUser->photo ?? null) ? asset('storage/'.$lmsUser->photo) : null,
        default => null,
    };
    $unread = $lmsUser && method_exists($lmsUser, 'unreadNotifications') ? $lmsUser->unreadNotifications->sortByDesc('created_at')->take(15) : collect();
    $unreadCount = $lmsUser && method_exists($lmsUser, 'unreadNotifications') ? $lmsUser->unreadNotifications->count() : 0;
    $notifRoutes = [
        'read' => route($role.'.notification.read', ['id' => '__ID__']),
        'all' => route($role.'.notification.readAll'),
    ];
    $profileUrl = ['admin' => route('admin.profile'), 'student' => route('student.profile'), 'lecturer' => null][$role];
    $logoutUrl = ['admin' => route('admin.logout'), 'student' => route('logout'), 'lecturer' => route('lecturer.logout')][$role];
@endphp

<header class="lms-topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="lms-menu-toggle" id="menuToggle" type="button" aria-label="Open menu"><i class="bi bi-list"></i></button>
        <h5>Welcome, <span>{{ \Illuminate\Support\Str::words($lmsName, 2, '') }}</span></h5>
    </div>

    <div class="lms-topbar-actions">
        <div class="dropdown" data-notif-read-url="{{ $notifRoutes['read'] }}" data-notif-all-url="{{ $notifRoutes['all'] }}">
            <a href="#" class="lms-icon-btn" id="notifDropdown" data-bs-toggle="dropdown" aria-label="Notifications">
                <i class="bi bi-bell"></i>
                @if ($unreadCount > 0)<span class="badge rounded-pill bg-danger" data-notif-count>{{ $unreadCount }}</span>@endif
            </a>
            <ul class="dropdown-menu dropdown-menu-end lms-dropdown-menu" style="width:340px;max-height:420px;overflow:auto;">
                <li class="notif-header d-flex justify-content-between align-items-center">
                    <span>Notifications</span>
                    @if ($unreadCount > 0)<button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="markAllReadBtn">Mark all read</button>@endif
                </li>
                @forelse ($unread as $notif)
                    <li class="dropdown-item d-flex justify-content-between align-items-start gap-2 text-wrap" data-notif-item="{{ $notif->id }}">
                        <a href="{{ $notif->data['link'] ?? '#' }}" class="text-decoration-none text-reset flex-grow-1">
                            <strong>{{ $notif->data['title'] ?? 'Notification' }}</strong><br>
                            <small class="text-muted">{{ $notif->data['message'] ?? '' }}</small>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-secondary mark-read-btn" data-id="{{ $notif->id }}" title="Mark as read"><i class="bi bi-check2"></i></button>
                    </li>
                @empty
                    <li class="text-center text-muted p-3 small">You're all caught up.</li>
                @endforelse
            </ul>
        </div>

        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none" id="profileDropdown" data-bs-toggle="dropdown" aria-label="Account menu">
                @if ($photo)
                    <img src="{{ $photo }}" class="lms-avatar" alt="{{ $lmsName }}" onerror="this.replaceWith(Object.assign(document.createElement('span'),{className:'lms-icon-btn fw-bold',textContent:'{{ $initials ?: 'U' }}'}))">
                @else
                    <span class="lms-icon-btn fw-bold" style="background:var(--sky-soft);color:var(--brand-700)">{{ $initials ?: 'U' }}</span>
                @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-end lms-dropdown-menu" aria-labelledby="profileDropdown">
                <li class="px-3 py-2"><div class="fw-bold">{{ $lmsName }}</div><div class="small text-muted">{{ $lmsUser->email ?? '' }}</div></li>
                <li><hr class="dropdown-divider"></li>
                @if ($profileUrl)<li><a class="dropdown-item" href="{{ $profileUrl }}"><i class="bi bi-person me-2"></i> My profile</a></li>@endif
                @if ($role === 'admin')<li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bi bi-gear me-2"></i> Settings</a></li>@endif
                <li>
                    <form method="POST" action="{{ $logoutUrl }}">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Log out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
