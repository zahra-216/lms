<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = $request->user()->load(['role', 'profile', 'subjects']);
        
        return response()->json([
            'user' => $user,
            'enrolled_courses' => $user->courses()->with(['course' => function($query) {
                $query->with('instructor');
            }])->get(),
            'courses_taught' => $user->coursesTaught()->get()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'social_links' => ['nullable', 'array'],
            'social_links.twitter' => ['nullable', 'string', 'url'],
            'social_links.linkedin' => ['nullable', 'string', 'url'],
            'social_links.github' => ['nullable', 'string', 'url'],
        ]);

        // Update user basic info
        if (isset($validated['name']) || isset($validated['email'])) {
            $user->update(array_filter([
                'name' => $validated['name'] ?? null,
                'email' => $validated['email'] ?? null,
            ]));
        }

        // Update or create profile
        $profileData = array_filter([
            'bio' => $validated['bio'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'social_links' => $validated['social_links'] ?? null,
        ]);

        if (!empty($profileData)) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        return response()->json([
            'user' => $user->fresh()->load(['role', 'profile']),
            'message' => 'Profile updated successfully'
        ]);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // 2MB max
        ]);

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->profile && $user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
        }

        // Store new avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');

        // Update profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['avatar' => $avatarPath]
        );

        return response()->json([
            'avatar_url' => Storage::url($avatarPath),
            'message' => 'Avatar uploaded successfully'
        ]);
    }

    public function deleteAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->profile && $user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
            
            $user->profile->update(['avatar' => null]);
        }

        return response()->json([
            'message' => 'Avatar deleted successfully'
        ]);
    }

    public function getUserCourses(Request $request)
    {
        $user = $request->user();

        $enrolledCourses = $user->courses()
            ->with(['course' => function($query) {
                $query->with(['instructor', 'assignments' => function($q) {
                    $q->where('is_published', true);
                }]);
            }])
            ->get();

        $taughtCourses = $user->coursesTaught()
            ->with(['enrollments.user', 'assignments'])
            ->get();

        return response()->json([
            'enrolled_subject' => $enrolledSubjects,
            'taught_subject' => $taughtSubjects,
        ]);
    }

    public function getUserProgress(Request $request)
    {
        $user = $request->user();

        // Get enrollment statistics
        $enrollments = $user->enrollments()->with('subject')->get();
        
        $stats = [
            'total_enrolled' => $enrollments->count(),
            'completed_courses' => $enrollments->where('status', 'completed')->count(),
            'active_enrollments' => $enrollments->where('status', 'enrolled')->count(),
            'average_grade' => $enrollments->where('final_grade', '!=', null)->avg('final_grade'),
        ];

        // Get recent submissions
        $recentSubmissions = $user->submissions()
            ->with(['assignment.subject'])
            ->latest('submitted_at')
            ->limit(10)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_submissions' => $recentSubmissions,
            'enrollments' => $enrollments
        ]);
    }
}