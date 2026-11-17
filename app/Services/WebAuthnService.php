<?php

namespace App\Services;

use RuntimeException;

/**
 * Verifikasi WebAuthn / Passkey minimal tanpa paket tambahan.
 *
 * Pemindaian wajah/sidik jari dilakukan sepenuhnya oleh perangkat pengguna
 * (Face ID, Windows Hello, buka kunci wajah Android). Server hanya menerima
 * kunci publik saat pendaftaran dan tanda tangan digital saat login.
 *
 * Kunci publik dikirim browser dalam format SPKI (AuthenticatorAttestationResponse.getPublicKey()),
 * sehingga tidak perlu parser CBOR. Attestation yang dipakai: "none".
 */
class WebAuthnService
{
    public const ALG_ES256 = -7;
    public const ALG_RS256 = -257;

    public const SUPPORTED_ALGORITHMS = [self::ALG_ES256, self::ALG_RS256];

    private const FLAG_USER_PRESENT = 0x01;
    private const FLAG_USER_VERIFIED = 0x04;
    private const FLAG_ATTESTED_DATA = 0x40;

    // ------------------------------------------------------------------
    // Helper encoding
    // ------------------------------------------------------------------

    public static function b64urlEncode(string $binary): string
    {
        return rtrim(strtr(base64_encode($binary), '+/', '-_'), '=');
    }

    public static function b64urlDecode(string $value): string
    {
        $value = strtr(trim($value), '-_', '+/');
        $pad = strlen($value) % 4;
        if ($pad) {
            $value .= str_repeat('=', 4 - $pad);
        }
        $decoded = base64_decode($value, true);
        if ($decoded === false) {
            throw new RuntimeException('Data base64url tidak valid.');
        }

        return $decoded;
    }

    public static function newChallenge(): string
    {
        return self::b64urlEncode(random_bytes(32));
    }

    /**
     * ID pengguna (user handle) yang disimpan di perangkat: hash, bukan email/ID asli.
     */
    public static function userHandle(int|string $userId, string $appKey): string
    {
        return hash_hmac('sha256', 'qcms-passkey-user:' . $userId, $appKey, true);
    }

    // ------------------------------------------------------------------
    // Pendaftaran
    // ------------------------------------------------------------------

    /**
     * @param  array{clientDataJSON:string, authenticatorData:string, publicKey:string, publicKeyAlgorithm:int|string}  $response  (semua base64url)
     * @return array{credential_id:string, public_key:string, algorithm:int, sign_count:int}
     */
    public function verifyRegistration(string $rawIdB64, array $response, string $expectedChallenge, string $rpId): array
    {
        foreach (['clientDataJSON', 'authenticatorData', 'publicKey', 'publicKeyAlgorithm'] as $key) {
            if (! isset($response[$key]) || $response[$key] === '') {
                throw new RuntimeException('Browser ini belum mendukung pendaftaran passkey. Perbarui browser Anda.');
            }
        }

        $rawId = self::b64urlDecode($rawIdB64);
        $clientDataJson = self::b64urlDecode($response['clientDataJSON']);
        $this->verifyClientData($clientDataJson, 'webauthn.create', $expectedChallenge, $rpId);

        $authData = self::b64urlDecode($response['authenticatorData']);
        $parsed = $this->parseAuthenticatorData($authData, $rpId);

        if (! ($parsed['flags'] & self::FLAG_ATTESTED_DATA)) {
            throw new RuntimeException('Data kredensial dari perangkat tidak lengkap.');
        }

        // Pastikan ID kredensial di authenticatorData sama dengan rawId
        if (strlen($authData) < 55) {
            throw new RuntimeException('Data kredensial dari perangkat tidak lengkap.');
        }
        $credIdLen = unpack('n', substr($authData, 53, 2))[1];
        $credIdInAuth = substr($authData, 55, $credIdLen);
        if ($credIdLen < 1 || ! hash_equals($credIdInAuth, $rawId)) {
            throw new RuntimeException('ID kredensial tidak cocok.');
        }

        $algorithm = (int) $response['publicKeyAlgorithm'];
        if (! in_array($algorithm, self::SUPPORTED_ALGORITHMS, true)) {
            throw new RuntimeException('Jenis kunci perangkat ini belum didukung.');
        }

        $pem = self::spkiToPem(self::b64urlDecode($response['publicKey']));
        $this->assertKeyMatchesAlgorithm($pem, $algorithm);

        return [
            'credential_id' => self::b64urlEncode($rawId),
            'public_key' => $pem,
            'algorithm' => $algorithm,
            'sign_count' => $parsed['sign_count'],
        ];
    }

    // ------------------------------------------------------------------
    // Login
    // ------------------------------------------------------------------

    /**
     * @param  array{clientDataJSON:string, authenticatorData:string, signature:string, userHandle?:string|null}  $response  (base64url)
     * @return int sign count baru
     */
    public function verifyAssertion(
        array $response,
        string $expectedChallenge,
        string $rpId,
        string $publicKeyPem,
        int $storedSignCount,
        ?string $expectedUserHandle = null
    ): int {
        foreach (['clientDataJSON', 'authenticatorData', 'signature'] as $key) {
            if (empty($response[$key])) {
                throw new RuntimeException('Respons perangkat tidak lengkap.');
            }
        }

        $clientDataJson = self::b64urlDecode($response['clientDataJSON']);
        $this->verifyClientData($clientDataJson, 'webauthn.get', $expectedChallenge, $rpId);

        $authData = self::b64urlDecode($response['authenticatorData']);
        $parsed = $this->parseAuthenticatorData($authData, $rpId);

        if ($expectedUserHandle !== null && ! empty($response['userHandle'])) {
            if (! hash_equals($expectedUserHandle, self::b64urlDecode($response['userHandle']))) {
                throw new RuntimeException('Passkey tidak cocok dengan akun.');
            }
        }

        $signedData = $authData . hash('sha256', $clientDataJson, true);
        $signature = self::b64urlDecode($response['signature']);

        $key = openssl_pkey_get_public($publicKeyPem);
        if ($key === false) {
            throw new RuntimeException('Kunci publik tersimpan rusak.');
        }

        if (openssl_verify($signedData, $signature, $key, OPENSSL_ALGO_SHA256) !== 1) {
            throw new RuntimeException('Verifikasi tanda tangan perangkat gagal.');
        }

        // Deteksi kloning perangkat (passkey yang disinkronkan biasanya selalu 0)
        $newCount = $parsed['sign_count'];
        if ($newCount !== 0 || $storedSignCount !== 0) {
            if ($newCount <= $storedSignCount) {
                throw new RuntimeException('Penghitung keamanan perangkat tidak valid.');
            }
        }

        return $newCount;
    }

    // ------------------------------------------------------------------
    // Internal
    // ------------------------------------------------------------------

    private function verifyClientData(string $json, string $expectedType, string $expectedChallenge, string $rpId): void
    {
        $data = json_decode($json, true);
        if (! is_array($data)) {
            throw new RuntimeException('Data klien tidak valid.');
        }

        if (($data['type'] ?? null) !== $expectedType) {
            throw new RuntimeException('Jenis permintaan tidak sesuai.');
        }

        if (! is_string($data['challenge'] ?? null)
            || ! hash_equals($expectedChallenge, rtrim($data['challenge'], '='))) {
            throw new RuntimeException('Sesi verifikasi kedaluwarsa. Silakan coba lagi.');
        }

        $origin = (string) ($data['origin'] ?? '');
        $originHost = strtolower((string) parse_url($origin, PHP_URL_HOST));
        $originScheme = strtolower((string) parse_url($origin, PHP_URL_SCHEME));

        if ($originHost !== strtolower($rpId)) {
            throw new RuntimeException('Alamat situs tidak cocok.');
        }

        $isLocal = $originHost === 'localhost';
        if ($originScheme !== 'https' && ! ($isLocal && $originScheme === 'http')) {
            throw new RuntimeException('Login dengan kunci perangkat hanya bisa dipakai melalui HTTPS.');
        }

        if (! empty($data['crossOrigin'])) {
            throw new RuntimeException('Permintaan lintas situs ditolak.');
        }
    }

    /**
     * @return array{flags:int, sign_count:int}
     */
    private function parseAuthenticatorData(string $authData, string $rpId): array
    {
        if (strlen($authData) < 37) {
            throw new RuntimeException('Data autentikator tidak valid.');
        }

        if (! hash_equals(hash('sha256', $rpId, true), substr($authData, 0, 32))) {
            throw new RuntimeException('Passkey ini terdaftar untuk alamat situs lain.');
        }

        $flags = ord($authData[32]);
        if (! ($flags & self::FLAG_USER_PRESENT)) {
            throw new RuntimeException('Kehadiran pengguna tidak terkonfirmasi.');
        }
        if (! ($flags & self::FLAG_USER_VERIFIED)) {
            throw new RuntimeException('Verifikasi biometrik / kunci perangkat tidak dilakukan.');
        }

        return [
            'flags' => $flags,
            'sign_count' => unpack('N', substr($authData, 33, 4))[1],
        ];
    }

    private static function spkiToPem(string $der): string
    {
        $pem = "-----BEGIN PUBLIC KEY-----\n"
            . chunk_split(base64_encode($der), 64, "\n")
            . "-----END PUBLIC KEY-----\n";

        if (openssl_pkey_get_public($pem) === false) {
            throw new RuntimeException('Kunci publik dari perangkat tidak valid.');
        }

        return $pem;
    }

    private function assertKeyMatchesAlgorithm(string $pem, int $algorithm): void
    {
        $details = openssl_pkey_get_details(openssl_pkey_get_public($pem));
        $type = $details['type'] ?? null;

        $ok = match ($algorithm) {
            self::ALG_ES256 => $type === OPENSSL_KEYTYPE_EC && ($details['ec']['curve_name'] ?? '') === 'prime256v1',
            self::ALG_RS256 => $type === OPENSSL_KEYTYPE_RSA && ($details['bits'] ?? 0) >= 2048,
            default => false,
        };

        if (! $ok) {
            throw new RuntimeException('Jenis kunci perangkat tidak sesuai.');
        }
    }
}
