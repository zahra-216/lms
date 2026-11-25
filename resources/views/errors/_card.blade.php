@php
    // One friendly page for every error. Stand-alone on purpose (no layout, no database, no session), so it still
    // works when the error is a server problem.
    $code = isset($exception) && method_exists($exception, 'getStatusCode') ? (int) $exception->getStatusCode() : 500;
    $raw = isset($exception) ? trim((string) $exception->getMessage()) : '';
    $path = request()->path();

    if (str_starts_with($path, 'admin')) {
        $home = url('/admin/dashboard'); $homeLabel = 'Go to the admin dashboard';
    } elseif (str_starts_with($path, 'lecturer')) {
        $home = url('/lecturer/dashboard'); $homeLabel = 'Go to my dashboard';
    } else {
        $home = url('/dashboard'); $homeLabel = 'Go to my dashboard';
    }

    $retry = false;
    $hint = 'If this keeps happening, please contact the campus office.';

    switch (true) {
        case $code === 401:
            $title = 'Please sign in'; $text = 'You need to sign in to open this page.';
            $home = url('/login'); $homeLabel = 'Go to sign in';
            break;
        case $code === 403:
            $title = "You can't open this page";
            $text = ($raw !== '' && strtolower($raw) !== 'forbidden') ? $raw : 'Your account does not have permission to open this page.';
            $hint = 'If you think this is a mistake, please contact the campus office. If you were moved to a new semester recently, open your dashboard and choose the module from there.';
            break;
        case $code === 404:
            if (str_starts_with($raw, 'This file')) {
                $title = 'This file is not available'; $text = $raw;
                $hint = 'Use Go back to return to the module page.';
            } else {
                $title = 'Page not found';
                $text = 'The page you are looking for does not exist, or it has been removed.';
                $hint = 'If you followed a link or a bookmark, it may be old. Use the button below to start again.';
            }
            break;
        case $code === 405:
            $title = "This address can't be opened directly";
            $text = str_contains($path, 'logout')
                ? 'To sign out, please use the Sign out button inside the portal.'
                : 'Please go back and use the buttons on the page instead of typing this address.';
            $hint = '';
            break;
        case $code === 413:
            $title = 'The file is too big'; $text = 'The file you chose is larger than the portal accepts. Please choose a smaller file and try again.';
            $hint = 'If you need to send a large file, please contact the campus office.';
            break;
        case $code === 419:
            $title = 'Your session ended'; $text = 'This page was open for too long. Please sign in again and repeat what you were doing.';
            $home = str_starts_with($path, 'admin') ? url('/admin/login') : (str_starts_with($path, 'lecturer') ? url('/lecturer/login') : url('/login'));
            $homeLabel = 'Go to sign in'; $hint = '';
            break;
        case $code === 429:
            $title = 'Too many tries'; $text = 'Please wait a minute and try again.'; $retry = true; $hint = '';
            break;
        case $code === 503:
            $title = "We'll be back soon"; $text = 'The portal is being updated. Please try again in a few minutes.'; $retry = true;
            break;
        case $code >= 500:
            $title = 'Something went wrong on our side'; $text = 'Please try again in a moment. Your work on other pages is safe.'; $retry = true;
            $hint = 'If it keeps happening, please tell the campus office what you were doing when it happened.';
            break;
        default:
            $title = "This page can't be shown"; $text = 'Please go back and try again.';
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title }} | TT Metro Campus LMS</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #f1f5f9; color: #0f172a; font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif; display: flex; flex-direction: column; }
        .bar { background: #012147; color: #fff; padding: 12px 20px; display: flex; align-items: center; gap: 12px; }
        .bar img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: #fff; }
        .bar b { display: block; font-size: 15px; letter-spacing: .03em; }
        .bar small { display: block; font-size: 12px; color: #93c5fd; letter-spacing: .08em; }
        main { flex: 1; display: grid; place-items: center; padding: 28px 16px; }
        .card { width: 100%; max-width: 560px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 32px 26px; text-align: center; box-shadow: 0 2px 10px rgba(1, 33, 71, .06); }
        .code { display: inline-block; min-width: 84px; padding: 8px 18px; border-radius: 999px; background: #e6f0ff; color: #0a3d91; font-weight: 800; font-size: 22px; letter-spacing: .04em; margin-bottom: 16px; }
        h1 { margin: 0 0 12px; font-size: 24px; line-height: 1.25; color: #012147; }
        p { margin: 0 0 12px; color: #475569; font-size: 16px; line-height: 1.55; }
        .hint { font-size: 14px; color: #64748b; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 22px; }
        .btn { display: inline-block; border-radius: 12px; padding: 11px 20px; font-size: 15px; font-weight: 700; text-decoration: none; cursor: pointer; border: 1px solid #012147; font-family: inherit; }
        .btn-primary { background: #012147; color: #fff; }
        .btn-light { background: #fff; color: #012147; }
        .btn:hover { opacity: .9; }
        .contact { margin-top: 20px; padding: 12px 14px; background: #f1f5f9; border-radius: 10px; font-size: 14px; color: #334155; overflow-wrap: anywhere; }
        .contact a { color: #0a3d91; font-weight: 600; }
        @media (max-width: 420px) { .card { padding: 26px 18px; } h1 { font-size: 21px; } .btn { width: 100%; text-align: center; } }
    </style>
</head>
<body>
    <div class="bar">
        <img src="{{ asset('images/logo.jpeg') }}" alt="">
        <span><b>TT METRO CAMPUS</b><small>LEARNING PORTAL</small></span>
    </div>
    <main>
        <div class="card" role="alert">
            <div class="code">{{ $code }}</div>
            <h1>{{ $title }}</h1>
            <p>{{ $text }}</p>
            @if ($hint !== '')<p class="hint">{{ $hint }}</p>@endif
            <div class="actions">
                @if ($retry)<button type="button" class="btn btn-primary" onclick="location.reload()">Try again</button>@endif
                <a href="{{ $home }}" class="btn {{ $retry ? 'btn-light' : 'btn-primary' }}">{{ $homeLabel }}</a>
                <button type="button" class="btn btn-light" onclick="if (history.length > 1) { history.back(); } else { location.href = '{{ $home }}'; }">Go back</button>
            </div>
            <div class="contact">Need help? Email the campus office: <a href="mailto:info.ttmetro@gmail.com">info.ttmetro@gmail.com</a></div>
        </div>
    </main>
</body>
</html>
