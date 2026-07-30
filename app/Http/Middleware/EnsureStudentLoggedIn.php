<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentLoggedIn
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('student_id')) {
            return redirect()->route('login')
                ->withErrors(['registration_no' => 'Please login to continue.']);
        }

        return $next($request);
    }
}