@extends('layouts.admin')
@section('title')
Zoom Connection
@endsection
@section('back', route('admin.zoom.index'))
@push('styles')
<style>
.zc-field { margin-bottom: 16px; }
.zc-where { display:inline-block; font-size:12px; font-weight:700; border-radius:999px; padding:2px 10px; margin-left:8px; }
.zc-where.admin { background:#dcfce7; color:#166534; }
.zc-where.server { background:#e0f2fe; color:#075985; }
.zc-where.none { background:#fee2e2; color:#991b1b; }
.zc-steps { padding-left: 1.2rem; }
.zc-steps li { margin-bottom: 10px; overflow-wrap: anywhere; }
.zc-scopes code { display:block; margin:2px 0; white-space:normal; overflow-wrap:anywhere; word-break:break-all; }
.zc-copy { display:flex; gap:8px; }
.zc-copy input { min-width:0; }
.zc-secret { position:relative; }
.zc-secret .zc-eye { position:absolute; right:8px; top:50%; transform:translateY(-50%); border:0; background:none; color:#64748b; padding:4px 8px; }
</style>
@endpush
@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-plug"></i> Zoom Connection</h2>
            <small>Choose which Zoom account the LMS uses to create classes and read attendance</small>
        </div>
    </div>

    @if($fake)
        <div class="alert alert-info rounded-3">Testing mode: Zoom is pretended. Nothing is sent to the real Zoom.</div>
    @elseif($connected)
        <div class="alert alert-success rounded-3"><i class="bi bi-check-circle"></i> <div>The Zoom keys are filled in. Press <strong>Test connection</strong> to check that Zoom accepts them.</div></div>
    @else
        <div class="alert alert-warning rounded-3"><i class="bi bi-exclamation-triangle"></i> <div>Zoom is not connected yet. Fill in the boxes below, save, then press <strong>Test connection</strong>.</div></div>
    @endif

    @if($test)
        <div class="alert {{ $test['ok'] ? 'alert-success' : 'alert-danger' }} rounded-3">
            <div>
                <strong>{{ $test['ok'] ? 'Test passed.' : 'Test failed.' }}</strong> {{ $test['message'] }}
                @foreach($test['notes'] as $note)<div class="mt-1">{{ $note }}</div>@endforeach
            </div>
        </div>
    @endif

    <div class="card-box">
        <h3 class="section-title"><i class="bi bi-key text-primary"></i> Zoom account details</h3>
        <p class="text-muted">You get these from the Zoom app you create for the account (steps below). Secret boxes are never shown again after saving: leave a box empty to keep what is saved.</p>

        <form method="POST" action="{{ route('admin.zoom-connection.save') }}" id="zcForm" autocomplete="off">
            @csrf
            @php
                $rows = [
                    'account_id' => ['Account ID', 'text', 'From the Zoom app: App Credentials page.'],
                    'client_id' => ['Client ID', 'text', 'From the Zoom app: App Credentials page.'],
                    'client_secret' => ['Client Secret', 'password', 'From the Zoom app: App Credentials page. Secret.'],
                    'webhook_secret' => ['Secret Token (for attendance messages)', 'password', 'From the Zoom app: Feature page, Event Subscriptions. Secret.'],
                    'host' => ['Zoom host email', 'email', 'The email you sign in to Zoom with. Classes are created under this user.'],
                ];
                $words = ['admin' => 'Saved here', 'server' => 'From the server file', 'none' => 'Missing'];
            @endphp
            @foreach($rows as $key => [$label, $type, $hint])
                @php $f = $fields[$key]; @endphp
                <div class="zc-field">
                    <label class="form-label fw-semibold" for="zc_{{ $key }}">{{ $label }}<span class="zc-where {{ $f['source'] }}">{{ $words[$f['source']] }}</span></label>
                    <div class="{{ $f['secret'] ? 'zc-secret' : '' }}">
                        <input type="{{ $type }}" name="{{ $key }}" id="zc_{{ $key }}" class="form-control @error($key) is-invalid @enderror"
                               value="{{ old($key, $f['value']) }}" data-original="{{ $f['value'] }}" maxlength="200" autocomplete="off" spellcheck="false"
                               placeholder="{{ $f['secret'] ? ($f['source'] === 'none' ? 'Paste it here' : 'Saved - leave empty to keep') : '' }}">
                        @if($f['secret'])<button type="button" class="zc-eye" data-eye="zc_{{ $key }}" aria-label="Show or hide"><i class="bi bi-eye"></i></button>@endif
                    </div>
                    <div class="form-text">{{ $hint }}</div>
                    @error($key)<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
            @endforeach

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i> Save</button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.zoom-connection.test') }}" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-plug"></i> Test connection</button>
            <span class="text-muted small ms-2">Checks the saved details with Zoom. Nothing is created.</span>
        </form>
    </div>

    <div class="card-box">
        <h3 class="section-title"><i class="bi bi-diagram-3 text-primary"></i> Address for Zoom to send "class ended" messages</h3>
        <p class="text-muted">Paste this into the Zoom app (step 5 below). It is the same for every Zoom account.</p>
        <div class="zc-copy">
            <input type="text" class="form-control" id="zcHook" value="{{ $webhookUrl }}" readonly>
            <button type="button" class="btn btn-outline-primary" id="zcCopy"><i class="bi bi-clipboard"></i> Copy</button>
        </div>
    </div>

    <div class="card-box">
        <h3 class="section-title"><i class="bi bi-list-check text-primary"></i> How to connect a new Zoom account</h3>
        <ol class="zc-steps">
            <li>Sign in at <strong>marketplace.zoom.us</strong> with the Zoom account that will hold the classes (an owner or admin of that account).</li>
            <li>Click <strong>Develop</strong>, then <strong>Build App</strong>, choose <strong>Server-to-Server OAuth App</strong> and click <strong>Create</strong>. Name it "TTMC LMS".</li>
            <li>On the <strong>App Credentials</strong> page copy the <strong>Account ID</strong>, <strong>Client ID</strong> and <strong>Client Secret</strong> into the boxes above.</li>
            <li>On the <strong>Feature</strong> page copy the <strong>Secret Token</strong> into the box above. Type the Zoom host email, then press <strong>Save</strong> here first.</li>
            <li>Still on the <strong>Feature</strong> page, turn on <strong>Event Subscriptions</strong>, click <strong>Add new event subscription</strong>, paste the address from the box above, and add two events: <strong>Meeting - End Meeting</strong> (for attendance) and <strong>Recording - All Recordings have completed</strong> (for class recordings). Click <strong>Validate</strong>, then <strong>Save</strong>.</li>
            <li>On the <strong>Scopes</strong> page add these permissions (Zoom shows them in its own wording):
                <div class="zc-scopes mt-2">
                    <code>meeting:write:meeting:admin</code>
                    <code>meeting:update:meeting:admin</code>
                    <code>meeting:delete:meeting:admin</code>
                    <code>meeting:read:meeting:admin</code>
                    <code>meeting:read:past_meeting:admin</code>
                    <code>report:read:list_meeting_participants:admin</code>
                    <code>cloud_recording:read:...</code> <span class="text-muted small">(for class recordings: in the Scopes search box type <strong>recording</strong> and tick every permission that reads or views recordings, including the one that lets the app download them)</span>
                    <code>user:read:user:admin</code> <span class="text-muted small">(optional: lets the test check the host email)</span>
                </div>
            </li>
            <li>On the <strong>Activation</strong> page click <strong>Activate your app</strong>.</li>
            <li>Come back here and press <strong>Test connection</strong>. When it passes, create a Zoom class to try it.</li>
        </ol>
        <div class="alert alert-warning rounded-3 mb-0">
            <div>
                <strong>Before switching to a different Zoom account:</strong> classes made with the old account can no longer be read after the switch, because Zoom only shows a class to the account that made it.
                Let every old class end and check its attendance first.
                @if($pending > 0)<br><strong>Right now {{ $pending }} class(es) are not finished processing.</strong><br>@endif
                Reading attendance also needs a Zoom plan that includes reports (Pro or higher).
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
(function () {
    var PENDING = {{ (int) $pending }};
    var form = document.getElementById('zcForm');

    document.querySelectorAll('[data-eye]').forEach(function (b) {
        b.addEventListener('click', function () {
            var i = document.getElementById(b.dataset.eye);
            i.type = i.type === 'password' ? 'text' : 'password';
        });
    });

    document.getElementById('zcCopy').addEventListener('click', function () {
        var i = document.getElementById('zcHook');
        i.select();
        try { navigator.clipboard.writeText(i.value); } catch (e) { document.execCommand('copy'); }
        this.innerHTML = '<i class="bi bi-check2"></i> Copied';
    });

    // changing the account while classes are unfinished: ask first
    form.addEventListener('submit', function (e) {
        var changed = ['zc_account_id', 'zc_client_id'].some(function (id) {
            var i = document.getElementById(id);
            return i.value.trim() !== '' && i.value.trim() !== i.dataset.original;
        });
        if (changed && document.getElementById('zc_account_id').dataset.original !== '' && PENDING > 0 &&
            !confirm('You are changing the Zoom account while ' + PENDING + ' class(es) are not finished. Their attendance may no longer be readable. Continue?')) {
            e.preventDefault();
        }
    });
})();
</script>
@endpush
