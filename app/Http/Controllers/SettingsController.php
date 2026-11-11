<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $theme = $user?->theme ?? $request->session()->get('qc_theme', 'light');
        $locale = $user?->locale ?? $request->session()->get('qc_locale', app()->getLocale() ?: 'id');

        return view('settings.preferences', [
            'currentTheme' => in_array($theme, ['light', 'dark'], true) ? $theme : 'light',
            'currentLocale' => in_array($locale, ['id', 'en'], true) ? $locale : 'id',
        ]);
    }

    public function updatePreferences(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', Rule::in(['light', 'dark'])],
            'locale' => ['required', 'string', Rule::in(['id', 'en'])],
        ]);

        $theme = $validated['theme'];
        $locale = $validated['locale'];

        $user = Auth::user();
        if ($user) {
            $user->forceFill([
                'theme' => $theme,
                'locale' => $locale,
            ])->save();
        }

        $request->session()->put('qc_theme', $theme);
        $request->session()->put('qc_locale', $locale);
        App::setLocale($locale);

        $msg = __('qc.settings.preferences_saved');

        ActivityLog::record('preferences_updated', $msg, null, [
            'theme' => $theme,
            'locale' => $locale,
        ]);

        $themeCookie = cookie('qc_theme', $theme, 60 * 24 * 365);
        $localeCookie = cookie('qc_locale', $locale, 60 * 24 * 365);

        if ($request->wantsJson() || $request->is('api/*')) {
            if ($request->hasSession()) {
                $request->session()->flash('success', $msg);
            }
            return response()->json([
                'success' => true,
                'message' => $msg,
                'theme' => $theme,
                'locale' => $locale,
            ])->withCookie($themeCookie)->withCookie($localeCookie);
        }

        return redirect()->route('settings.preferences')
            ->with('success', $msg)
            ->withCookie($themeCookie)
            ->withCookie($localeCookie);
    }
}

