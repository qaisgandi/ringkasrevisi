@extends('layouts.admin')

@section('title', 'Pengaturan Akun - Sistem Ringkasan')

@section('content')
<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col gap-2">
        <h1 class="heading-font text-2xl md:text-3xl font-extrabold text-[#0f2a4a] tracking-tight">Pengaturan Akun</h1>
        <p class="text-sm text-slate-500 font-medium">Perbarui profil administrator dan kata sandi Anda</p>
    </div>

    <!-- Alert Notifications -->
    @if (session('success'))
        <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl text-sm max-w-4xl" role="alert">
            <!-- Check Circle Icon -->
            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <span class="font-semibold">Berhasil:</span> {{ session('success') }}
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex flex-col gap-1.5 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-2xl text-sm max-w-4xl">
            <div class="flex items-center gap-2 mb-1">
                <!-- X Circle Icon -->
                <svg class="w-5 h-5 text-rose-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-bold">Terjadi Kesalahan:</span>
            </div>
            @foreach ($errors->all() as $error)
                <div class="flex items-center gap-2 pl-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-6xl">
        <!-- Left Side: Profile Information Card -->
        <div class="lg:col-span-2 bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 md:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="heading-font font-bold text-lg text-slate-800">Informasi Profil</h3>
                    <p class="text-xs text-slate-400 font-medium">Ubah nama lengkap, username, dan alamat email Anda</p>
                </div>
            </div>

            <form action="{{ route('account.settings.update') }}" method="POST" class="space-y-5">
                @csrf
                
                <!-- Nama Lengkap -->
                <div class="space-y-1.5">
                    <label for="name" class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Nama Lengkap</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#1e5eff] focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium"
                        placeholder="Masukkan nama lengkap"
                    >
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Username -->
                    <div class="space-y-1.5">
                        <label for="username" class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm font-semibold select-none">@</span>
                            <input 
                                type="text" 
                                name="username" 
                                id="username" 
                                value="{{ old('username', $user->username) }}"
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
                            value="{{ old('email', $user->email) }}"
                            required
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#1e5eff] focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium"
                            placeholder="admin@bps.go.id"
                        >
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-[#1e5eff] hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-md shadow-blue-500/10 hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer"
                    >
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Side: Change Password Card -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 md:p-8 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <div>
                    <h3 class="heading-font font-bold text-base text-slate-800">Ubah Password</h3>
                    <p class="text-xs text-slate-400 font-medium">Ganti kata sandi secara berkala</p>
                </div>
            </div>

            <form action="{{ route('account.settings.password') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Password Lama -->
                <div class="space-y-1.5">
                    <label for="old_password" class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Password Lama</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="old_password" 
                            id="old_password" 
                            required
                            class="w-full pl-4 pr-11 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-[#1e5eff] focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium"
                            placeholder="••••••••"
                        >
                        <button 
                            type="button" 
                            class="toggle-password-btn absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none"
                            data-target="old_password"
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

                <!-- Password Baru -->
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Password Baru</label>
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

                <!-- Konfirmasi Password Baru -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Konfirmasi Password</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            required
                            class="w-full pl-4 pr-11 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-[#1e5eff] focus:ring-4 focus:ring-blue-100 transition-all text-sm font-medium"
                            placeholder="••••••••"
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

                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full px-5 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-sm shadow-md shadow-amber-500/10 hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                        </svg>
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>
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
