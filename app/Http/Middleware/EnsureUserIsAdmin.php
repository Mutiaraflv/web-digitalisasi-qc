<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, app()->getLocale() === 'en'
                ? 'Access denied. Manage Accounts can only be accessed by Administrators.'
                : 'Akses ditolak. Menu Kelola Akun hanya dapat diakses oleh Administrator.');
        }

        return $next($request);
    }
}

