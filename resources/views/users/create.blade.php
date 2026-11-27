@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')
@section('header-title', 'Tambah Pengguna Baru')
@section('header-subtitle', 'Pendaftaran akun dan penugasan peran akses sistem')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('users.index') }}" class="hover:text-indigo-600">Kelola Pengguna</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Tambah Baru</span>
    </nav>

    <!-- Main Card Form -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-base font-bold text-slate-900">Formulir Akun Pengguna</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pastikan alamat email valid dan kata sandi memenuhi standar keamanan.</p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Nama Pengguna -->
            <div>
                <label for="nama_user" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <div class="mt-2">
                    <input type="text" id="nama_user" name="nama_user" required
                        value="{{ old('nama_user') }}"
                        placeholder="Contoh: Rahmat Hidayat, S.Kom"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-sm">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Alamat Email (Login) <span class="text-rose-500">*</span>
                </label>
                <div class="mt-2">
                    <input type="email" id="email" name="email" required
                        value="{{ old('email') }}"
                        placeholder="nama@kapuas.go.id"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-sm">
                </div>
            </div>

            <!-- Peran (Role) -->
            <div>
                <label for="role" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Peran Pengguna (Role) <span class="text-rose-500">*</span>
                </label>
                <div class="mt-2">
                    <select id="role" name="role" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-sm cursor-pointer">
                        <option value="">-- Pilih Peran Pengguna --</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Hak Penuh & Kelola User)</option>
                        <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>Operator (Input & Kelola Arsip)</option>
                        <option value="viewer" {{ old('role') === 'viewer' ? 'selected' : '' }}>Viewer (Hanya Lihat & Unduh Arsip)</option>
                    </select>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Operator dapat menginput dan mengedit arsip, namun tidak dapat mengelola akun pengguna.</p>
            </div>

            <!-- Status Pengguna -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Status Pengguna <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="radio" name="status" value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'checked' : '' }}
                            class="w-4 h-4 text-emerald-600 border-slate-300 focus:ring-emerald-500 cursor-pointer">
                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="radio" name="status" value="nonaktif" {{ old('status') === 'nonaktif' ? 'checked' : '' }}
                            class="w-4 h-4 text-slate-500 border-slate-300 focus:ring-slate-400 cursor-pointer">
                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            Nonaktif
                        </span>
                    </label>
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5">Pengguna nonaktif tidak dapat masuk ke sistem meskipun memiliki akun terdaftar.</p>
            </div>

            <!-- Kata Sandi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input type="password" id="password" name="password" required
                            placeholder="Minimal 8 karakter"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-sm">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Konfirmasi Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            placeholder="Ulangi kata sandi"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-sm">
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-600/25 rounded-xl transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    <span>Simpan Pengguna</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
