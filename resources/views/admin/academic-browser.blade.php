@extends('layouts.admin')
@section('title')
{{ $title }}
@endsection

@push('styles')
<style>
    .crumbs { display:flex; flex-wrap:wrap; align-items:center; gap:8px; margin-bottom:18px; font-size:14px; font-weight:600; }
    .crumbs a { color:var(--brand-700); text-decoration:none; background:var(--sky-soft); padding:5px 12px; border-radius:999px; }
    .crumbs span.cur { background:var(--brand-700); color:#fff; padding:5px 12px; border-radius:999px; }
    .crumbs i { color:var(--muted); font-size:12px; }
    .pick-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(250px, 1fr)); gap:20px; }
    .pick-card { display:flex; flex-direction:column; background:#fff; border:1px solid var(--line); border-radius:var(--radius); overflow:hidden; text-decoration:none; color:inherit; box-shadow:var(--shadow-card); transition:transform .18s, box-shadow .18s; height:100%; }
    .pick-card:hover { transform:translateY(-4px); box-shadow:var(--shadow-lift); color:inherit; }
    .pick-img { height:150px; background-size:cover; background-position:center; }
    .pick-body { padding:18px 20px; display:flex; gap:14px; align-items:center; flex:1; }
    .pick-icon { width:46px; height:46px; border-radius:14px; background:var(--sky-soft); color:var(--brand-700); display:grid; place-items:center; font-size:21px; flex-shrink:0; }
    .pick-title { font-family:'Plus Jakarta Sans',sans-serif; font-weight:800; font-size:16.5px; color:var(--navy-900); line-height:1.25; }
    .pick-sub { color:var(--muted); font-size:13px; margin-top:2px; }
    .pick-go { margin-left:auto; color:var(--brand-600); font-size:18px; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-{{ $icon }}"></i> {{ $title }}</h2>
            <small>{{ $subtitle }}</small>
        </div>
    </div>

    <form method="GET" action="{{ url()->current() }}" class="row g-2 mb-3">
        <div class="col">
            <input type="search" name="q" value="{{ $tree['q'] }}" class="form-control" placeholder="Search any subject by name or code...">
        </div>
        <div class="col-auto"><button class="btn-navy" type="submit"><i class="bi bi-search"></i> Search</button></div>
        @if ($tree['searching'])<div class="col-auto"><a href="{{ url()->current() }}" class="btn btn-outline-secondary">Clear</a></div>@endif
    </form>

    <nav class="crumbs" aria-label="Where you are">
        @foreach ($tree['crumbs'] as $i => [$label, $link])
            @if ($i > 0)<i class="bi bi-chevron-right"></i>@endif
            @if ($link && ! $loop->last)<a href="{{ $link }}">{{ $label }}</a>@else<span class="cur">{{ $label }}</span>@endif
        @endforeach
    </nav>

    <h3 class="section-title">{{ $tree['heading'] }}</h3>

    @if (count($tree['cards']))
        <div class="pick-grid">
            @foreach ($tree['cards'] as $card)
                <a href="{{ $card['url'] }}" class="pick-card">
                    @if ($card['img'])<div class="pick-img" style="background-image:url('{{ $card['img'] }}')"></div>@endif
                    <div class="pick-body">
                        @unless ($card['img'])<div class="pick-icon"><i class="bi bi-{{ $card['icon'] }}"></i></div>@endunless
                        <div>
                            <div class="pick-title">{{ $card['title'] }}</div>
                            <div class="pick-sub">{{ $card['sub'] }}</div>
                        </div>
                        <i class="bi bi-arrow-right pick-go"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-state"><i class="bi bi-inbox"></i>Nothing to show here yet.</div>
    @endif
</div>
@endsection
