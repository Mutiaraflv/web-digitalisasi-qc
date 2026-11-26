<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackUserPresence
{
    public const IDLE_TIMEOUT_SECONDS = 1200; // 20 minutes

    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            if ($request->hasSession()) {
                $lastActivity = (int) $request->session()->get('_last_user_activity', 0);
                $now = time();

                // If _last_user_activity was recorded and exceeds 20 minutes of inactivity, force auto-logout
                if ($lastActivity > 0 && ($now - $lastActivity) > self::IDLE_TIMEOUT_SECONDS) {
                    $userId = $user->id;
                    $sessionId = $request->session()->getId();

                    ActivityLog::record('logout', 'Sesi berakhir otomatis (tidak ada aktivitas selama 20 menit)', null, [], $userId);

                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    User::clearPresenceIfNoOtherSessions($userId, $sessionId);

                    if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                        return response()->json([
                            'error' => 'session_timeout',
                            'message' => __('qc.auth.timeout_message'),
                            'redirect' => route('login', ['timeout' => 1]),
                        ], 401)->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
                    }

                    return redirect()->route('login', ['timeout' => 1])
                        ->with('timeout', __('qc.auth.timeout_message'));
                }

                // If request is user interaction or normal navigation, refresh last user activity
                $isBackgroundRoute = $request->routeIs('dashboard.liveData', 'presence.heartbeat');
                $isUserActive = $request->header('X-QC-User-Active') === '1';

                if (! $isBackgroundRoute || $isUserActive || $lastActivity === 0) {
                    $request->session()->put('_last_user_activity', $now);
                }
            }

            User::touchPresence($user, $request);
        }

        return $next($request);
    }
}

