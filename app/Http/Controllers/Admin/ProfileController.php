<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // SHOW PROFILE (READ FROM ADMIN TABLE)
    public function show()
    {
        $admin = Auth::guard('admin')->user(); // ✅ admin table data
        return view('admin.profile', compact('admin'));
    }

    // UPDATE PROFILE
  public function update(Request $request)
{
    $admin = Auth::guard('admin')->user();

    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:admins,email,' . $admin->id,
    ]);

    $admin->name = $request->name;
    $admin->email = $request->email;

    if ($request->password) {
        $request->validate([
            'password' => 'min:6|confirmed'
        ]);

        $admin->password = Hash::make($request->password);
    }

    if ($request->hasFile('photo')) {

        $file = $request->file('photo');
        $filename = time().'_'.$file->getClientOriginalName();

        $file->move(public_path('uploads/admin'), $filename);

        $admin->photo = $filename;
    }

    $admin->save();

    return back()->with('success', 'Profile updated successfully');
}
}