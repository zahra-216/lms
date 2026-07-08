<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    use AuthorizesRequests;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = $user->notifications();

        // Filter by type if provided
        if ($request->query('type')) {
            $query->where('type', $request->query('type'));
        }

        // Filter by read status
        if ($request->query('read') !== null) {
            $query->where('is_read', $request->boolean('read'));
        }

        // Sort by created_at desc (newest first)
        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($notifications);
    }

    public function unreadCount(Request $request)
    {
        $user = $request->user();
        $count = $this->notificationService->getUnreadCount($user);

        return response()->json([
            'unread_count' => $count
        ]);
    }

    public function markAsRead(Notification $notification)
    {
        $user = request()->user();

        // Users can only mark their own notifications as read
        if ($notification->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to modify this notification'
            ], Response::HTTP_FORBIDDEN);
        }

        $this->notificationService->markAsRead($notification);

        return response()->json([
            'notification' => $notification->fresh(),
            'message' => 'Notification marked as read'
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $count = $this->notificationService->markAllAsRead($user);

        return response()->json([
            'message' => "Marked {$count} notifications as read"
        ]);
    }

    public function store(Request $request)
    {
        // Only admins can send notifications to other users
        $user = $request->user();
        if ($user->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Only administrators can send notifications'
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'role_name' => ['nullable', 'string', 'exists:roles,name'],
            'subject_id' => ['nullable', 'integer', 'exists:subject,id'],
            'send_to_all' => ['nullable', 'boolean'],
            'type' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'link' => ['nullable', 'string', 'max:500'],
            'related_id' => ['nullable', 'integer'],
        ]);

        $notifications = [];

        try {
            if ($validated['send_to_all'] ?? false) {
                // Send to all users
                $notifications = $this->notificationService->sendSystemNotification(
                    $validated['type'],
                    $validated['title'],
                    $validated['message'],
                    $validated['link'] ?? null
                );
            } elseif (!empty($validated['user_ids'])) {
                // Send to specific users
                $notifications = $this->notificationService->sendToUsers(
                    $validated['user_ids'],
                    $validated['type'],
                    $validated['title'],
                    $validated['message'],
                    $validated['link'] ?? null,
                    $validated['related_id'] ?? null
                );
            } elseif (!empty($validated['role_name'])) {
                // Send to users with specific role
                $notifications = $this->notificationService->sendToRole(
                    $validated['role_name'],
                    $validated['type'],
                    $validated['title'],
                    $validated['message'],
                    $validated['link'] ?? null,
                    $validated['related_id'] ?? null
                );
            } elseif (!empty($validated['subject_id'])) {
                // Send to course users
                $notifications = $this->notificationService->sendToCourse(
                    $validated['subject_id'],
                    $validated['type'],
                    $validated['title'],
                    $validated['message'],
                    $validated['link'] ?? null,
                    $validated['related_id'] ?? null
                );
            } else {
                return response()->json([
                    'message' => 'Must specify recipients: user_ids, role_name, subject_id, or send_to_all'
                ], Response::HTTP_BAD_REQUEST);
            }

            return response()->json([
                'notifications_sent' => count($notifications),
                'message' => 'Notifications sent successfully'
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send notifications: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Notification $notification)
    {
        $user = request()->user();

        // Users can only view their own notifications
        if ($notification->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to view this notification'
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json($notification);
    }

    public function destroy(Notification $notification)
    {
        $user = request()->user();

        // Users can only delete their own notifications
        if ($notification->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to delete this notification'
            ], Response::HTTP_FORBIDDEN);
        }

        $notification->delete();

        return response()->noContent();
    }

    public function getRecent(Request $request)
    {
        $user = $request->user();
        $limit = $request->query('limit', 10);

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($notifications);
    }

    public function getByType(Request $request, string $type)
    {
        $user = $request->user();
        
        $notifications = $user->notifications()
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($notifications);
    }

    public function cleanup(Request $request)
    {
        // Only admins can cleanup notifications
        $user = $request->user();
        if ($user->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Only administrators can cleanup notifications'
            ], Response::HTTP_FORBIDDEN);
        }

        $deletedCount = $this->notificationService->cleanupOldNotifications();

        return response()->json([
            'message' => "Cleaned up {$deletedCount} old notifications"
        ]);
    }
}