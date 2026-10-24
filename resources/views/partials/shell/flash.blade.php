{{-- Success / error messages from controllers. One place, every page. --}}
@php
    $flashes = collect([
        'success' => session('success') ?? session('status'),
        'danger' => session('error'),
        'warning' => session('warning'),
        'info' => session('info'),
    ])->filter();
@endphp
@if ($flashes->isNotEmpty() || $errors->any())
    <div class="container lms-flash">
        @foreach ($flashes as $type => $message)
            <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert" data-lms-flash data-flash-type="{{ $type }}">
                <i class="bi bi-{{ $type === 'success' ? 'check-circle' : ($type === 'danger' ? 'exclamation-octagon' : 'info-circle') }}"></i>
                <div class="flex-grow-1">{{ $message }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endforeach
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert" data-lms-flash>
                <i class="bi bi-exclamation-octagon"></i>
                <div class="flex-grow-1">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
@endif
