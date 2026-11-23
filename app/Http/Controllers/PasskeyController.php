<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Passkey;
use App\Models\User;
use App\Services\WebAuthnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

/**
 * Login dengan wajah / sidik jari menggunakan Passkey (WebAuthn).
 */
class PasskeyController extends Controller
{
    private const CHALLENGE_TTL = 300; // detik
    private const MAX_PASSKEYS_PER_USER = 10;

    public function __construct(private readonly WebAuthnService $webauthn)
    {
    }

    // ------------------------------------------------------------------
    // Pendaftaran (dari halaman Profil, user sudah login)
    // ------------------------------------------------------------------

    public function registerOptions(Request $request): JsonResponse
    {
        if (! Passkey::tableExists()) {
            return $this->fail(__('qc.passkey.err_not_migrated'), 503);
        }

        /** @var User $user */
        $user = $request->user();

        if ($user->passkeys()->count() >= self::MAX_PASSKEYS_PER_USER) {
            return $this->fail(__('qc.passkey.err_limit', ['max' => self::MAX_PASSKEYS_PER_USER]));
        }

        $challenge = WebAuthnService::newChallenge();
        $request->session()->put('passkey.register', [
            'challenge' => $challenge,
            'rp_id' => $this->rpId($request),
            'expires' => time() + self::CHALLENGE_TTL,
        ]);

        return response()->json([
            'challenge' => $challenge,
            'rp' => ['name' => 'QCMS PT SSC Works', 'id' => $this->rpId($request)],
            'user' => [
                'id' => WebAuthnService::b64urlEncode($this->userHandle($user)),
                'name' => $user->email,
                'displayName' => $user->name,
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => WebAuthnService::ALG_ES256],
                ['type' => 'public-key', 'alg' => WebAuthnService::ALG_RS256],
            ],
            'authenticatorSelection' => [
                'residentKey' => 'required',
                'requireResidentKey' => true,
                'userVerification' => 'required',
            ],
            'excludeCredentials' => $user->passkeys()->pluck('credential_id')->map(fn ($id) => [
                'type' => 'public-key',
                'id' => $id,
            ])->values(),
            'attestation' => 'none',
            'timeout' => 120000,
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        if (! Passkey::tableExists()) {
            return $this->fail(__('qc.passkey.err_not_migrated'), 503);
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:60'],
            'rawId' => ['required', 'string', 'max:1024'],
            'response' => ['required', 'array'],
            'response.clientDataJSON' => ['required', 'string'],
            'response.authenticatorData' => ['nullable', 'string'],
            'response.publicKey' => ['nullable', 'string'],
            'response.publicKeyAlgorithm' => ['nullable', 'integer'],
            'response.transports' => ['nullable', 'array'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $pending = $this->pullChallenge($request, 'passkey.register');
        if (! $pending) {
            return $this->fail(__('qc.passkey.err_expired'));
        }

        try {
            $result = $this->webauthn->verifyRegistration(
                $validated['rawId'],
                $validated['response'],
                $pending['challenge'],
                $pending['rp_id']
            );
        } catch (RuntimeException $e) {
            return $this->fail($e->getMessage());
        }

        if (Passkey::where('credential_id', $result['credential_id'])->exists()) {
            return $this->fail(__('qc.passkey.err_duplicate'));
        }

        $transports = array_values(array_filter(
            (array) ($validated['response']['transports'] ?? []),
            fn ($t) => is_string($t) && strlen($t) <= 20
        ));

        $passkey = $user->passkeys()->create([
            'name' => trim((string) ($validated['name'] ?? '')) ?: __('qc.passkey.default_name'),
            'credential_id' => $result['credential_id'],
            'public_key' => $result['public_key'],
            'algorithm' => $result['algorithm'],
            'sign_count' => $result['sign_count'],
            'transports' => $transports ?: null,
            'rp_id' => $pending['rp_id'],
        ]);

        ActivityLog::record(
            'passkey_added',
            "Mendaftarkan kunci perangkat: {$passkey->name}",
            null,
            ['subject' => $passkey->name]
        );

        return response()->json([
            'ok' => true,
            'message' => __('qc.passkey.registered'),
        ]);
    }

    public function destroy(Request $request, Passkey $passkey): RedirectResponse
    {
        abort_unless($passkey->user_id === $request->user()->id, 403);

        $name = $passkey->name;
        $passkey->delete();

        ActivityLog::record(
            'passkey_removed',
            "Menghapus kunci perangkat: {$name}",
            null,
            ['subject' => $name]
        );

        return redirect()->route('profile.show')
            ->with('success', __('qc.passkey.removed', ['name' => $name]));
    }

    // ------------------------------------------------------------------
    // Login (halaman login, belum masuk)
    // ------------------------------------------------------------------

    public function loginOptions(Request $request): JsonResponse
    {
        if (! Passkey::tableExists()) {
            return $this->fail(__('qc.passkey.err_not_migrated'), 503);
        }

        if (Passkey::count() === 0) {
            return $this->fail(__('qc.passkey.err_none_registered'), 422);
        }

        $challenge = WebAuthnService::newChallenge();
        $request->session()->put('passkey.login', [
            'challenge' => $challenge,
            'rp_id' => $this->rpId($request),
            'expires' => time() + self::CHALLENGE_TTL,
        ]);

        return response()->json([
            'challenge' => $challenge,
            'rpId' => $this->rpId($request),
            'allowCredentials' => [],
            'userVerification' => 'required',
            'timeout' => 120000,
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        if (! Passkey::tableExists()) {
            return $this->fail(__('qc.passkey.err_not_migrated'), 503);
        }

        $validated = $request->validate([
            'rawId' => ['required', 'string', 'max:1024'],
            'response' => ['required', 'array'],
            'response.clientDataJSON' => ['required', 'string'],
            'response.authenticatorData' => ['required', 'string'],
            'response.signature' => ['required', 'string'],
            'response.userHandle' => ['nullable', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $pending = $this->pullChallenge($request, 'passkey.login');
        if (! $pending) {
            return $this->fail(__('qc.passkey.err_expired'));
        }

        try {
            $credentialId = WebAuthnService::b64urlEncode(WebAuthnService::b64urlDecode($validated['rawId']));
        } catch (RuntimeException) {
            return $this->fail(__('qc.passkey.err_unknown'));
        }

        $passkey = Passkey::with('user')->where('credential_id', $credentialId)->first();
        if (! $passkey || ! $passkey->user) {
            return $this->fail(__('qc.passkey.err_unknown'));
        }

        try {
            $newCount = $this->webauthn->verifyAssertion(
                $validated['response'],
                $pending['challenge'],
                $pending['rp_id'],
                $passkey->public_key,
                (int) $passkey->sign_count,
                $this->userHandle($passkey->user)
            );
        } catch (RuntimeException $e) {
            return $this->fail($e->getMessage());
        }

        $passkey->forceFill([
            'sign_count' => $newCount,
            'last_used_at' => now(),
        ])->save();

        Auth::login($passkey->user, false);
        $request->session()->regenerate();
        $request->session()->put('_last_user_activity', time());

        User::touchPresence(Auth::user(), $request, true);
        ActivityLog::record('login', 'Masuk ke sistem QCMS dengan kunci perangkat (Passkey)', null, [
            'method' => 'passkey',
            'device' => $passkey->name,
        ]);

        return response()->json([
            'ok' => true,
            'redirect' => $request->session()->pull('url.intended', route('dashboard')),
        ]);
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    private function rpId(Request $request): string
    {
        return strtolower($request->getHost());
    }

    private function userHandle(User $user): string
    {
        return WebAuthnService::userHandle($user->getKey(), (string) config('app.key'));
    }

    /**
     * Ambil challenge sekali pakai dari session.
     *
     * @return array{challenge:string, rp_id:string}|null
     */
    private function pullChallenge(Request $request, string $key): ?array
    {
        $pending = $request->session()->pull($key);
        if (! is_array($pending) || ($pending['expires'] ?? 0) < time()) {
            return null;
        }

        return $pending;
    }

    private function fail(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['ok' => false, 'message' => $message], $status);
    }
}
