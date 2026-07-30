<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Hash;

class LecturerController extends Controller
{
    public function index()
    {
        $lecturers = Lecturer::all();
        return view('admin.lecturers.index', compact('lecturers'));
    }

    public function create()
    {
        return view('admin.lecturers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:lecturers,username',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:lecturers,email',
            'password' => 'required|string|min:6',
        ]);

        Lecturer::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Lecturer added successfully!');
    }

    public function edit($id)
    {
        $lecturer = Lecturer::findOrFail($id);
        return view('admin.lecturers.edit', compact('lecturer'));
    }

    public function update(Request $request, $id)
    {
        $lecturer = Lecturer::findOrFail($id);

        $request->validate([
            'username' => 'required|string|unique:lecturers,username,' . $lecturer->id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:lecturers,email,' . $lecturer->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $lecturer->username = $request->username;
        $lecturer->name = $request->name;
        $lecturer->email = $request->email;

        if ($request->filled('password')) {
            $lecturer->password = Hash::make($request->password);
        }

        $lecturer->save();

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Lecturer updated successfully!');
    }

    public function destroy($id)
    {
        Lecturer::findOrFail($id)->delete();

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Lecturer deleted!');
    }
}
