<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body{
            font-family: Arial;
            font-size: 12px;
        }

        .header-table{
            width:100%;
            border:none;
            margin-bottom:15px;
        }

        .logo{
            width:90px;
            height:auto;
        }

        .title{
            text-align:left;
            padding-left:10px;
        }

        .title h1{
            margin:0;
            color:#0a3d62;
            font-size:24px;
        }

        .title p{
            margin:2px 0;
            font-size:16px;
        }

        table.data{
            width:100%;
            border-collapse: collapse;
        }

        table.data th, table.data td{
            border:1px solid #000;
            padding:6px;
        }

        table.data th{
            background:#f2f2f2;
        }

        h3{
            margin-top:10px;
        }
    </style>
</head>

<body>

<!-- HEADER -->
<table class="header-table">
    <tr>

        <!-- LOGO LEFT -->
        <td style="width:15%; text-align:left;">
            <img src="{{ public_path('images/logo.jpeg') }}" class="logo">
        </td>

        <!-- TITLE LEFT (NO GAP, CLEAN ALIGN) -->
        <td style="width:85%;" class="title">
            <h1>TT METRO CAMPUS, SRI LANKA</h1>
            <p><b>Empowering Minds, Shaping Future</b></p>
            <p>Address:No 11 AI, Galle Road, Mount Lavinia</p>
            <p>Web: www.ttmetrocampus.com | Email: co.techlink@gmail.com | Phone: 077 2270348</p>
        </td>

    </tr>
</table>

<hr>

<!-- REPORT TITLE -->
<h1>Enrollments Report</h1>

<!-- TABLE -->
<table class="data">
    <tr>
        <th>Student</th>
        <th>Course</th>
        <th>Status</th>
        <th>Date</th>
    </tr>

    @foreach($enrollments as $e)
    <tr>
        <td>{{ $e->student->name }}</td>
        <td>{{ $e->course->name }}</td>
        <td>{{ $e->status }}</td>
        <td>{{ $e->enrolled_at }}</td>
    </tr>
    @endforeach

</table>

</body>
</html>