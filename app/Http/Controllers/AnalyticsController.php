<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\User;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Enrollment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AnalyticsController extends Controller
{
    use AuthorizesRequests;

    public function getDashboardStats(Request $request)
    {
        $user = $request->user();

        // Basic stats available to all users
        $stats = [
            'total_subject' => Subject::count(),
            'total_users' => User::count(),
            'total_assignments' => Assignment::count(),
            'total_submissions' => Submission::count(),
            'total_enrollments' => Enrollment::count(),
        ];

        // Role-specific stats
        if ($user->role?->name === 'admin') {
            $stats['admin_stats'] = [
                'active_subjects' => Course::where('status', 'active')->count(),
                'inactive_subjects' => Course::where('status', 'inactive')->count(),
                'archived_subjects' => Course::where('status', 'archived')->count(),
                'students' => User::whereHas('role', function ($query) {
                    $query->where('name', 'student');
                })->count(),
                'instructors' => User::whereHas('role', function ($query) {
                    $query->where('name', 'instructor');
                })->count(),
                'admins' => User::whereHas('role', function ($query) {
                    $query->where('name', 'admin');
                })->count(),
            ];
        }

        if ($user->role?->name === 'instructor') {
            $instructorCourses = Course::where('instructor_id', $user->id)->pluck('id');
            
            $stats['instructor_stats'] = [
                'my_courses' => $instructorCourses->count(),
                'my_students' => Enrollment::whereIn('subject_id', $instructorCourses)->distinct('user_id')->count(),
                'my_assignments' => Assignment::whereIn('subject_id', $instructorCourses)->count(),
                'my_submissions' => Submission::whereHas('assignment', function ($query) use ($instructorCourses) {
                    $query->whereIn('subject_id', $instructorCourses);
                })->count(),
                'pending_grades' => Submission::whereHas('assignment', function ($query) use ($instructorCourses) {
                    $query->whereIn('subject_id', $instructorCourses);
                })->whereNull('grade')->count(),
            ];
        }

        if ($user->role?->name === 'student') {
            $studentEnrollments = Enrollment::where('user_id', $user->id)->pluck('subject_id');
            
            $stats['student_stats'] = [
                'enrolled_courses' => $studentEnrollments->count(),
                'completed_courses' => Enrollment::where('user_id', $user->id)->where('status', 'completed')->count(),
                'my_submissions' => Submission::where('user_id', $user->id)->count(),
                'graded_submissions' => Submission::where('user_id', $user->id)->whereNotNull('grade')->count(),
                'pending_submissions' => Assignment::whereIn('subject_id', $studentEnrollments)
                    ->where('due_date', '>', now())
                    ->whereDoesntHave('submissions', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })->count(),
            ];
        }

        return response()->json($stats);
    }

    public function getUserAnalytics(Request $request, User $user)
    {
        $currentUser = $request->user();

        // Users can only view their own analytics, or admins can view any user's analytics
        if ($user->id !== $currentUser->id && $currentUser->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized to view this user\'s analytics'
            ], Response::HTTP_FORBIDDEN);
        }

        $days = $request->query('days', 30);
        $startDate = now()->subDays($days);

        // User engagement metrics
        $enrollments = Enrollment::where('user_id', $user->id)->with('subject')->get();
        $submissions = Submission::where('user_id', $user->id)->with('assignment')->get();
        
        // Course performance
        $coursePerformance = $enrollments->map(function ($enrollment) {
            $courseSubmissions = Submission::where('user_id', $enrollment->user_id)
                ->whereHas('assignment', function ($query) use ($enrollment) {
                    $query->where('subject_id', $enrollment->subject_id);
                })->get();

            return [
                'subject_id' => $enrollment->subject_id,
                'subject_name' => $enrollment->subject->name,
                'enrollment_status' => $enrollment->status,
                'final_grade' => $enrollment->final_grade,
                'submissions_count' => $courseSubmissions->count(),
                'average_grade' => $courseSubmissions->whereNotNull('grade')->avg('grade'),
                'late_submissions' => $courseSubmissions->where('is_late', true)->count(),
            ];
        });

        // Submission trends
        $submissionTrends = Submission::where('user_id', $user->id)
            ->where('submitted_at', '>=', $startDate)
            ->selectRaw('DATE(submitted_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Grade trends
        $gradeTrends = Submission::where('user_id', $user->id)
            ->whereNotNull('grade')
            ->where('graded_at', '>=', $startDate)
            ->selectRaw('DATE(graded_at) as date, AVG(grade) as average_grade')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'period_days' => $days,
            'enrollments' => $enrollments->count(),
            'completed_courses' => $enrollments->where('status', 'completed')->count(),
            'total_submissions' => $submissions->count(),
            'average_grade' => $submissions->whereNotNull('grade')->avg('grade'),
            'late_submissions' => $submissions->where('is_late', true)->count(),
            'course_performance' => $coursePerformance,
            'submission_trends' => $submissionTrends,
            'grade_trends' => $gradeTrends,
            'generated_at' => now()->toISOString(),
        ]);
    }

    public function getSystemAnalytics(Request $request)
    {
        // Only admins can view system analytics
        $user = $request->user();
        if ($user->role?->name !== 'admin') {
            return response()->json([
                'message' => 'Only administrators can view system analytics'
            ], Response::HTTP_FORBIDDEN);
        }

        $days = $request->query('days', 30);
        $startDate = now()->subDays($days);

        // User growth trends
        $userGrowth = User::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Course creation trends
        $courseGrowth = Course::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Enrollment trends
        $enrollmentTrends = Enrollment::where('enrolled_at', '>=', $startDate)
            ->selectRaw('DATE(enrolled_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Submission trends
        $submissionTrends = Submission::where('submitted_at', '>=', $startDate)
            ->selectRaw('DATE(submitted_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Performance metrics
        $performanceMetrics = [
            'average_course_completion_rate' => round(
                Enrollment::where('status', 'completed')->count() / 
                max(Enrollment::count(), 1) * 100, 2
            ),
            'average_assignment_completion_rate' => round(
                Submission::count() / 
                max(Assignment::count() * User::whereHas('role', function ($query) {
                    $query->where('name', 'student');
                })->count(), 1) * 100, 2
            ),
            'average_grade' => round(Submission::whereNotNull('grade')->avg('grade'), 2),
            'late_submission_rate' => round(
                Submission::where('is_late', true)->count() / 
                max(Submission::count(), 1) * 100, 2
            ),
        ];

        return response()->json([
            'period_days' => $days,
            'user_growth' => $userGrowth,
            'course_growth' => $courseGrowth,
            'enrollment_trends' => $enrollmentTrends,
            'submission_trends' => $submissionTrends,
            'performance_metrics' => $performanceMetrics,
            'generated_at' => now()->toISOString(),
        ]);
    }
}