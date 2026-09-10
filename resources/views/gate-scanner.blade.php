<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gate Scanner — {{ $device->name }}</title>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<style>
    * { box-sizing:border-box; }
    body {
        background:#012147; font-family:'Segoe UI', sans-serif; color:#fff;
        margin:0; padding:20px; text-align:center; min-height:100vh;
    }
    h2 { margin:10px 0 4px; }
    small { opacity:0.7; }
    #reader { max-width:420px; margin:20px auto; border-radius:16px; overflow:hidden; }
    
    #verifyOverlay {
        position:fixed; inset:0; display:none; align-items:center; justify-content:center;
        flex-direction:column; z-index:999; text-align:center; padding:20px;
    }
    #verifyOverlay.show { display:flex; }
    .verify-check {
        width:90px; height:90px; border-radius:50%; display:flex; align-items:center; justify-content:center;
        font-size:46px; margin-bottom:20px; animation:pop .3s ease;
    }
    @keyframes pop { from{ transform:scale(0.5); opacity:0; } to{ transform:scale(1); opacity:1; } }
    .verify-name { font-size:24px; font-weight:700; margin-bottom:6px; }
    .verify-status { font-size:16px; font-weight:600; opacity:0.9; margin-bottom:4px; }
    .verify-time { font-size:14px; opacity:0.7; }
</style>

</head>
<body>
    <div id="verifyOverlay">
        <div class="verify-check" id="verifyIcon"></div>
        <div class="verify-name" id="verifyName"></div>
        <div class="verify-status" id="verifyStatus"></div>
        <div class="verify-time" id="verifyTime"></div>
    </div>

    <h2><i class="bi bi-qr-code-scan"></i> Gate Scanner</h2>
    <small>{{ $device->name }}</small>

    <div id="reader"></div>

<script>
const deviceKey = "{{ $deviceKey }}";
const scanUrl = "{{ route('gate-scanner.scan', $deviceKey) }}";
let busy = false;

const overlay = document.getElementById('verifyOverlay');
const icon = document.getElementById('verifyIcon');
const nameEl = document.getElementById('verifyName');
const statusEl = document.getElementById('verifyStatus');
const timeEl = document.getElementById('verifyTime');

function showOverlay(success, main, sub, time) {
    overlay.style.background = success ? '#065f46' : '#991b1b';
    icon.style.background = 'rgba(255,255,255,0.15)';
    icon.innerHTML = success ? '✓' : '✕';
    icon.style.color = '#fff';
    nameEl.textContent = main;
    statusEl.textContent = sub;
    timeEl.textContent = time || '';
    overlay.classList.add('show');

    setTimeout(() => {
        overlay.classList.remove('show');
    }, 2000);
}

async function onScanSuccess(decodedText) {
    if (busy) return;
    busy = true;

    try {
        const res = await fetch(scanUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ token: decodedText })
        });
        const data = await res.json();

        if (data.success) {
            const label = data.scan_type === 'in' ? 'CHECKED IN' : 'CHECKED OUT';
            showOverlay(true, data.name, label, data.time);
        } else {
            showOverlay(false, 'Not Verified', data.message, '');
        }
    } catch (e) {
        showOverlay(false, 'Connection Error', 'Try again', '');
    }

    setTimeout(() => { busy = false; }, 2500);
}

const html5QrCode = new Html5Qrcode("reader");
html5QrCode.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 250 },
    onScanSuccess
);
</script>
</body>
</html>