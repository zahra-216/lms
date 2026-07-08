<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ActivityLogController extends Controller
{
    use AuthorizesRequests;

    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function index(Request $request)
    {
        // Only admins can view all activity logs
        $user = $request->user();
        if ($user->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Only administrators can view activity logs'
            ], Response::HTTP_FORBIDDEN);
        }

        $query = ActivityLog::with('user');

        // Filter by user if provided
        if ($request->query('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        // Filter by action if provided
        if ($request->query('action')) {
            $query->where('action', $request->query('action'));
        }

        // Filter by model if provided
        if ($request->query('model')) {
            $query->where('model', $request->query('model'));
        }

        // Filter by date range
        if ($request->query('start_date')) {
            $query->where('created_at', '>=', $request->query('start_date'));
        }

        if ($request->query('end_date')) {
            $query->where('created_at', '<=', $request->query('end_date'));
        }

        // Filter by IP address if provided
        if ($request->query('ip_address')) {
            $query->where('ip_address', $request->query('ip_address'));
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json($logs);
    }

    public function show(ActivityLog $activityLog)
    {
        $user = request()->user();

        // Users can only view their own activity logs, or admins can view all
        if ($activityLog->user_id !== $user->id && $user->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized to view this activity log'
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json($activityLog->load('user'));
    }

    public function getUserActivity(Request $request, User $user)
    {
        $currentUser = $request->user();

        // Users can only view their own activity, or admins can view any user's activity
        if ($user->id !== $currentUser->id && $currentUser->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized to view this user\'s activity'
            ], Response::HTTP_FORBIDDEN);
        }

        $days = $request->query('days', 30);
        $summary = $this->activityLogService->getUserActivitySummary($user, $days);

        return response()->json($summary);
    }

    public function getSystemActivity(Request $request)
    {
        // Only admins can view system activity
        $user = $request->user();
        if ($user->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Only administrators can view system activity'
            ], Response::HTTP_FORBIDDEN);
        }

        $days = $request->query('days', 30);
        $summary = $this->activityLogService->getSystemActivitySummary($days);

        return response()->json($summary);
    }

    public function getCourseActivity(Request $request, int $courseId)
    {
        $user = $request->user();

        // Check if user has access to this course
        $course = \App\Models\Course::find($courseId);
        if (!$course) {
            return response()->json([
                'message' => 'Course not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $hasAccess = $user->role?->name === 'admin' || 
                    $user->id === $course->instructor_id ||
                    $course->users()->where('user_id', $user->id)->exists();

        if (!$hasAccess) {
            return response()->json([
                'message' => 'Unauthorized to view this course\'s activity'
            ], Response::HTTP_FORBIDDEN);
        }

        $days = $request->query('days', 30);
        $summary = $this->activityLogService->getCourseActivitySummary($courseId, $days);

        return response()->json($summary);
    }

    public function getActivityTrends(Request $request)
    {
        // Only admins can view activity trends
        $user = $request->user();
        if ($user->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Only administrators can view activity trends'
            ], Response::HTTP_FORBIDDEN);
        }

        $days = $request->query('days', 30);
        $trends = $this->activityLogService->getActivityTrends($days);

        return response()->json($trends);
    }

    public function getRecentActivity(Request $request)
    {
        $user = $request->user();
        $limit = $request->query('limit', 20);

        $query = ActivityLog::with('user')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        $activities = $query->get();

        return response()->json($activities);
    }

    public function cleanup(Request $request)
    {
        // Only admins can cleanup activity logs
        $user = $request->user();
        if ($user->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Only administrators can cleanup activity logs'
            ], Response::HTTP_FORBIDDEN);
        }

        $days = $request->query('days', 90);
        $deletedCount = $this->activityLogService->cleanupOldLogs($days);

        return response()->json([
            'message' => "Cleaned up {$deletedCount} old activity logs"
        ]);
    }

    public function getActivityStats(Request $request)
    {
        // Only admins can view activity stats
        $user = $request->user();
        if ($user->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Only administrators can view activity statistics'
            ], Response::HTTP_FORBIDDEN);
        }

        $days = $request->query('days', 30);
        $startDate = now()->subDays($days);

        // Get top users by activity
        $topUsers = ActivityLog::where('created_at', '>=', $startDate)
            ->with('user')
            ->selectRaw('user_id, COUNT(*) as activity_count')
            ->groupBy('user_id')
            ->orderBy('activity_count', 'desc')
            ->limit(10)
            ->get();

        // Get most common actions
        $topActions = ActivityLog::where('created_at', '>=', $startDate)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Get activity by hour of day
        $activityByHour = ActivityLog::where('created_at', '>=', $startDate)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Get activity by day of week
        $activityByDay = ActivityLog::where('created_at', '>=', $startDate)
            ->selectRaw('DAYOFWEEK(created_at) as day_of_week, COUNT(*) as count')
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();

        return response()->json([
            'period_days' => $days,
            'top_users' => $topUsers,
            'top_actions' => $topActions,
            'activity_by_hour' => $activityByHour,
            'activity_by_day' => $activityByDay,
            'generated_at' => now()->toISOString(),
        ]);
    }
}