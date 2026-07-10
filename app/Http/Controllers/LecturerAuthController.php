<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        $credentials = $request->only('username', 'password');

        if (Auth::guard('lecturer')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('lecturer.dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid username or password.',
        ]);
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
}
