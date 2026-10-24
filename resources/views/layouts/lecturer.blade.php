{{-- Shell for the lecturer area. Pages: @extends('layouts.lecturer') --}}
@extends('layouts.app', ['role' => 'lecturer', 'homeUrl' => route('lecturer.dashboard')])
