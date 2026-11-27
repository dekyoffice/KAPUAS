<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna sistem.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $query = User::withCount('arsip');

        if (!empty($search)) {
            $escaped = \App\Support\StrHelper::escapeLike($search);
            $query->where(function ($q) use ($escaped) {
                $q->where('nama_user', 'like', "%{$escaped}%")
                  ->orWhere('email', 'like', "%{$escaped}%");
            });
        }

        if (!empty($role)) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('role')->orderBy('nama_user')->paginate(10)->withQueryString();

        return view('users.index', [
            'users' => $users,
            'search' => $search,
            'selectedRole' => $role,
        ]);
    }

    /**
     * Tampilkan form penambahan pengguna baru.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Simpan pengguna baru ke database.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'nama_user' => $validated['nama_user'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
            'email_verified_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Pengguna '{$validated['nama_user']}' dengan peran " . ucfirst($validated['role']) . " berhasil didaftarkan.");
    }

    /**
     * Tampilkan formulir edit pengguna.
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Perbarui data pengguna.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();
        $isCurrentLoggedInUser = ($user->id_user === auth()->user()->id_user);

        // Aturan (a): admin yang sedang login TIDAK boleh mengubah role ke non-admin atau status ke nonaktif
        if ($isCurrentLoggedInUser) {
            if ($validated['role'] !== 'admin') {
                return redirect()->route('users.index')
                    ->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri.');
            }
            if ($validated['status'] !== 'aktif') {
                return redirect()->route('users.index')
                    ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
            }
        }

        // Aturan (b): tidak boleh ada perubahan yang membuat jumlah admin aktif menjadi 0
        $willBeActiveAdmin = ($validated['role'] === 'admin' && $validated['status'] === 'aktif');
        $wasActiveAdmin = ($user->role === 'admin' && $user->status === 'aktif');
        if ($wasActiveAdmin && !$willBeActiveAdmin) {
            $otherActiveAdmins = User::where('role', 'admin')
                ->where('status', 'aktif')
                ->where('id_user', '!=', $user->id_user)
                ->count();

            if ($otherActiveAdmins === 0) {
                return redirect()->route('users.index')
                    ->with('error', 'Tidak dapat mengubah akun ini karena sistem harus memiliki setidaknya satu Administrator aktif.');
            }
        }

        $updateData = [
            'nama_user' => $validated['nama_user'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ];

        // Jika password diisi, update password
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', "Data pengguna '{$user->nama_user}' berhasil diperbarui.");
    }

    /**
     * Hapus pengguna dengan proteksi akun mandiri, admin aktif terakhir, dan relasi data arsip.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Cegah Admin menghapus akunnya sendiri yang sedang aktif
        if ($user->id_user === auth()->user()->id_user) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang masuk.');
        }

        // Aturan (b): tidak boleh menghapus admin yang membuat jumlah admin aktif menjadi 0
        if ($user->role === 'admin' && $user->status === 'aktif') {
            $otherActiveAdmins = User::where('role', 'admin')
                ->where('status', 'aktif')
                ->where('id_user', '!=', $user->id_user)
                ->count();

            if ($otherActiveAdmins === 0) {
                return redirect()->route('users.index')
                    ->with('error', 'Tidak dapat menghapus akun ini karena sistem harus memiliki setidaknya satu Administrator aktif.');
            }
        }

        // Cek keterikatan dengan data arsip
        $arsipCount = $user->arsip()->count();
        if ($arsipCount > 0) {
            return redirect()->route('users.index')
                ->with('error', "Pengguna '{$user->nama_user}' tidak dapat dihapus karena masih memiliki {$arsipCount} data arsip yang diunggah. Alihkan arsip terlebih dahulu jika ingin menghapus.");
        }

        $nama = $user->nama_user;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Pengguna '{$nama}' berhasil dihapus dari sistem.");
    }
}
