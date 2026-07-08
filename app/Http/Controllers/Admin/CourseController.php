<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Faculty;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // Display all courses
    public function index()
    {
        $courses = Course::with('faculty')->get(); // eager load faculty
        return view('admin.course.index', compact('courses'));
    }

    // Show form to create new course
    public function create()
    {
        $faculties = Faculty::all();
        return view('admin.course.create', compact('faculties'));
    }

    // Store new course
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code',
            'faculty_id' => 'required|exists:faculties,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->all();

        // ✅ Store image directly in public/storage/courses (Windows friendly)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/courses'), $filename);
            $data['image'] = $filename;
        }

        Course::create($data);

        return redirect()->route('admin.courses.index')->with('success', 'Course added successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $faculties = Faculty::all();
        return view('admin.course.edit', compact('course', 'faculties'));
    }

    // Update course
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code,' . $course->id,
            'faculty_id' => 'required|exists:faculties,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($course->image && file_exists(public_path('storage/courses/' . $course->image))) {
                unlink(public_path('storage/courses/' . $course->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/courses'), $filename);
            $data['image'] = $filename;
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully!');
    }

    // Delete course
    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        // Delete image if exists
        if ($course->image && file_exists(public_path('storage/courses/' . $course->image))) {
            unlink(public_path('storage/courses/' . $course->image));
        }

        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully!');
    }
}