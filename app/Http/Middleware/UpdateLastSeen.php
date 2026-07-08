<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Student;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::has('student_id')) {

            Student::where('id', Session::get('student_id'))
                ->update([
                    'last_seen_at' => now()
                ]);
        }

        return $next($request);
    }
}