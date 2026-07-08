<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function unreadCount(Request $request)
    {
        $user = $request->user();
        $count = $this->notificationService->getUnreadCount($user);

        // Optionally return latest 10 notifications
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->limit(10)->get();

        return response()->json([
            'unread_count' => $count,
            'notifications' => $notifications
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        return $user->notifications()->orderBy('created_at','desc')->paginate(20);
    }
    public function markRead($id)
    {
        $notification = DatabaseNotification::findOrFail($id);
        $notification->markAsRead();
        return back();
    }
}