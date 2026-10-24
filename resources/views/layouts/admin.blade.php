{{-- Shell for the admin area. Pages: @extends('layouts.admin') --}}
@extends('layouts.app', ['role' => 'admin', 'homeUrl' => route('admin.dashboard')])
