<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // ================= LOGIN =================

    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email','password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect('/admin/dashboard');
        }

        return back()->withErrors(['Invalid login']);
    }

    // ================= CREATE ADMIN =================

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6'
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success','Admin created successfully');
    }

    // ================= DASHBOARD =================

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // ================= LOGOUT =================

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    }

    // ================= FORGOT PASSWORD =================

    public function forgotForm()
    {
        return view('admin.forgot');
    }

    // SEND RESET LINK
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return back()->withErrors(['email' => 'Email not found']);
        }

        // generate token
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => hash('sha256', $token),
                'created_at' => now()
            ]
        );

        // reset link
        $link = url("/admin/reset-password/$token");

        return back()
            ->with('status', 'Reset link generated')
            ->with('link', $link); // ✅ IMPORTANT FIX
    }

    // RESET FORM
    public function resetForm($token)
    {
        return view('admin.reset', compact('token'));
    }

    // RESET PASSWORD
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $hashedToken = hash('sha256', $request->token);

        $record = DB::table('password_reset_tokens')
            ->where('token', $hashedToken)
            ->first();

        if (!$record) {
            return back()->withErrors(['error' => 'Invalid or expired token']);
        }

        // optional expiry check (15 min)
        if (now()->diffInMinutes($record->created_at) > 15) {
            return back()->withErrors(['error' => 'Token expired']);
        }

        // update password
        Admin::where('email', $record->email)->update([
            'password' => Hash::make($request->password)
        ]);

        // delete token
        DB::table('password_reset_tokens')
            ->where('email', $record->email)
            ->delete();

        return redirect('/admin/login')
            ->with('success', 'Password reset successful');
    }
}