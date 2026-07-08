<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    // SHOW PAGE
    public function index()
    {
        return view('admin.settings');
    }

    // UPDATE PASSWORD
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        $admin->password = Hash::make($request->password);
        $admin->save();

        return back()->with('success', 'Password updated successfully');
    }
}