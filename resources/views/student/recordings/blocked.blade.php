@extends('layouts.student')
@section('title')
Recordings hidden
@endsection
@push('styles')
<style>
.navy-header { background: #012147; color: #fff; padding: 20px; }
.rec-blocked { max-width: 640px; margin: 0 auto; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 28px 24px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.rec-blocked .icon { width: 64px; height: 64px; border-radius: 50%; background: #fee2e2; color: #b91c1c; display: inline-grid; place-items: center; font-size: 30px; margin-bottom: 14px; }
.rec-blocked h5 { font-weight: 800; color: #012147; margin-bottom: 10px; }
.rec-blocked p { color: #475569; margin-bottom: 10px; text-align: center; }
.rec-blocked .contact { margin-top: 16px; padding: 12px 14px; background: #f1f5f9; border-radius: 10px; font-size: 14.5px; overflow-wrap: anywhere; }
</style>
@endpush
@section('content')
    <div class="navy-header">
        <div class="container">
            <h4 class="mt-2 mb-0">Class recordings</h4>
        </div>
    </div>

    <div class="container my-4">
        <div class="rec-blocked">
            <div class="icon"><i class="bi bi-lock-fill"></i></div>
            <h5>Recordings are hidden for your account</h5>
            <p>
                You cannot watch class recordings right now, because your student status is
                <strong>{{ $status === 'dropped' ? 'Dropped Out' : 'Inactive' }}</strong>.
                The rest of the portal works as usual.
            </p>
            <p>If you think this is a mistake, please contact the campus admin office and ask them to check your student status.</p>
            <div class="contact"><i class="bi bi-envelope"></i> Email: <a href="mailto:info.ttmetro@gmail.com">info.ttmetro@gmail.com</a></div>
        </div>
    </div>
@endsection
