<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileUpoadControlle extends Controller
{
    public function uploadSubmission(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240'], // 10MB max
            'assignment_id' => ['required', 'integer', 'exists:assignments,id'],
        ]);

        $assignment = \App\Models\Assignment::findOrFail($validated['assignment_id']);
        $user = $request->user();

        // Check if user is enrolled in the subject
        if (!$assignment->subject->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'You are not enrolled in this subject'
            ], Response::HTTP_FORBIDDEN);
        }

        $file = $validated['file'];
        
        // Validate file type based on assignment settings
        $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedTypes)) {
            return response()->json([
                'message' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes)
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validate file size (convert MB to bytes)
        $maxSize = $assignment->max_file_size * 1024 * 1024; // Convert MB to bytes
        if ($file->getSize() > $maxSize) {
            return response()->json([
                'message' => "File size exceeds limit of {$assignment->max_file_size}MB"
            ], Response::HTTP_BAD_REQUEST);
        }

        // Generate unique filename
        $filename = Str::uuid() . '.' . $extension;
        $path = 'submissions/' . $assignment->id . '/' . $filename;

        // Store file
        $storedPath = $file->storeAs('submissions/' . $assignment->id, $filename, 'public');

        return response()->json([
            'file_path' => $storedPath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_type' => $file->getMimeType(),
            'download_url' => url('storage/' . $storedPath),
            'message' => 'File uploaded successfully'
        ], Response::HTTP_CREATED);
    }

    public function uploadAvatar(Request $request)
    {
        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // 2MB max
        ]);

        $user = $request->user();
        $file = $validated['avatar'];

        // Delete old avatar if exists
        if ($user->profile && $user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
        }

        // Generate unique filename
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = 'avatars/' . $filename;

        // Store file
        $storedPath = $file->storeAs('avatars', $filename, 'public');

        // Update user profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['avatar' => $storedPath]
        );

        return response()->json([
            'avatar_url' => url('storage/' . $storedPath),
            'message' => 'Avatar uploaded successfully'
        ]);
    }

    public function uploadCourseContent(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:51200'], // 50MB max for course content
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ]);

        $course = \App\Models\subject::findOrFail($validated['subject_id']);
        $user = $request->user();

        // Check if user can upload content to this course
        if ($user->id !== $course->instructor_id && $user->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized to upload content to this course'
            ], Response::HTTP_FORBIDDEN);
        }

        $file = $validated['file'];
        
        // Validate file type for course content
        $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'ppt', 'pptx', 'xls', 'xlsx'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedTypes)) {
            return response()->json([
                'message' => 'File type not allowed for course content'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Generate unique filename
        $filename = Str::uuid() . '.' . $extension;
        $path = 'note/' . $course->id . '/' . $filename;

        // Store file
        $storedPath = $file->storeAs('note/' . $course->id, $filename, 'public');

        return response()->json([
            'file_path' => $storedPath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_type' => $file->getMimeType(),
            'download_url' => url('storage/' . $storedPath),
            'message' => 'Course content uploaded successfully'
        ], Response::HTTP_CREATED);
    }

    public function download(Request $request, $fileId)
    {
        // This is a simplified version - in production you'd want more security
        $filePath = $request->query('path');
        
        if (!$filePath) {
            return response()->json([
                'message' => 'File path required'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json([
                'message' => 'File not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Basic security check - ensure file is in allowed directories
        $allowedPaths = ['submissions/', 'note/', 'avatars/'];
        $isAllowed = false;
        
        foreach ($allowedPaths as $allowedPath) {
            if (str_starts_with($filePath, $allowedPath)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            return response()->json([
                'message' => 'Access denied'
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->download(storage_path('app/public/' . $filePath));
    }

    public function deleteFile(Request $request)
    {
        $validated = $request->validate([
            'file_path' => ['required', 'string'],
        ]);

        $user = $request->user();
        $filePath = $validated['file_path'];

        // Check if file exists
        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json([
                'message' => 'File not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Basic security check
        $allowedPaths = ['submissions/', 'note/', 'avatars/'];
        $isAllowed = false;
        
        foreach ($allowedPaths as $allowedPath) {
            if (str_starts_with($filePath, $allowedPath)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            return response()->json([
                'message' => 'Access denied'
            ], Response::HTTP_FORBIDDEN);
        }

        // Delete file
        Storage::disk('public')->delete($filePath);

        return response()->json([
            'message' => 'File deleted successfully'
        ]);
    }

    public function getFileInfo(Request $request)
    {
        $validated = $request->validate([
            'file_path' => ['required', 'string'],
        ]);

        $filePath = $validated['file_path'];

        // Check if file exists
        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json([
                'message' => 'File not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $fileInfo = [
            'file_path' => $filePath,
            'file_name' => basename($filePath),
            'file_size' => Storage::disk('public')->size($filePath),
            'last_modified' => Storage::disk('public')->lastModified($filePath),
            'download_url' => url('storage/' . $filePath),
        ];

        return response()->json($fileInfo);
    }
}

