@extends('layouts.admin')

@section('title', 'Tambah User - Sistem Ringkasan')

@section('content')
<div class="space-y-8">
    <!-- Header Page & Back Button -->
    <div class="flex items-center gap-4">
        <a 
            href="{{ route('users.index') }}" 
            class="p-2.5 bg-white hover:bg-slate-50 text-slate-500 hover:text-slate-800 border border-slate-200/60 rounded-xl transition-all shadow-sm flex items-center justify-center shrink-0 cursor-pointer"
            title="Kembali ke Daftar User"
        >
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </a>
        <div class="flex flex-col gap-0.5">
            <h1 class="heading-font text-2xl font-extrabold text-[#0f2a4a] tracking-tight">Tambah User Baru</h1>
            <p class="text-xs text-slate-500 font-medium">Buat akun administrator baru untuk hak akses sistem</p>
        </div>
    </div>

    <!-- Alert Validation Errors -->
    @if ($errors->any())
        <div class="flex flex-col gap-1.5 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-2xl text-sm">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-5 h-5 text-rose-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-bold">Gagal Menyimpan Data:</span>
            </div>
            @foreach ($errors->all() as $error)
                <div class="flex items-center gap-2 pl-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 md:p-8">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nama Lengkap -->
            <div class="space-y-1.5">
                <label for="name" class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Nama Lengkap</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name') }}"
                    required
                    class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#1e5eff] focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium"
                    placeholder="Masukkan nama lengkap"
                >
            </div>

            <!-- Role / Peran -->
            <div class="space-y-1.5">
                <label for="role" class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Role / Hak Akses</label>
                <select 
                    name="role" 
                    id="role" 
                    required
                    class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-[#1e5eff] focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium"
                >
                    <option value="user" {{ old('role', 'admin') === 'user' ? 'selected' : '' }}>User Umum (Akses Portal Ringkasan)</option>
                    <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>Administrator (Akses Dashboard Admin)</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Username -->
                <div class="space-y-1.5">
                    <label for="username" class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm font-semibold select-none">@</span>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            value="{{ old('username') }}"
                            required
                            class="w-full pl-8 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#1e5eff] focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium"
                            placeholder="username"
                        >
                    </div>
                </div>

                <!-- Email -->
                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Alamat Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}"
                        required
                        class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#1e5eff] focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium"
                        placeholder="contoh: user@bps.go.id"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-slate-100">
                <!-- Password -->
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Password</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            required
                            class="w-full pl-4 pr-11 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-[#1e5eff] focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium"
                            placeholder="Min. 6 karakter"
                        >
                        <button 
                            type="button" 
                            class="toggle-password-btn absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none"
                            data-target="password"
                            title="Tampilkan Password"
                        >
                            <svg class="w-5 h-5 eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg class="w-5 h-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Konfirmasi Password</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            required
                            class="w-full pl-4 pr-11 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-[#1e5eff] focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium"
                            placeholder="Ulangi password"
                        >
                        <button 
                            type="button" 
                            class="toggle-password-btn absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none"
                            data-target="password_confirmation"
                            title="Tampilkan Password"
                        >
                            <svg class="w-5 h-5 eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg class="w-5 h-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <a 
                    href="{{ route('users.index') }}" 
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg font-bold text-xs transition-all cursor-pointer"
                >
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 bg-[#1e5eff] hover:bg-blue-700 text-white rounded-lg font-bold text-xs shadow-md shadow-blue-500/10 hover:shadow-lg transition-all flex items-center gap-1.5 cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-password-btn');
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                if (!passwordInput) return;

                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                const eyeOpen = this.querySelector('.eye-open');
                const eyeClosed = this.querySelector('.eye-closed');

                if (type === 'password') {
                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                    this.setAttribute('title', 'Tampilkan Password');
                } else {
                    eyeOpen.classList.add('hidden');
                    eyeClosed.classList.remove('hidden');
                    this.setAttribute('title', 'Sembunyikan Password');
                }
            });
        });
    });
</script>
@endsection
