<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile view.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('profile.show', compact('user'));
    }

    /**
     * Update user profile information (name, email, avatar).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'avatar.image' => 'File foto profil harus berupa gambar.',
            'avatar.mimes' => 'Format foto profil harus JPG, PNG, atau WEBP.',
            'avatar.max' => 'Ukuran foto profil maksimal 3 MB.',
        ]);

        $oldName = $user->name;
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($request->hasFile('avatar')) {
            $updateData['avatar'] = $this->processAvatarUpload($request->file('avatar'), $user);
        }

        $user->update($updateData);

        ActivityLog::record(
            'profile_updated',
            "Memperbarui data profil akun ({$user->name})",
            null,
            ['old_name' => $oldName, 'name' => $user->name, 'email' => $user->email]
        );

        return redirect()->route('profile.show')
            ->with('success', 'Informasi profil akun Anda berhasil diperbarui.');
    }

    /**
     * Upload or replace the user's avatar image directly.
     */
    public function uploadAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'avatar.required' => 'Pilih file foto profil terlebih dahulu.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'avatar.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $filename = $this->processAvatarUpload($request->file('avatar'), $user);
        $user->update(['avatar' => $filename]);

        ActivityLog::record(
            'profile_updated',
            "Memperbarui foto profil akun ({$user->name})",
            null,
            ['user' => $user->email]
        );

        return redirect()->route('profile.show')
            ->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Store and optimize an uploaded avatar image into public/uploads/avatars.
     * Crops image to 1:1 square ratio, resizes to max 512x512, and saves as optimized WebP or JPEG.
     */
    private function processAvatarUpload($file, $user): string
    {
        $dir = public_path('uploads/avatars');
        if (!file_exists($dir)) {
            @mkdir($dir, 0755, true);
        }

        if ($user->avatar && file_exists($dir . '/' . $user->avatar)) {
            @unlink($dir . '/' . $user->avatar);
        }

        $useWebp = function_exists('imagewebp');
        $ext = $useWebp ? 'webp' : 'jpg';
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $ext;
        $destPath = $dir . '/' . $filename;

        $srcPath = $file->getRealPath();
        $img = null;
        $imgType = @exif_imagetype($srcPath);

        if ($imgType === IMAGETYPE_JPEG) {
            $img = @imagecreatefromjpeg($srcPath);
            if ($img && function_exists('exif_read_data')) {
                $exif = @exif_read_data($srcPath);
                $ort = $exif['Orientation'] ?? 1;
                $rot = match ($ort) {
                    3 => imagerotate($img, 180, 0),
                    6 => imagerotate($img, -90, 0),
                    8 => imagerotate($img, 90, 0),
                    default => null,
                };
                if ($rot) {
                    imagedestroy($img);
                    $img = $rot;
                }
            }
        } elseif ($imgType === IMAGETYPE_PNG) {
            $img = @imagecreatefrompng($srcPath);
        } elseif ($imgType === IMAGETYPE_WEBP && function_exists('imagecreatefromwebp')) {
            $img = @imagecreatefromwebp($srcPath);
        }

        if ($img) {
            $w = imagesx($img);
            $h = imagesy($img);
            $side = min($w, $h);
            $cropX = (int) round(($w - $side) / 2);
            $cropY = (int) round(($h - $side) / 2);

            $targetSize = min(512, $side);
            $dst = imagecreatetruecolor($targetSize, $targetSize);

            // Clean white background for transparency
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefilledrectangle($dst, 0, 0, $targetSize, $targetSize, $white);

            imagecopyresampled($dst, $img, 0, 0, $cropX, $cropY, $targetSize, $targetSize, $side, $side);
            imagedestroy($img);

            if ($useWebp) {
                imagewebp($dst, $destPath, 85);
            } else {
                imagejpeg($dst, $destPath, 85);
            }
            imagedestroy($dst);
        } else {
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
        }

        return $filename;
    }

    /**
     * Remove the user's uploaded avatar.
     */
    public function destroyAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar && file_exists(public_path('uploads/avatars/' . $user->avatar))) {
            @unlink(public_path('uploads/avatars/' . $user->avatar));
        }

        $user->update(['avatar' => null]);

        ActivityLog::record(
            'profile_updated',
            "Menghapus foto profil akun ({$user->name}) dan kembali ke inisial nama",
            null,
            ['user' => $user->email]
        );

        return redirect()->route('profile.show')
            ->with('success', 'Foto profil berhasil dihapus dan kembali ke inisial nama.');
    }

    /**
     * Upload or draw/save the user's digital signature.
     */
    public function uploadSignature(Request $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Check if canvas base64 data was submitted
        if ($request->filled('signature_data')) {
            $dataUrl = (string) $request->input('signature_data');
            if (preg_match('#^data:image/(png|jpeg|jpg|webp);base64,(.+)$#i', $dataUrl, $m)) {
                $bin = base64_decode($m[2]);
                if ($bin !== false && strlen($bin) > 20 && strlen($bin) < 5_000_000) {
                    $dir = public_path('uploads/signatures');
                    if (!file_exists($dir)) {
                        @mkdir($dir, 0755, true);
                    }
                    if ($user->signature && file_exists($dir . '/' . $user->signature)) {
                        @unlink($dir . '/' . $user->signature);
                    }
                    $filename = 'sig_' . $user->id . '_' . time() . '.png';
                    $destPath = $dir . '/' . $filename;
                    $this->cropAndSaveSignatureImage($bin, $destPath);

                    $user->update(['signature' => $filename]);

                    ActivityLog::record(
                        'profile_updated',
                        "Memperbarui tanda tangan digital akun ({$user->name}) via kanvas",
                        null,
                        ['user' => $user->email]
                    );

                    return redirect()->route('profile.show')
                        ->with('success', 'Tanda tangan digital berhasil disimpan di profil.');
                }
            }
            return redirect()->route('profile.show')
                ->with('error', 'Format data tanda tangan tidak valid.');
        }

        // 2. Check if file was uploaded
        $request->validate([
            'signature_file' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'signature_file.required' => 'Pilih file gambar tanda tangan atau gunakan kanvas.',
            'signature_file.image' => 'File harus berupa gambar.',
            'signature_file.mimes' => 'Format file tanda tangan harus PNG, JPG, atau WEBP.',
            'signature_file.max' => 'Ukuran file tanda tangan maksimal 5 MB.',
        ]);

        $file = $request->file('signature_file');
        $dir = public_path('uploads/signatures');
        if (!file_exists($dir)) {
            @mkdir($dir, 0755, true);
        }
        if ($user->signature && file_exists($dir . '/' . $user->signature)) {
            @unlink($dir . '/' . $user->signature);
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
            $ext = 'png';
        }
        $filename = 'sig_' . $user->id . '_' . time() . '.' . $ext;
        $destPath = $dir . '/' . $filename;
        $rawBytes = @file_get_contents($file->getRealPath());
        if ($rawBytes && $this->cropAndSaveSignatureImage($rawBytes, $destPath)) {
            // Berhasil dipangkas & dioptimasi
        } else {
            $file->move($dir, $filename);
        }

        $user->update(['signature' => $filename]);

        ActivityLog::record(
            'profile_updated',
            "Mengunggah gambar tanda tangan digital akun ({$user->name})",
            null,
            ['user' => $user->email]
        );

        return redirect()->route('profile.show')
            ->with('success', 'Gambar tanda tangan digital berhasil diperbarui.');
    }

    /**
     * Pangkas area transparan/kosong berlebih di sekeliling goresan tanda tangan.
     */
    private function cropAndSaveSignatureImage(string $binaryData, string $destPath, int $pad = 8): bool
    {
        if (!extension_loaded('gd') || !function_exists('imagecreatefromstring')) {
            return (bool) @file_put_contents($destPath, $binaryData);
        }

        $im = @imagecreatefromstring($binaryData);
        if (!$im) {
            return (bool) @file_put_contents($destPath, $binaryData);
        }

        $w = imagesx($im);
        $h = imagesy($im);
        $minX = $w; $maxX = -1; $minY = $h; $maxY = -1;

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgba = imagecolorat($im, $x, $y);
                $alpha = ($rgba >> 24) & 0x7F;
                if ($alpha < 125) { // piksel tidak transparan
                    if ($x < $minX) $minX = $x;
                    if ($x > $maxX) $maxX = $x;
                    if ($y < $minY) $minY = $y;
                    if ($y > $maxY) $maxY = $y;
                }
            }
        }

        if ($maxX !== -1) {
            $cropX = max(0, $minX - $pad);
            $cropY = max(0, $minY - $pad);
            $cropW = min($w - 1, $maxX + $pad) - $cropX + 1;
            $cropH = min($h - 1, $maxY + $pad) - $cropY + 1;

            $cropped = imagecreatetruecolor($cropW, $cropH);
            imagealphablending($cropped, false);
            imagesavealpha($cropped, true);
            $trans = imagecolorallocatealpha($cropped, 0, 0, 0, 127);
            imagefilledrectangle($cropped, 0, 0, $cropW, $cropH, $trans);
            imagecopy($cropped, $im, 0, 0, $cropX, $cropY, $cropW, $cropH);

            imagedestroy($im);
            $im = $cropped;
        }

        imagealphablending($im, false);
        imagesavealpha($im, true);
        $saved = (bool) @imagepng($im, $destPath, 6);
        imagedestroy($im);
        return $saved;
    }

    /**
     * Remove the user's digital signature.
     */
    public function destroySignature(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->signature && file_exists(public_path('uploads/signatures/' . $user->signature))) {
            @unlink(public_path('uploads/signatures/' . $user->signature));
        }

        $user->update(['signature' => null]);

        ActivityLog::record(
            'profile_updated',
            "Menghapus tanda tangan digital akun ({$user->name})",
            null,
            ['user' => $user->email]
        );

        return redirect()->route('profile.show')
            ->with('success', 'Tanda tangan digital berhasil dihapus dari profil.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Kata sandi saat ini yang Anda masukkan tidak sesuai.',
            ])->withInput();
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLog::record(
            'password_updated',
            "Memperbarui kata sandi akun ({$user->name})",
            null,
            ['user' => $user->email]
        );

        return redirect()->route('profile.show')
            ->with('success', 'Kata sandi berhasil diperbarui. Gunakan kata sandi baru saat login berikutnya.');
    }
}

