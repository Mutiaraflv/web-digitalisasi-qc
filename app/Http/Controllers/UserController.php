<?php

namespace App\Http\Controllers;

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

        if ($roleFilter && in_array($roleFilter, ['admin', 'user'])) {
            $query->where('role', $roleFilter);
        }

        $users = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'user' => User::where('role', 'user')->count(),
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
            'role' => ['required', 'string', Rule::in(['admin', 'user'])],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'role.required' => 'Peran (role) pengguna wajib dipilih.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('settings.users.index')
            ->with('success', "Akun '{$validated['name']}' berhasil ditambahkan ke sistem.");
    }

    /**
     * Update the specified user account.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'user'])],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'role.required' => 'Peran (role) pengguna wajib dipilih.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
        ]);

        // Pencegahan: Jangan biarkan admin yang sedang login menurunkan perannya sendiri jika tidak ada admin lain
        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak dapat menghapus hak Administrator dari akun Anda sendiri.');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('settings.users.index')
            ->with('success', "Data akun '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified user account.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Pencegahan 1: Jangan biarkan admin menghapus akunnya sendiri yang sedang aktif
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        // Pencegahan 2: Pastikan setidaknya masih ada satu admin di sistem
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus satu-satunya akun Administrator tersisa di sistem.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('settings.users.index')
            ->with('success', "Akun '{$name}' telah berhasil dihapus dari sistem.");
    }
}

