@extends('layouts.public')
@section('title')
Grade and GPA scale
@endsection

@section('content')
@php
    $scale = [
        ['85 - 100', 'A+', '4.0', 'Distinction'], ['75 - 84', 'A', '4.0', 'Distinction'], ['70 - 74', 'A-', '3.7', 'Merit'],
        ['65 - 69', 'B+', '3.3', 'Merit'], ['60 - 64', 'B', '3.0', 'Merit'], ['55 - 59', 'B-', '2.7', 'Pass'],
        ['50 - 54', 'C+', '2.3', 'Pass'], ['45 - 49', 'C', '2.0', 'Pass'], ['40 - 44', 'C-', '1.7', 'Fail'],
        ['35 - 39', 'D+', '1.3', 'Fail'], ['30 - 34', 'D', '1.0', 'Fail'], ['0 - 29', 'F', '0.0', 'Fail'],
    ];
    $tone = ['Distinction' => 'bg-success', 'Merit' => 'bg-primary', 'Pass' => 'bg-info text-dark', 'Fail' => 'bg-danger'];
@endphp
<div class="pub-wrap" style="max-width:860px;margin-top:40px">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-mortarboard"></i> Common grade and GPA scale</h2>
            <small>How marks convert to grades, grade points and classifications.</small>
        </div>
    </div>
    <div class="table-wrap">
        <table class="table table-hover align-middle">
            <thead><tr><th>Marks (%)</th><th>Grade</th><th>GPA</th><th>Classification</th></tr></thead>
            <tbody>
                @foreach ($scale as [$marks, $grade, $gpa, $class])
                    <tr>
                        <td>{{ $marks }}</td>
                        <td class="fw-bold">{{ $grade }}</td>
                        <td>{{ $gpa }}</td>
                        <td><span class="badge {{ $tone[$class] }}">{{ $class }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
