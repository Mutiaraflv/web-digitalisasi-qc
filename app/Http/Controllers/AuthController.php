<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi tidak cocok.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        \App\Models\User::touchPresence(Auth::user(), $request, true);
        ActivityLog::record('login', 'Masuk ke sistem QCMS');

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $sessionId = $request->session()->getId();
        ActivityLog::record('logout', 'Keluar dari sistem QCMS', null, [], $userId);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        \App\Models\User::clearPresenceIfNoOtherSessions($userId, $sessionId);

        return redirect()->route('login');
    }
}
