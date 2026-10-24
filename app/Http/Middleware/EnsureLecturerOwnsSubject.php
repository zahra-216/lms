<?php

namespace App\Http\Middleware;

use App\Models\Subject;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A lecturer may only work on the subjects assigned to them.
 *
 * Applies to every lecturer route that targets a subject (/subject/{id}/..., /subject/{subject}/...).
 * Anything nested under that subject (note, video, assignment, quiz, recording...) must also belong to it,
 * so changing the number in the address bar cannot reach another lecturer's content.
 */
class EnsureLecturerOwnsSubject
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $lecturer = auth('lecturer')->user();

        if (! $route || ! $lecturer) {
            return $next($request);
        }

        $param = $route->parameter('subject');
        if ($param === null && str_contains($route->uri(), 'subject/{id}')) {
            $param = $route->parameter('id');
        }

        $subjectId = $param instanceof Model ? $param->getKey() : $param;

        if ($subjectId !== null) {
            $owns = Subject::whereKey($subjectId)->where('lecturer_id', $lecturer->id)->exists();
            abort_unless($owns, 403, 'This subject is not assigned to you.');

            // Anything nested under the subject must really belong to it.
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
