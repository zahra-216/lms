@extends('layouts.admin')
@section('title')
YouTube Connection
@endsection
@section('back', route('admin.zoom.index'))
@push('styles')
<style>
.yc-field { margin-bottom: 16px; }
.yc-where { display:inline-block; font-size:12px; font-weight:700; border-radius:999px; padding:2px 10px; margin-left:8px; }
.yc-where.on { background:#dcfce7; color:#166534; }
.yc-where.off { background:#fee2e2; color:#991b1b; }
.yc-steps { padding-left: 1.2rem; }
.yc-steps li { margin-bottom: 10px; overflow-wrap: anywhere; }
.yc-copy { display:flex; gap:8px; }
.yc-copy input { min-width:0; }
.yc-secret { position:relative; }
.yc-secret .yc-eye { position:absolute; right:8px; top:50%; transform:translateY(-50%); border:0; background:none; color:#64748b; padding:4px 8px; }
</style>
@endpush
@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-youtube"></i> YouTube Connection</h2>
            <small>Link the campus YouTube channel, so class recordings can be uploaded to it</small>
        </div>
    </div>

    @if($connected)
        <div class="alert alert-success rounded-3"><i class="bi bi-check-circle"></i> <div>Connected to the YouTube channel <strong>{{ $channel }}</strong>. Use <strong>Send a test video</strong> below to check that uploading works.</div></div>
    @elseif($hasKeys)
        <div class="alert alert-warning rounded-3"><i class="bi bi-exclamation-triangle"></i> <div>The keys are saved, but YouTube is not connected yet. Click <strong>Connect YouTube</strong>.</div></div>
    @else
        <div class="alert alert-warning rounded-3"><i class="bi bi-exclamation-triangle"></i> <div>YouTube is not connected yet. Follow the steps at the bottom of this page, then enter the two keys here.</div></div>
    @endif

    @if($test)
        <div class="alert {{ $test['ok'] ? 'alert-success' : 'alert-danger' }} rounded-3">
            <div>
                <strong>{{ $test['ok'] ? 'Test passed.' : 'Test failed.' }}</strong> {{ $test['message'] }}
                @if(! empty($test['url']))<div class="mt-1"><a href="{{ $test['url'] }}" target="_blank" rel="noopener" class="alert-link">{{ $test['url'] }}</a> - delete it afterwards in YouTube Studio.</div>@endif
            </div>
        </div>
    @endif

    <div class="card-box">
        <h3 class="section-title"><i class="bi bi-key text-primary"></i> Google keys</h3>
        <p class="text-muted">You get these from Google (steps below). The secret is never shown again after saving: leave its box empty to keep it.</p>
        <form method="POST" action="{{ route('admin.youtube.save') }}" autocomplete="off">
            @csrf
            <div class="yc-field">
                <label class="form-label fw-semibold" for="yc_client_id">Client ID<span class="yc-where {{ $clientId ? 'on' : 'off' }}">{{ $clientId ? 'Saved' : 'Missing' }}</span></label>
                <input type="text" name="client_id" id="yc_client_id" class="form-control @error('client_id') is-invalid @enderror" value="{{ old('client_id', $clientId) }}" maxlength="200" autocomplete="off" spellcheck="false">
                @error('client_id')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="yc-field">
                <label class="form-label fw-semibold" for="yc_client_secret">Client Secret<span class="yc-where {{ $hasSecret ? 'on' : 'off' }}">{{ $hasSecret ? 'Saved' : 'Missing' }}</span></label>
                <div class="yc-secret">
                    <input type="password" name="client_secret" id="yc_client_secret" class="form-control @error('client_secret') is-invalid @enderror" value="{{ old('client_secret') }}" maxlength="200" autocomplete="off" spellcheck="false"
                           placeholder="{{ $hasSecret ? 'Saved - leave empty to keep' : 'Paste it here' }}">
                    <button type="button" class="yc-eye" id="ycEye" aria-label="Show or hide"><i class="bi bi-eye"></i></button>
                </div>
                @error('client_secret')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i> Save</button>
        </form>

        <div class="d-flex gap-2 flex-wrap mt-3">
            @if($hasKeys)
                <a href="{{ route('admin.youtube.connect') }}" class="btn btn-outline-primary"><i class="bi bi-youtube"></i> {{ $connected ? 'Connect again' : 'Connect YouTube' }}</a>
            @endif
            @if($connected)
                <form method="POST" action="{{ route('admin.youtube.disconnect') }}" data-confirm="Disconnect YouTube? Recordings will not be uploaded until you connect it again.">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-x-circle"></i> Disconnect</button>
                </form>
            @endif
        </div>
    </div>

    @if($connected)
    <div class="card-box">
        <h3 class="section-title"><i class="bi bi-cloud-upload text-primary"></i> Send a test video</h3>
        <p class="text-muted">Choose a very short video (a few seconds, 50 MB at most). It is uploaded to the channel as <strong>Unlisted</strong>, to prove that uploading works. Delete it afterwards in YouTube Studio.</p>
        <form method="POST" action="{{ route('admin.youtube.test') }}" enctype="multipart/form-data" id="ycTest">
            @csrf
            <input type="file" name="video" accept="video/mp4,video/quicktime,video/webm,.mp4,.mov,.webm,.avi,.mkv" class="form-control mb-2 @error('video') is-invalid @enderror" required>
            @error('video')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
            <button type="submit" class="btn btn-outline-primary" id="ycTestBtn"><i class="bi bi-upload"></i> Send test video</button>
            <span class="text-muted small ms-2" id="ycWait" hidden>Uploading... please wait.</span>
        </form>
    </div>
    @endif

    <div class="card-box">
        <h3 class="section-title"><i class="bi bi-diagram-3 text-primary"></i> Redirect address for Google</h3>
        <p class="text-muted">Paste this into Google (step 4 below), exactly as it is.</p>
        <div class="yc-copy">
            <input type="text" class="form-control" id="ycHook" value="{{ $redirectUri }}" readonly>
            <button type="button" class="btn btn-outline-primary" id="ycCopy"><i class="bi bi-clipboard"></i> Copy</button>
        </div>
    </div>

    <div class="card-box">
        <h3 class="section-title"><i class="bi bi-list-check text-primary"></i> How to get the two keys from Google</h3>
        <p class="text-muted">Use the campus Google account that owns the campus YouTube channel. Google's screens change names now and then, so the wording may differ a little.</p>
        <ol class="yc-steps">
            <li>Open <strong>console.cloud.google.com</strong> and sign in. If it asks, accept the terms. Click the project name at the top, then <strong>New project</strong>. Name it "TT Metro LMS" and click <strong>Create</strong>, then make sure it is the selected project.</li>
            <li>Open the menu, then <strong>APIs &amp; Services</strong>, then <strong>Library</strong>. Search for <strong>YouTube Data API v3</strong>, open it and click <strong>Enable</strong>.</li>
            <li>Go to <strong>OAuth consent screen</strong> (or <strong>Google Auth Platform</strong>). Click <strong>Get started</strong>. App name: "TT Metro LMS". Support email: the campus Gmail. Audience: <strong>External</strong>. Contact email: the campus Gmail. Accept and click <strong>Create</strong>.</li>
            <li>Go to <strong>Clients</strong> (or <strong>Credentials</strong>), click <strong>Create client</strong> (or <strong>Create credentials</strong>, then <strong>OAuth client ID</strong>). Application type: <strong>Web application</strong>. Under <strong>Authorized redirect URIs</strong> click <strong>Add URI</strong> and paste the address from the box above. Click <strong>Create</strong>. Copy the <strong>Client ID</strong> and the <strong>Client secret</strong> into the boxes at the top of this page and press <strong>Save</strong>.</li>
            <li><strong>Important:</strong> go to <strong>Audience</strong> and click <strong>Publish app</strong> (status "In production"). If you leave it on "Testing", Google ends the YouTube sign-in after 7 days and uploads stop.</li>
            <li>Come back here and click <strong>Connect YouTube</strong>. Sign in with the account that owns the channel. Google may say "Google hasn't verified this app": click <strong>Advanced</strong>, then <strong>Go to TT Metro LMS (unsafe)</strong>. It is your own app. Tick the boxes that let it upload and see the channel, and click <strong>Continue</strong>.</li>
            <li>When you are back here and it shows the channel name, send a test video.</li>
        </ol>
        <div class="alert alert-warning rounded-3 mb-0">
            <div>
                <strong>Videos longer than 15 minutes:</strong> YouTube only accepts longer videos from a verified channel. Open <strong>youtube.com/verify</strong> while signed in to the campus account and confirm the phone number.
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function () {
    document.getElementById('ycEye').addEventListener('click', function () {
        var i = document.getElementById('yc_client_secret');
        i.type = i.type === 'password' ? 'text' : 'password';
    });
    document.getElementById('ycCopy').addEventListener('click', function () {
        var i = document.getElementById('ycHook');
        i.select();
        try { navigator.clipboard.writeText(i.value); } catch (e) { document.execCommand('copy'); }
        this.innerHTML = '<i class="bi bi-check2"></i> Copied';
    });
    var t = document.getElementById('ycTest');
    if (t) t.addEventListener('submit', function () {
        document.getElementById('ycTestBtn').disabled = true;
        document.getElementById('ycWait').hidden = false;
    });
})();
</script>
@endpush
