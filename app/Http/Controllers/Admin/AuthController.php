<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    /* ================= LOGIN ================= */

    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $key = 'admin-login-'.$request->ip();

        // 🔐 Limit login attempts (5 attempts)
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'email' => 'Too many login attempts. Try again later.'
            ]);
        }

        if (Auth::guard('admin')->attempt(
            $request->only('email','password'),
            $request->boolean('remember')
        )) {
            $request->session()->regenerate(); // 🔐 Prevent session fixation
            RateLimiter::clear($key);

            return redirect()->route('admin.dashboard');
        }

        RateLimiter::hit($key, 60); // Lock 60 seconds
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    /* ================= DASHBOARD ================= */

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /* ================= LOGOUT ================= */

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /* ================= FORGOT PASSWORD ================= */

    public function showForgotForm()
    {
        return view('admin.forgot');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::broker('admins')
            ->sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /* ================= RESET PASSWORD ================= */

    public function showResetForm($token)
    {
        return view('admin.reset', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::broker('admins')->reset(
            $request->only('email','password','password_confirmation','token'),
            function ($admin, $password) {
                $admin->password = Hash::make($password);
                $admin->setRememberToken(Str::random(60));
                $admin->save();

                event(new PasswordReset($admin));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')
                ->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}