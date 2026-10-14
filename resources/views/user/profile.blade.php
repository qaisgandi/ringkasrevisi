@extends('layouts.user')

@section('title', 'Profil Saya')

@section('content')
    <div class="max-w-4xl w-full mx-auto px-4 py-12 space-y-8">
        <div>
            <h2 class="heading-font text-2xl md:text-3xl font-black text-bps-navy tracking-tight">Pengaturan Profil</h2>
            <p class="text-xs md:text-sm text-slate-400 font-semibold mt-1">Ubah data profil pribadi Anda atau perbarui kata sandi akun Anda di sini.</p>
        </div>

        <!-- Alert Notifications -->
        @if (session('success'))
            <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl text-sm" role="alert">
                <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        <!-- Validation Error Message -->
        @if ($errors->any())
            <div class="flex flex-col gap-1 bg-red-50 border border-red-200 text-red-700 px-4 py-3.5 rounded-2xl text-sm">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Box: Edit Profile details -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 md:p-8 shadow-sm space-y-6">
                <h3 class="heading-font text-lg font-extrabold text-bps-navy flex items-center gap-2">
                    <svg class="w-5 h-5 text-bps-lightBlue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Data Akun
                </h3>
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:bg-white focus:border-bps-lightBlue focus:ring-4 focus:ring-blue-100/50 transition-all">
                    </div>
                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:bg-white focus:border-bps-lightBlue focus:ring-4 focus:ring-blue-100/50 transition-all">
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Alamat Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:bg-white focus:border-bps-lightBlue focus:ring-4 focus:ring-blue-100/50 transition-all">
                    </div>
                    <button type="submit" class="w-full py-3 bg-bps-lightBlue hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-md shadow-blue-500/10 hover:shadow-lg transition-all">
                        Simpan Profil
                    </button>
                </form>
            </div>

            <!-- Right Box: Change Password -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 md:p-8 shadow-sm space-y-6">
                <h3 class="heading-font text-lg font-extrabold text-bps-navy flex items-center gap-2">
                    <svg class="w-5 h-5 text-bps-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Ubah Password
                </h3>
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <!-- Hidden elements so we can reuse updateProfile logic safely -->
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="username" value="{{ $user->username }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">

                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Password Baru</label>
                        <input type="password" name="password" id="password" required placeholder="Minimal 8 karakter" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:bg-white focus:border-bps-lightBlue focus:ring-4 focus:ring-blue-100/50 transition-all">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi password baru" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:bg-white focus:border-bps-lightBlue focus:ring-4 focus:ring-blue-100/50 transition-all">
                    </div>
                    <button type="submit" class="w-full py-3 bg-bps-orange hover:bg-amber-600 text-white rounded-xl font-bold text-sm shadow-md shadow-amber-500/10 hover:shadow-lg transition-all">
                        Perbarui Password
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
