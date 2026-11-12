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

