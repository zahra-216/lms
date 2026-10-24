{{-- Shell for the student area. Pages: @extends('layouts.student') --}}
@extends('layouts.app', ['role' => 'student', 'homeUrl' => route('dashboard')])
