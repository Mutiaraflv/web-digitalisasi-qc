<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class ApplyUserPreferences
{
    /**
     * Apply saved user theme & locale preferences cleanly on every web request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        $locale = $user?->locale
            ?? $request->session()->get('qc_locale')
            ?? $request->cookie('qc_locale')
            ?? 'id';
        if (!in_array($locale, ['id', 'en'], true)) {
            $locale = 'id';
        }

        $theme = $user?->theme
            ?? $request->session()->get('qc_theme')
            ?? $request->cookie('qc_theme')
            ?? 'light';
        if (!in_array($theme, ['light', 'dark'], true)) {
            $theme = 'light';
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);
        View::share('currentLocale', $locale);
        View::share('currentTheme', $theme);

        return $next($request);
    }
}

