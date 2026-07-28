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
            <span class="welcome-text">Welcome, {{ $lecturer->name }}</span>
            <a href="{{ route('lecturer.my.payments') }}" class="btn-pill-light">
                <i class="bi bi-wallet2"></i> My Payments
            </a>
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
            <span class="faculty-count-pill"><i class="bi bi-building"></i> {{ $faculties->count() }} Faculties</span>
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
</body>
</html>