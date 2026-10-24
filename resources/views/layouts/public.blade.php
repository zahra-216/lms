<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{!! trim($__env->yieldContent('title')) ?: 'Learning Portal' !!} | TT Metro Campus LMS</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/lms.css') }}?v={{ @filemtime(public_path('css/lms.css')) }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="lms lms-public">
    <div class="pub-topbar">
        <div class="pub-wrap d-flex justify-content-between flex-wrap gap-2">
            <span><i class="bi bi-telephone"></i> 011 4319 996 &nbsp;|&nbsp; 077 2270 348</span>
            <span><i class="bi bi-envelope"></i> Info.ttmcml@gmail.com</span>
        </div>
    </div>

    <nav class="pub-nav">
        <div class="pub-wrap d-flex align-items-center justify-content-between gap-3">
            <a href="{{ url('/') }}" class="pub-brand">
                <img src="{{ asset('images/logo.jpeg') }}" alt="TT Metro Campus">
                <span><b>TT METRO CAMPUS</b><small>Learning Portal</small></span>
            </a>
            <div class="pub-links">
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
                <a href="{{ url('/student-grade') }}" class="{{ request()->is('student-grade') ? 'active' : '' }}">Grade scale</a>
                <a href="{{ route('verify.show') }}" class="{{ request()->is('verify*') ? 'active' : '' }}">Verify certificate</a>
                <a href="https://ttmetrocampus.com/" target="_blank" rel="noopener">Main website <i class="bi bi-box-arrow-up-right small"></i></a>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if (session()->has('student_id'))
                    <a href="{{ route('dashboard') }}" class="btn-navy">My dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-navy"><i class="bi bi-box-arrow-in-right"></i> Student login</a>
                @endif
            </div>
        </div>
    </nav>

    <main>@yield('content')</main>

    <footer class="pub-footer">
        <div class="pub-wrap d-flex justify-content-between flex-wrap gap-3">
            <div>
                <b>TT Metro Campus</b><br>
                <span><i class="bi bi-geo-alt"></i> No 11 A1, Galle Road, Mount Lavinia</span>
            </div>
            <div class="text-md-end">
                <a href="{{ route('lecturer.login') }}">Lecturer login</a> &middot; <a href="{{ route('admin.login') }}">Admin login</a><br>
                <small>&copy; {{ date('Y') }} TT Metro Campus. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
