<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use App\Models\Faculty;

class LecturerAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.lecturer-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $key = 'lecturer-login:'.Str::lower($request->username).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'username' => 'Too many attempts. Please try again in '.RateLimiter::availableIn($key).' seconds.',
            ])->onlyInput('username');
        }

        if (Auth::guard('lecturer')->attempt($request->only('username', 'password'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            return redirect()->route('lecturer.dashboard');
        }

        RateLimiter::hit($key, 120);

        return back()->withErrors([
            'username' => 'Invalid username or password.',
        ])->onlyInput('username');
    }

    public function dashboard()
    {
        $lecturer = Auth::guard('lecturer')->user();
        $faculties = Faculty::all();

        return view('lecturer.dashboard', compact('lecturer', 'faculties'));
    }

    public function logout(Request $request)
    {
        Auth::guard('lecturer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('lecturer.login');
    }

    public function myPayments()
    {
        $lecturer = Auth::guard('lecturer')->user();

        $payments = \App\Models\LecturerPayment::with('courses')
            ->where('lecturer_id', $lecturer->id)
            ->latest('date')
            ->get();

        return view('lecturer.my-payments', compact('lecturer', 'payments'));
    }
}
