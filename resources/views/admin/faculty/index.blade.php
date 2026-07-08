<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculties | TT Metro Campus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:#f4f6f9;
            color:#012147;
            padding:40px;
        }

        .container {
            max-width:700px;
            margin:auto;
            background:#fff;
            padding:30px;
            border-radius:12px;
            box-shadow:0 4px 10px rgba(0,0,0,.1);
        }

        h1 {text-align:center; margin-bottom:30px;}

        .success {color:green;text-align:center; margin-bottom:20px;}
        .error {color:red;text-align:center; margin-bottom:20px;}

        .add-btn {
            display:inline-block;
            margin-bottom:20px;
            padding:10px 20px;
            background:#012147;
            color:#fff;
            border-radius:8px;
            text-decoration:none;
            font-weight:bold;
        }

        .add-btn:hover {background:#021634;}

        table {
            width:100%;
            border-collapse:collapse;
        }

        th,td {
            border:1px solid #ddd;
            padding:10px;
            text-align:center;
        }

        .action-btn {
            padding:6px 12px;
            border:none;
            border-radius:6px;
            cursor:pointer;
            font-weight:bold;
        }

        .edit-btn {background:#ffc107; color:#012147;}
        .edit-btn:hover {background:#e0a800; color:#fff;}

        .delete-btn {background:#dc3545; color:#fff;}
        .delete-btn:hover {background:#c82333;}

        /* ✅ IMAGE STYLE */
        .faculty-img {
            width:60px;
            height:60px;
            border-radius:50%;
            object-fit:cover;
            border:2px solid #012147;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>FACULTIES</h1>

    @if(session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ route('admin.faculties.create') }}" class="add-btn">
        <i class="fa fa-plus"></i> Add Faculty
    </a>

    <table>
        <tr>
            <th>#</th>
            <th>Image</th>   <!-- ✅ NEW -->
            <th>Faculty Name</th>
            <th>Actions</th>
        </tr>

        @foreach($faculties as $faculty)
        <tr>
            <td>{{ $loop->iteration }}</td>

            <!-- ✅ IMAGE COLUMN -->
            <td>
                @php
                    $img = $faculty->image
                        ? asset('storage/faculty/'.$faculty->image)
                        : 'https://via.placeholder.com/60';
                @endphp

                <img src="{{ $img }}" class="faculty-img">
            </td>

            <td>{{ $faculty->name }}</td>

            <td>
                <a href="{{ route('admin.faculties.edit', $faculty->id) }}" class="action-btn edit-btn">
                    <i class="fa fa-edit"></i>
                </a>

                <form action="{{ route('admin.faculties.destroy', $faculty->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure to delete?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn delete-btn">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach

    </table>
</div>

</body>
</html>