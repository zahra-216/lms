<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<div class="container mt-5">
    <h2>{{ $course->name }} - {{ strtoupper($levelObj->name) }} Semesters</h2>
    <div class="row mt-4">
        @foreach($semesters as $semester)
        <div class="col-md-3 mb-3">
            <a href="{{ route('semester.modules', ['semesterId'=>$semester->id]) }}" class="text-decoration-none">
                <div class="card p-4 text-center">
                    <h5>{{ $semester->name }}</h5>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>

</body>
</html>