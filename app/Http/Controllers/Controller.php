<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

abstract class Controller
{
    /**
     * Where to go after saving or deleting: the page the user came from.
     *
     * - From an edit/create form: back to the page that opened the form (a filtered list,
     *   page 3 of a list, a parent page...). RememberPreviousPage records that.
     * - From a list (e.g. a delete button): the same list, with its filters and page kept.
     * - Otherwise: $fallback.
     *
     * Chain ->with('success', ...) on the result as with any redirect.
     */
    protected function returnTo(string $fallback): RedirectResponse
    {
        $previous = url()->previous();
        $current = request()->fullUrl();

        $isOurs = fn (?string $url) => $url
            && parse_url($url, PHP_URL_HOST) === request()->getHost()
            && ! preg_match('#/(login|logout)(/|$|\?)#', parse_url($url, PHP_URL_PATH) ?? '');

        if (! $isOurs($previous) || $previous === $current) {
            return redirect()->to($fallback);
        }

        $path = parse_url($previous, PHP_URL_PATH) ?? '';
        $isFormPage = (bool) preg_match('#/(edit|create)$#', $path);

        if ($isFormPage) {
            $origin = session('nav.back', [])[md5($previous)] ?? null;

            return redirect()->to($isOurs($origin) ? $origin : $fallback);
        }

        // Came from a list or another normal page: go back to it. If it was the page of the record
        // that has just been deleted (/things/12), that page no longer exists.
        if (request()->isMethod('DELETE') && preg_match('#/\d+$#', $path)) {
            $origin = session('nav.back', [])[md5($previous)] ?? null;

            return redirect()->to($isOurs($origin) ? $origin : $fallback);
        }

        return redirect()->to($previous);
    }
}
