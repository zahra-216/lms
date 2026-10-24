<?php

namespace App\Http\Middleware;

use App\Models\Quiz;
use App\Models\Student;
use App\Models\Subject;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A student may only open subjects of their own course and level.
 *
 * Stops a signed-in student from reading another course's notes, videos, quizzes or grades
 * by typing a different subject number into the address bar.
 */
class EnsureStudentCanAccessSubject
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $student = Student::find(session('student_id'));

        if (! $route || ! $student) {
            return $next($request);
        }

        $subjectId = $this->subjectIdFrom($route);

        if ($subjectId !== null) {
            $allowed = Subject::whereKey($subjectId)
                ->where('course_id', $student->course_id)
                ->where('level_id', $student->level_id)
                ->exists();

            abort_unless($allowed, 403, 'This subject is not part of your course.');
        }

        return $next($request);
    }

    private function subjectIdFrom($route): int|string|null
    {
        $uri = $route->uri();

        // /student/subject/{id}/... and /student/recordings/subject/{subjectId}
        if (str_contains($uri, 'subject/{id}')) {
            return $route->parameter('id');
        }
        if ($route->hasParameter('subjectId')) {
            return $route->parameter('subjectId');
        }

        // /student/quiz/{quiz}/start
        $quiz = $route->parameter('quiz');
        if ($quiz instanceof Quiz) {
            return $quiz->subject_id;
        }

        return null;
    }
}
