<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   
<div class="container mt-5">
    <h2>{{ $course->name }} - Levels</h2>
    <div class="row mt-4">
        @foreach($levels as $level)
        @php
            $url = auth('student')->check() 
                    ? route('course.level.semesters', ['courseId'=>$course->id,'level'=>$level->name])
                    : route('student.login');
        @endphp
        <div class="col-md-3 mb-3">
            <a href="{{ $url }}" class="text-decoration-none">
                <div class="card p-4 text-center">
                    <h4>{{ strtoupper($level->name) }}</h4>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>

</body>
</html>