<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of user accounts.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $roleFilter = $request->query('role');

        $query = User::query()->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter && in_array($roleFilter, array_keys(User::roleLabels()), true)) {
            $query->where('role', $roleFilter);
        }

        $users = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', User::ROLE_ADMIN)->count(),
            'qc_leader' => User::where('role', User::ROLE_QC_LEADER)->count(),
            'user' => User::where('role', User::ROLE_USER)->count(),
        ];

        return view('settings.users.index', compact('users', 'stats', 'search', 'roleFilter'));
    }

    /**
     * Store a newly created user account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', Rule::in(array_keys(User::roleLabels()))],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'role.required' => 'Role pengguna wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLog::record('user_created', "Menambahkan akun baru {$newUser->name} ({$newUser->roleLabel()})", null, [
            'subject' => $newUser->name,
            'role' => $newUser->role,
        ]);

        $msg = app()->getLocale() === 'en'
            ? "Account '{$validated['name']}' added successfully."
            : "Akun '{$validated['name']}' berhasil ditambahkan ke sistem.";

        return redirect()->route('settings.users.index')->with('success', $msg);
    }

    /**
     * Update the specified user account.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(array_keys(User::roleLabels()))],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            $err = app()->getLocale() === 'en'
                ? 'You cannot remove Administrator role from your own account.'
                : 'Anda tidak dapat menghapus hak Administrator dari akun Anda sendiri.';
            return back()->with('error', $err);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        ActivityLog::record('user_updated', "Memperbarui akun {$user->name} ({$user->roleLabel()})", null, [
            'subject' => $user->name,
            'role' => $user->role,
        ]);

        $msg = app()->getLocale() === 'en'
            ? "Account '{$user->name}' updated successfully."
            : "Data akun '{$user->name}' berhasil diperbarui.";

        return redirect()->route('settings.users.index')->with('success', $msg);
    }

    /**
     * Remove the specified user account.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            $err = app()->getLocale() === 'en'
                ? 'You cannot delete your own active account.'
                : 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.';
            return back()->with('error', $err);
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            $err = app()->getLocale() === 'en'
                ? 'Cannot delete the only remaining Administrator account.'
                : 'Tidak dapat menghapus satu-satunya akun Administrator tersisa di sistem.';
            return back()->with('error', $err);
        }

        try {
            $name = $user->name;
            $user->delete();

            ActivityLog::record('user_deleted', "Menghapus akun {$name}", null, [
                'subject' => $name,
            ]);

            $msg = app()->getLocale() === 'en'
                ? "Account '{$name}' deleted from the system."
                : "Akun '{$name}' telah berhasil dihapus dari sistem.";

            return redirect()->route('settings.users.index')->with('success', $msg);
        } catch (\Throwable $e) {
            \Log::error("Failed to delete user ID {$user->id}: " . $e->getMessage());
            $err = app()->getLocale() === 'en'
                ? "Failed to delete account '{$user->name}'. It may be linked to existing inspection data."
                : "Gagal menghapus akun '{$user->name}'. Akun kemungkinan terkait dengan data inspeksi.";
            return back()->with('error', $err);
        }
    }
}

