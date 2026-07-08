<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faculty;
use Illuminate\Support\Facades\Storage;

class FacultyController extends Controller
{
    // List
    public function index()
    {
        $faculties = Faculty::all();
        return view('admin.faculty.index', compact('faculties'));
    }

    // Create form
    public function create()
    {
        return view('admin.faculty.create');
    }

    // STORE (IMAGE FIXED)
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $faculty = new Faculty();
        $faculty->name = $request->name;

        // ✅ IMAGE UPLOAD FIXED
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();

            // store in storage/app/public/faculty
            $file->storeAs('public/faculty', $filename);

            $faculty->image = $filename;
        }

        $faculty->save();

        return redirect()->route('admin.faculties.index')
            ->with('success', 'Faculty added successfully!');
    }

    // EDIT
    public function edit($id)
    {
        $faculty = Faculty::findOrFail($id);
        return view('admin.faculty.edit', compact('faculty'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $faculty = Faculty::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $faculty->name = $request->name;

        // ✅ UPDATE IMAGE
        if ($request->hasFile('image')) {

            // delete old image
            if ($faculty->image && Storage::exists('public/faculty/'.$faculty->image)) {
                Storage::delete('public/faculty/'.$faculty->image);
            }

            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();

            $file->storeAs('public/faculty', $filename);

            $faculty->image = $filename;
        }

        $faculty->save();

        return redirect()->route('admin.faculties.index')
            ->with('success', 'Faculty updated successfully!');
    }

    // DELETE
    public function destroy($id)
    {
        $faculty = Faculty::findOrFail($id);

        if ($faculty->image && Storage::exists('public/faculty/'.$faculty->image)) {
            Storage::delete('public/faculty/'.$faculty->image);
        }

        $faculty->delete();

        return redirect()->route('admin.faculties.index')
            ->with('success', 'Faculty deleted!');
    }
}