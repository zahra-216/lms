@extends('layouts.public')
@section('title')
Welcome
@endsection

@section('content')
<section class="hero-public">
    <div class="pub-wrap position-relative" style="z-index:1">
        <span class="chip mb-3" style="background:rgba(255,255,255,.14);color:#fff;display:inline-flex;padding:6px 14px;border-radius:999px;font-weight:700;font-size:12px;letter-spacing:.1em;text-transform:uppercase">TT Metro Campus Learning Portal</span>
        <h1>Empowering Minds,<br>Shaping Futures.</h1>
        <p class="mt-3">Your classes, notes, videos, assignments, quizzes and results, all in one place. Sign in to continue learning.</p>
        <div class="d-flex flex-wrap gap-2 mt-4">
            @if (session()->has('student_id'))
                <a href="{{ route('dashboard') }}" class="add-btn px-4 py-3"><i class="bi bi-speedometer2"></i> Go to my dashboard</a>
            @else
                <a href="{{ route('login') }}" class="add-btn px-4 py-3"><i class="bi bi-box-arrow-in-right"></i> Student login</a>
            @endif
            <a href="{{ route('verify.show') }}" class="btn btn-outline-light px-4 py-3"><i class="bi bi-patch-check"></i> Verify a certificate</a>
        </div>
    </div>
</section>

<section class="pub-wrap" style="margin-top:-48px;position:relative;z-index:2">
    <div class="row g-4">
        <div class="col-md-4">
            <a href="{{ route('login') }}" class="portal-card">
                <div class="stat-icon mb-3"><i class="bi bi-mortarboard"></i></div>
                <h3>Students</h3>
                <p>Open your subjects, notes and lecture videos, hand in assignments, take quizzes and check your grades.</p>
                <span class="go">Student login <i class="bi bi-arrow-right"></i></span>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('lecturer.login') }}" class="portal-card">
                <div class="stat-icon mb-3"><i class="bi bi-person-workspace"></i></div>
                <h3>Lecturers</h3>
                <p>Share notes and recordings, set assignments and quizzes, mark work and keep lecture records.</p>
                <span class="go">Lecturer login <i class="bi bi-arrow-right"></i></span>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.login') }}" class="portal-card">
                <div class="stat-icon mb-3"><i class="bi bi-shield-lock"></i></div>
                <h3>Administration</h3>
                <p>Manage students, lecturers, courses, attendance, payments and certificates.</p>
                <span class="go">Admin login <i class="bi bi-arrow-right"></i></span>
            </a>
        </div>
    </div>
</section>
@endsection
