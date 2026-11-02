<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('lecturer*')) {
                return route('lecturer.login');
            }

            if ($request->is('admin*')) {
                return route('admin.login');
            }

            return route('login');
        });

        $middleware->appendToGroup('web', \App\Http\Middleware\RememberPreviousPage::class);

        $middleware->alias([
            'student.auth' => \App\Http\Middleware\EnsureStudentLoggedIn::class,
            'student.subject' => \App\Http\Middleware\EnsureStudentCanAccessSubject::class,
            'lecturer.subject' => \App\Http\Middleware\EnsureLecturerOwnsSubject::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // A quiz (or an attempt) that was deleted while someone still had its page open:
        // explain what happened instead of showing a bare "404 not found".
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            $missing = $e->getPrevious();

            if (! $missing instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
                || ! in_array($missing->getModel(), [\App\Models\Quiz::class, \App\Models\QuizSubmission::class], true)) {
                return null;
            }

            if ($request->routeIs('student.quiz.*')) {
                return redirect()->route('dashboard')->with('error',
                    'This quiz was deleted by your lecturer or the administrator, so it and your attempt are no longer available. '
                    .'If you have any questions, please contact your lecturer or the campus office (info.ttmetro@gmail.com).');
            }

            $subject = $request->route('subject');
            if ($subject instanceof \App\Models\Subject) {
                if ($request->routeIs('lecturer.quizzes.*')) {
                    return redirect()->route('lecturer.quizzes.index', $subject->id)->with('error', 'That quiz no longer exists. It may have been deleted.');
                }
                if ($request->routeIs('admin.subjects.quizzes.*')) {
                    return redirect()->route('admin.subjects.quizzes.index', $subject->id)->with('error', 'That quiz no longer exists. It may have been deleted.');
                }
            }

            return null;
        });
    })->create();