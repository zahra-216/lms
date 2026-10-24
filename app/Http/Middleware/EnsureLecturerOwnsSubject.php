<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Anything addressed under a subject (note, video, assignment, quiz, recording...) must really
 * belong to that subject, so changing one number in the address bar cannot edit or delete
 * content that lives in a different subject.
 *
 * (Lecturers are not assigned to individual subjects in this system, so access is not narrowed
 * further than that. If the campus later assigns lecturers to subjects, add the check here.)
 */
class EnsureLecturerOwnsSubject
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();

        if (! $route) {
            return $next($request);
        }

        $subject = $route->parameter('subject');
        if ($subject === null && str_contains($route->uri(), 'subject/{id}')) {
            $subject = $route->parameter('id');
        }

        $subjectId = $subject instanceof Model ? $subject->getKey() : $subject;

        if ($subjectId !== null) {
            foreach ($route->parameters() as $name => $value) {
                if ($name === 'subject' || ! $value instanceof Model) {
                    continue;
                }
                if (array_key_exists('subject_id', $value->getAttributes()) && (int) $value->subject_id !== (int) $subjectId) {
                    abort(404);
                }
            }
        }

        return $next($request);
    }
}
