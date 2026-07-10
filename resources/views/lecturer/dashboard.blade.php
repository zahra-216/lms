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
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f4f7ff; }
        .topbar { background: #012147; color: #fff; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 10; }
        .topbar h1 { font-size: 20px; margin: 0; }
        .topbar .actions { display: flex; align-items: center; gap: 12px; }
        .hero { padding: 100px 24px 60px; background: linear-gradient(180deg, rgba(1,33,71,.9) 0%, rgba(1,33,71,.8) 100%), url('{{ asset('images/ttmc.jpeg') }}') center/cover no-repeat; color: #fff; text-align: center; }
        .hero h2 { font-size: 42px; margin-bottom: 12px; }
        .hero p { font-size: 18px; max-width: 760px; margin: auto; }
        .faculties { padding: 36px 24px 60px; max-width: 1200px; margin: auto; }
        .faculties-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; }
        .faculty-card { background: #fff; border-radius: 18px; padding: 26px; box-shadow: 0 14px 36px rgba(0,0,0,.08); transition: transform .25s ease; }
        .faculty-card:hover { transform: translateY(-6px); }
        .faculty-card h3 { margin: 0 0 12px; font-size: 20px; color: #012147; }
        .faculty-card p { margin: 0; color: #475569; }
        .faculty-card-link { display: block; color: inherit; text-decoration: none; }
        .faculty-card-link:hover .faculty-card { transform: translateY(-6px); }
        .logout-btn { background: #ff4d4f; border: none; color: white; padding: 10px 18px; border-radius: 10px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Lecturer Dashboard</h1>
        <div class="actions">
            <span>Welcome, {{ $lecturer->name }}</span>
            <form method="POST" action="{{ route('lecturer.logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <section class="hero">
        <h2>Hello, {{ $lecturer->name }}!</h2>
        <p>Here are all the faculties available in the system. Use this page to review faculty offerings and start your lecturer activities from here.</p>
    </section>

    <section class="faculties">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">All Faculties</h3>
                <p class="text-secondary">Browse the active faculties in your school.</p>
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
                        <div style="height:180px; border-radius:16px; overflow:hidden; background:#f2f5fb; margin-bottom:18px; display:flex; align-items:center; justify-content:center;">
                            <img src="{{ $imageUrl }}" alt="{{ $faculty->name }}" style="width:100%; height:100%; object-fit:cover;" />
                        </div>
                        <h3>{{ $faculty->name }}</h3>
                        <p>{{ $faculty->description ?? 'No description available.' }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
</body>
</html>
