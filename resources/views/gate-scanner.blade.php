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
    #result {
        max-width:420px; margin:20px auto; padding:24px; border-radius:16px;
        font-size:20px; font-weight:700; min-height:80px;
        display:flex; flex-direction:column; align-items:center; justify-content:center;
        transition:background .2s;
    }
    .result-idle { background:#0a2452; }
    .result-in { background:#065f46; }
    .result-out { background:#92400e; }
    .result-error { background:#991b1b; }
    .result-sub { font-size:14px; font-weight:400; margin-top:6px; opacity:0.85; }
</style>
</head>
<body>
    <h2><i class="bi bi-qr-code-scan"></i> Gate Scanner</h2>
    <small>{{ $device->name }}</small>

    <div id="reader"></div>
    <div id="result" class="result-idle">Ready — scan a card</div>

<script>
const deviceKey = "{{ $deviceKey }}";
const scanUrl = "{{ route('gate-scanner.scan', $deviceKey) }}";
const resultBox = document.getElementById('result');
let busy = false;

function showResult(cls, main, sub) {
    resultBox.className = cls;
    resultBox.innerHTML = `<div>${main}</div><div class="result-sub">${sub}</div>`;
    setTimeout(() => {
        resultBox.className = 'result-idle';
        resultBox.innerHTML = 'Ready — scan a card';
    }, 3000);
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
            const cls = data.scan_type === 'in' ? 'result-in' : 'result-out';
            const label = data.scan_type === 'in' ? 'CHECKED IN' : 'CHECKED OUT';
            showResult(cls, `${data.name} — ${label}`, data.time);
        } else {
            showResult('result-error', 'Not recorded', data.message);
        }
    } catch (e) {
        showResult('result-error', 'Connection error', 'Try again');
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