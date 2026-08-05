<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body{ font-family: Arial; font-size: 10px; }
    .header-table{ width:100%; border:none; margin-bottom:12px; }
    .logo{ width:70px; height:auto; }
    .title{ text-align:left; padding-left:10px; }
    .title h1{ margin:0; color:#0a3d62; font-size:18px; }
    .title p{ margin:1px 0; font-size:11px; }
    h2{ margin:6px 0 10px; font-size:14px; color:#012147; }
    table.data{ width:100%; border-collapse: collapse; }
    table.data th, table.data td{ border:1px solid #000; padding:4px; text-align:center; }
    table.data th{ background:#012147; color:#fff; }
    table.data td.name{ text-align:left; white-space:nowrap; }
    .present{ color:green; font-weight:bold; }
    .absent{ color:red; font-weight:bold; }
    .unmarked{ color:#888; }
</style>
</head>
<body>

<table class="header-table">
    <tr>
        <td style="width:10%; text-align:left;">
            <img src="{{ public_path('images/logo.jpeg') }}" class="logo">
        </td>
        <td style="width:90%;" class="title">
            <h1>TT METRO CAMPUS, SRI LANKA</h1>
            <p><b>Empowering Minds, Shaping Future</b></p>
            <p>Address: No 11 AI, Galle Road, Mount Lavinia</p>
            <p>Web: www.ttmetrocampus.com | Email: co.techlink@gmail.com | Phone: 077 2270348</p>
        </td>
    </tr>
</table>

<hr>

<h2>Attendance Report — {{ $subject->code }} - {{ $subject->name }} ({{ $start->format('F Y') }})</h2>

<table class="data">
    <tr>
        <th style="text-align:left;">Student Name</th>
        @foreach($dates as $d)
            <th>{{ \Carbon\Carbon::parse($d)->format('d') }}</th>
        @endforeach
    </tr>
    @foreach($students as $student)
        <tr>
            <td class="name">{{ $student->name }}</td>
            @foreach($dates as $d)
                @php $status = $records->get($d)?->get($student->id)?->status; @endphp
                <td>
                    @if($status === 'present') <span class="present">&#10003;</span>
                    @elseif($status === 'absent') <span class="absent">&#10007;</span>
                    @else <span class="unmarked">&ndash;</span>
                    @endif
                </td>
            @endforeach
        </tr>
    @endforeach
</table>

</body>
</html>