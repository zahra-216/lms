<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Scan attendance</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<style>
:root {
  --accent:#004b93; --accent-dark:#003a73;
  --ink:#17181c; --muted:#6b6e78; --faint:#9a9da7; --line:#e7e8ec; --paper:#f7f7f5;
  --good:#2a8a57; --bad:#c0392b;
}
* { box-sizing:border-box; }
body { margin:0; background:var(--paper); color:var(--ink); font-family:"DM Sans",system-ui,-apple-system,"Segoe UI",Arial,sans-serif; -webkit-font-smoothing:antialiased; }
h1, h2 { font-family:"Bricolage Grotesque","DM Sans",system-ui,sans-serif; letter-spacing:-0.01em; margin:0; }
.wrap { max-width:460px; margin:0 auto; padding:24px 16px 48px; }
.back { display:inline-flex; align-items:center; gap:6px; margin-bottom:20px; font-size:.875rem; font-weight:500; color:var(--muted); text-decoration:none; }
.back:hover { color:var(--ink); }
h1 { font-size:1.65rem; font-weight:600; line-height:1.2; }
.sub { margin:4px 0 22px; font-size:.875rem; color:var(--muted); }
.card { background:#fff; border:1px solid var(--line); border-radius:16px; padding:20px; }
.card h2 { font-size:1rem; font-weight:600; margin-bottom:14px; }
#reader { border-radius:12px; overflow:hidden; }
.note { display:none; margin-bottom:16px; padding:10px 14px; border-radius:12px; font-size:.875rem; color:var(--bad); background:rgba(192,57,43,.05); border:1px solid rgba(192,57,43,.2); }
.btn { display:flex; align-items:center; justify-content:center; width:100%; padding:.8rem 1rem; border-radius:10px; border:1px solid var(--accent); background:var(--accent); color:#fff; font:inherit; font-size:.875rem; font-weight:600; cursor:pointer; text-decoration:none; }
.btn:hover { background:var(--accent-dark); }
.btn:disabled { opacity:.55; cursor:not-allowed; }
.btn-ghost { background:#fff; color:var(--ink); border-color:var(--line); margin-top:10px; }
.btn-ghost:hover { background:var(--paper); }
.link { display:block; width:100%; margin-top:12px; padding:0; border:0; background:none; font:inherit; font-size:.875rem; color:var(--muted); cursor:pointer; text-align:center; }
dl { margin:0; }
.row { display:flex; justify-content:space-between; gap:16px; padding:11px 0; border-bottom:1px solid var(--line); font-size:.875rem; }
.row:last-child { border-bottom:0; }
.row dt { color:var(--muted); }
.row dd { margin:0; font-weight:600; text-align:right; }
.tick { display:flex; flex-direction:column; align-items:center; padding:4px 0 10px; }
.tick span { display:flex; align-items:center; justify-content:center; width:64px; height:64px; border-radius:50%; background:var(--good); }
.tick h2 { margin:14px 0 0; color:var(--good); }
.hidden { display:none; }
</style>
</head>
<body>
<div class="wrap">
  <a class="back" href="{{ route('admin.gate-log.index') }}">&larr; Back to Gate Log</a>
  <h1>Scan attendance</h1>
  <p class="sub">Scan the student's QR code.</p>

  <div class="note" id="note"></div>

  <div class="card" id="scanView">
    <h2>Point the camera at the QR code</h2>
    <div id="reader"></div>
  </div>

  <div class="card hidden" id="readyView">
    <h2>QR code verified</h2>
    <dl id="readyDetails"></dl>
    <button class="btn" id="markBtn" style="margin-top:18px"></button>
    <button class="link" id="againBtn">Scan again</button>
  </div>

  <div class="card hidden" id="doneView">
    <div class="tick">
      <span>
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
      </span>
      <h2 id="doneTitle"></h2>
    </div>
    <dl id="doneDetails"></dl>
    <button class="btn" id="nextBtn" style="margin-top:18px">Scan next student</button>
    <a class="btn btn-ghost" href="{{ route('admin.gate-log.index') }}">Back to Gate Log</a>
  </div>
</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const verifyUrl = "{{ route('admin.scan.verify') }}";
const markUrl = "{{ route('admin.scan.mark') }}";

const $ = (id) => document.getElementById(id);
const views = ['scanView', 'readyView', 'doneView'];
let scanner = null;
let token = '';
let handled = false;

function show(id) { views.forEach((v) => $(v).classList.toggle('hidden', v !== id)); }
function showNote(msg) { const n = $('note'); n.textContent = msg; n.style.display = 'block'; }
function hideNote() { $('note').style.display = 'none'; }

async function post(url, body) {
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
    body: JSON.stringify(body),
  });
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(data.message || 'Something went wrong. Please refresh the page.');
  return data;
}

function details(el, d) {
  const rows = [['Name', d.name], ['Reg No', d.ref || '-'], ['Course', d.course || '-'], ['Date', d.date], ['Time', d.time]];
  el.innerHTML = '';
  rows.forEach(([k, v]) => {
    const row = document.createElement('div');
    row.className = 'row';
    const dt = document.createElement('dt'); dt.textContent = k;
    const dd = document.createElement('dd'); dd.textContent = v;
    row.append(dt, dd);
    el.append(row);
  });
}

async function startScanner() {
  handled = false;
  hideNote();
  show('scanView');
  scanner = new Html5Qrcode('reader');
  try {
    await scanner.start({ facingMode: 'environment' }, { fps: 10, qrbox: 250 }, onScan);
  } catch (e) {
    showNote('Camera could not start. Please allow camera access.');
  }
}

async function stopScanner() {
  if (!scanner) return;
  try { await scanner.stop(); scanner.clear(); } catch (e) {}
  scanner = null;
}

async function onScan(text) {
  if (handled) return;
  handled = true;
  try {
    const data = await post(verifyUrl, { token: text });
    token = text;
    await stopScanner();
    hideNote();
    details($('readyDetails'), data);
    $('markBtn').textContent = data.next === 'in' ? 'Check In' : 'Check Out';
    show('readyView');
  } catch (e) {
    showNote(e.message);
    setTimeout(() => { handled = false; }, 2500);
  }
}

$('markBtn').addEventListener('click', async () => {
  const btn = $('markBtn');
  btn.disabled = true;
  try {
    const d = await post(markUrl, { token });
    hideNote();
    $('doneTitle').textContent = d.scan_type === 'in' ? 'Check In Successful!' : 'Check Out Successful!';
    details($('doneDetails'), d);
    show('doneView');
  } catch (e) {
    showNote(e.message);
  }
  btn.disabled = false;
});

$('againBtn').addEventListener('click', startScanner);
$('nextBtn').addEventListener('click', startScanner);
startScanner();
</script>
</body>
</html>