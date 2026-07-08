<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Subject;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AnnouncementController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, Subject $subject)
    {
        $this->authorize('view', $subject);

        $query = $subject->announcements()->with('user');

        // Filter by pinned status
        if ($request->query('pinned') !== null) {
            $query->where('is_pinned', $request->boolean('pinned'));
        }

        // Order: pinned first, then by created_at desc
        $announcements = $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($announcements);
    }

    public function store(Request $request, Subject $subject)
    {
        $this->authorize('update', $subject);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $announcement = $course->announcements()->create([
            ...$validated,
            'user_id' => $request->user()->id, // Set the author
        ]);

        return response()->json($announcement->load(['course', 'user']), Response::HTTP_CREATED);
    }

    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement->subject);

        return response()->json($announcement->load(['subject', 'user']));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement->subject);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $announcement->update($validated);

        return response()->json($announcement->fresh()->load(['subject', 'user']));
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('update', $announcement->subject);

        $announcement->delete();

        return response()->noContent();
    }

    public function togglePin(Announcement $announcement)
    {
        $this->authorize('update', $announcement->subject);

        $announcement->update(['is_pinned' => !$announcement->is_pinned]);

        return response()->json([
            'message' => $announcement->is_pinned ? 'Announcement pinned' : 'Announcement unpinned',
            'is_pinned' => $announcement->is_pinned
        ]);
    }

    public function getRecent(Request $request, Subject $subject)
    {
        $this->authorize('view', $subject);

        $limit = $request->query('limit', 5);

        $announcements = $subject->announcements()
            ->with('user')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($announcements);
    }

    public function getPinned(Request $request, Subject $subject)
    {
        $this->authorize('view', $subject);

        $announcements = $subject->announcements()
            ->where('is_pinned', true)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($announcements);
    }
}