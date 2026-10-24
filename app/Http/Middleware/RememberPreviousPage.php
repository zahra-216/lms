<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Remembers which page each page was opened from, so that saving or deleting
 * (see Controller::returnTo) can send people back where they were.
 */
class RememberPreviousPage
{
    private const LIMIT = 40;

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && ! $request->ajax() && ! $request->wantsJson()) {
            $from = $request->headers->get('referer');
            $here = $request->fullUrl();

            if ($from && $from !== $here && parse_url($from, PHP_URL_HOST) === $request->getHost()) {
                $map = $request->session()->get('nav.back', []);
                $map[md5($here)] = $from;

                // keep the newest entries only
                $request->session()->put('nav.back', array_slice($map, -self::LIMIT, null, true));
            }
        }

        return $next($request);
    }
}
