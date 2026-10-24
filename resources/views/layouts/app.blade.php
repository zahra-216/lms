<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | TT Metro Campus LMS</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/lms.css') }}?v={{ @filemtime(public_path('css/lms.css')) }}" rel="stylesheet">
    @stack('head')
    @stack('styles')
</head>
<body class="lms lms-{{ $role }}">
    <div class="lms-overlay" id="lmsOverlay"></div>

    @include('partials.shell.sidebar')

    <div class="lms-wrap">
        @include('partials.shell.topbar')

        <main class="lms-main" id="main">
            @unless (View::hasSection('no_back'))
                <div class="container lms-back-row">
                    @php $explicitBack = trim($__env->yieldContent('back')); @endphp
                    <a href="{{ $explicitBack ?: '#' }}" class="back-btn" @if (! $explicitBack) data-lms-back data-fallback="{{ $homeUrl }}" @endif>
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            @endunless

            @include('partials.shell.flash')

            @yield('content')
        </main>

        <footer class="lms-footer">
            &copy; {{ date('Y') }} TT Metro Campus. All rights reserved. &middot;
            <a href="https://ttmetrocampus.com/" target="_blank" rel="noopener">ttmetrocampus.com</a>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/lms.js') }}?v={{ @filemtime(public_path('js/lms.js')) }}"></script>
    @stack('scripts')
</body>
</html>
