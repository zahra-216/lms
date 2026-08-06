<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lecturer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f4f6fb; }

        .topbar {
            background: #012147; color: #fff; padding: 14px 24px;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 10;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .topbar h1 { font-size: 19px; margin: 0; font-weight: 700; display:flex; align-items:center; gap:10px; }
        .topbar .actions { display: flex; align-items: center; gap: 14px; }
        .topbar .welcome-text { font-size: 14px; opacity: 0.9; }

        .btn-pill-light {
            background: rgba(255,255,255,0.12); border: none; color: #fff;
            padding: 8px 16px; border-radius: 10px; text-decoration: none;
            font-size: 14px; display: inline-flex; align-items: center; gap: 6px;
            transition: 0.2s;
        }
        .btn-pill-light:hover { background: rgba(255,255,255,0.22); color: #fff; }

        .logout-btn {
            background: #ef4444; border: none; color: #fff;
            padding: 8px 16px; border-radius: 10px; font-size: 14px;
            display: inline-flex; align-items: center; gap: 6px; transition: 0.2s;
        }
        .logout-btn:hover { background: #dc2626; }

        .header-actions{ display:flex; gap:12px; flex-wrap:wrap; }
        .btn-pill-navy{
            background:#012147; color:#fff; border:none;
            padding:8px 18px; border-radius:20px; text-decoration:none;
            font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px;
            box-shadow:0 4px 12px rgba(0,0,0,0.08); transition:0.2s;
        }
        .btn-pill-navy:hover{ background:#1e3a6e; color:#fff; }

        @media (max-width:576px){
            .header-actions{ width:100%; }
            .btn-pill-navy{ flex:1; justify-content:center; }
        }
        .hero {
            padding: 80px 24px 70px;
            background: linear-gradient(120deg, rgba(1,33,71,.92), rgba(30,58,110,.88)), url('{{ asset('images/ttmc.jpeg') }}') center/cover no-repeat;
            color: #fff; text-align: center;
        }
        .hero h2 { font-size: 38px; margin-bottom: 12px; font-weight: 700; }
        .hero p { font-size: 17px; max-width: 720px; margin: auto; opacity: 0.92; line-height: 1.6; }
        .hero-icon { font-size: 42px; margin-bottom: 14px; opacity: 0.9; }

        .faculties { padding: 40px 24px 70px; max-width: 1200px; margin: auto; }
        .section-title-row {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 26px; flex-wrap: wrap; gap: 12px;
        }
        .section-title-row h3 { margin: 0 0 4px; font-size: 22px; color: #012147; font-weight: 700; }
        .section-title-row p { margin: 0; color: #64748b; font-size: 14px; }
        .faculty-count-pill {
            background: #fff; color: #012147; font-weight: 600; font-size: 13px;
            padding: 8px 16px; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .faculties-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(270px, 1fr)); gap: 22px; }

        .faculty-card-link { display: block; color: inherit; text-decoration: none; }
        .faculty-card {
            background: #fff; border-radius: 18px; padding: 0; overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,.07); transition: transform .25s ease, box-shadow .25s ease;
        }
        .faculty-card-link:hover .faculty-card { transform: translateY(-6px); box-shadow: 0 14px 32px rgba(0,0,0,.12); }
        .faculty-card-img { height: 170px; background: #f2f5fb; overflow: hidden; position: relative; }
        .faculty-card-img img { width: 100%; height: 100%; object-fit: cover; }
        .faculty-card-body { padding: 20px 22px 24px; }
        .faculty-card h3 { margin: 0 0 8px; font-size: 18px; color: #012147; font-weight: 700; }
        .faculty-card p { margin: 0 0 14px; color: #64748b; font-size: 14px; line-height: 1.5; }
        .faculty-card-cta {
            font-size: 13px; font-weight: 600; color: #012147;
            display: inline-flex; align-items: center; gap: 6px;
        }

        .notif-badge{
            font-size: 10px; padding: 3px 6px;
        }
        .notif-dropdown{ min-width: 300px; max-height: 360px; overflow-y: auto; }
        .notif-dropdown .dropdown-item{ white-space: normal; font-size: 13px; }

        @media (max-width:576px){
            .topbar{ flex-direction:column; align-items:flex-start; gap:10px; padding:14px 16px; }
            .topbar .actions{ width:100%; justify-content:space-between; }
            .hero{ padding:60px 16px 40px; }
            .hero h2{ font-size:26px; }
            .hero p{ font-size:14px; }
            .section-title-row{ flex-direction:column; align-items:flex-start; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <h1><i class="bi bi-speedometer2"></i> Lecturer Dashboard</h1>
        <div class="actions">
            <div class="dropdown">
                <button class="btn-pill-light position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i>
                    @if($lecturer->unreadNotifications->count())
                        <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle notif-badge">
                            {{ $lecturer->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end p-2 notif-dropdown">
                    @if($lecturer->unreadNotifications->count())
                        <li class="d-flex justify-content-end mb-1">
                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" style="font-size:12px;" onclick="markAllRead(this)">
                                Mark all as read
                            </button>
                        </li>
                    @endif
                    @forelse($lecturer->notifications->take(10) as $note)
                        <li class="mb-1">
                            <a href="{{ isset($note->data['subject_id']) ? route('lecturer.subject.timetable', $note->data['subject_id']) : '#' }}"
                            class="dropdown-item rounded {{ $note->read_at ? '' : 'bg-light fw-semibold' }}"
                            onclick="event.preventDefault();
                                        fetch('{{ route('lecturer.notification.read', $note->id) }}', {
                                            method:'POST',
                                            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
                                        }).then(()=> window.location = this.href);">
                                <div>{{ $note->data['title'] ?? 'Notification' }}</div>
                                <div class="text-muted" style="font-size:12px;">{{ $note->data['message'] ?? '' }}</div>
                            </a>
                        </li>
                    @empty
                        <li class="text-center text-muted p-2" style="font-size:13px;">No notifications</li>
                    @endforelse
                </ul>
            </div>
            <span class="welcome-text">Welcome, {{ $lecturer->name }}</span>
            <form method="POST" action="{{ route('lecturer.logout') }}">
                @csrf
                <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
        </div>
    </div>

    <section class="hero">
        <i class="bi bi-mortarboard hero-icon"></i>
        <h2>Hello, {{ $lecturer->name }}!</h2>
        <p>Here are all the faculties available in the system. Use this page to review faculty offerings and start your lecturer activities from here.</p>
    </section>

    <section class="faculties">
        <div class="section-title-row">
            <div>
                <h3>All Faculties</h3>
                <p>Browse the active faculties in your school.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('lecturer.lecture-records.index') }}" class="btn-pill-navy">
                    <i class="bi bi-journal-text"></i> Lecture Record
                </a>
                <a href="{{ route('lecturer.my.payments') }}" class="btn-pill-navy">
                    <i class="bi bi-wallet2"></i> My Payments
                </a>
            </div>
        </div>

        <div class="faculties-grid">
            @foreach($faculties as $faculty)
                @php
                    $imgPath = 'storage/faculty/'.$faculty->image;
                    $imageUrl = ($faculty->image && file_exists(public_path($imgPath)))
                        ? asset($imgPath)
                        : 'https://picsum.photos/320/220?random=' . $loop->index;
                @endphp
                <a href="{{ route('faculty.courses', ['facultyId' => $faculty->id]) }}" class="faculty-card-link">
                    <div class="faculty-card">
                        <div class="faculty-card-img">
                            <img src="{{ $imageUrl }}" alt="{{ $faculty->name }}" />
                        </div>
                        <div class="faculty-card-body">
                            <h3>{{ $faculty->name }}</h3>
                            <p>{{ $faculty->description ?? 'No description available.' }}</p>
                            <span class="faculty-card-cta">View Courses <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function markAllRead(btn) {
        fetch('{{ route('lecturer.notification.readAll') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        }).then(() => window.location.reload());
    }
    </script>
</body>
</html>