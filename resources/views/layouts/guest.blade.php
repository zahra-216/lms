<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{!! trim($__env->yieldContent('title')) ?: 'Sign in' !!} | TT Metro Campus LMS</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/lms.css') }}?v={{ @filemtime(public_path('css/lms.css')) }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="lms lms-guest">
    <div class="auth-shell">
        <aside class="auth-side">
            <a href="https://ttmetrocampus.com/" class="lms-brand" target="_blank" rel="noopener">
                <img src="{{ asset('images/logo.jpeg') }}" alt="TT Metro Campus">
                <span><b>TT METRO CAMPUS</b><small>Higher Education Institute</small></span>
            </a>
            <div>
                <h2>Empowering Minds,<br>Shaping Futures.</h2>
                <p>Your classes, notes, assignments, quizzes and results in one place.</p>
            </div>
            <small>&copy; {{ date('Y') }} TT Metro Campus</small>
        </aside>

        <main class="auth-main">
            <div class="auth-card">
                <a href="https://ttmetrocampus.com/" class="auth-mobile-brand" target="_blank" rel="noopener">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="TT Metro Campus"> <b>TT METRO CAMPUS</b>
                </a>
                @hasSection('icon')
                    <div class="auth-icon"><i class="bi bi-@yield('icon')"></i></div>
                @endif
                <h1>@yield('heading', 'Sign in')</h1>
                @hasSection('subheading')<p class="auth-sub">@yield('subheading')</p>@endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-octagon"></i><div>{{ $errors->first() }}</div></div>
                @endif
                @if (session('success') || session('status'))
                    <div class="alert alert-success" role="status"><i class="bi bi-check-circle"></i><div>{{ session('success') ?? session('status') }}</div></div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-octagon"></i><div>{{ session('error') }}</div></div>
                @endif

                @yield('content')

                @hasSection('footer')<div class="auth-foot">@yield('footer')</div>@endif
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show / hide a password field: <i class="bi bi-eye" data-toggle-password="fieldId">
        document.querySelectorAll('[data-toggle-password]').forEach(function (icon) {
            icon.addEventListener('click', function () {
                var input = document.getElementById(icon.dataset.togglePassword);
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                icon.classList.toggle('bi-eye', !show);
                icon.classList.toggle('bi-eye-slash', show);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
