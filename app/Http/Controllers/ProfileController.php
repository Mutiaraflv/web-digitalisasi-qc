<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
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
            if ($user->avatar && file_exists(public_path('uploads/avatars/' . $user->avatar))) {
                @unlink(public_path('uploads/avatars/' . $user->avatar));
            }

            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $updateData['avatar'] = $filename;
        }

        $user->update($updateData);

        ActivityLog::record(
            'profile_updated',
            "Memperbarui data profil akun ({$user->name})",
            null,
            ['old_name' => $oldName, 'name' => $user->name, 'email' => $user->email]
        );

        return redirect()->route('profile.show')
            ->with('success', app()->getLocale() === 'en'
                ? 'Profile details and avatar updated successfully.'
                : 'Informasi profil dan foto akun Anda berhasil diperbarui.');
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
            ['user' => $user->email, 'name' => $user->name]
        );

        return redirect()->route('profile.show')
            ->with('success', app()->getLocale() === 'en'
                ? 'Profile photo removed and reverted to initials.'
                : 'Foto profil berhasil dihapus dan kembali ke inisial nama.');
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
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            $err = app()->getLocale() === 'en'
                ? 'The current password you entered is incorrect.'
                : 'Kata sandi saat ini yang Anda masukkan tidak sesuai.';
            return back()->withErrors([
                'current_password' => $err,
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
            ->with('success', app()->getLocale() === 'en'
                ? 'Password updated successfully. Use your new password on your next login.'
                : 'Kata sandi berhasil diperbarui. Gunakan kata sandi baru saat login berikutnya.');
    }
}

