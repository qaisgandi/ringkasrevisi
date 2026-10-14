@extends('layouts.admin')

@section('title', 'Manajemen User - Sistem Ringkasan')

@section('content')
<div class="space-y-8">
    <!-- Header Page & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex flex-col gap-1">
            <h1 class="heading-font text-2xl md:text-3xl font-extrabold text-[#0f2a4a] tracking-tight">Manajemen User</h1>
            <p class="text-sm text-slate-500 font-medium">Kelola daftar akun administrator yang memiliki akses ke sistem</p>
        </div>
        <div>
            <a 
                href="{{ route('users.create') }}" 
                class="px-5 py-3 bg-[#1e5eff] hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-md shadow-blue-500/10 hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah User Baru
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session('success'))
        <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl text-sm" role="alert">
            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <span class="font-semibold">Berhasil:</span> {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-2xl text-sm" role="alert">
            <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <span class="font-semibold">Gagal:</span> {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Search Box Card -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100">
        <form action="{{ route('users.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative w-full sm:flex-1">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 pointer-events-none">
                    <svg class="w-4.5 h-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari user berdasarkan nama, username, atau email..." 
                    class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200/60 focus:border-[#1e5eff] rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-100/50 transition-all text-sm font-medium"
                >
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button 
                    type="submit" 
                    class="flex-1 sm:flex-initial px-6 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold text-sm transition-all cursor-pointer"
                >
                    Cari
                </button>
                @if (request()->filled('search'))
                    <a 
                        href="{{ route('users.index') }}" 
                        class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-sm transition-all flex items-center justify-center cursor-pointer"
                        title="Reset Pencarian"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">User</th>
                        <th class="py-4 px-6">Username</th>
                        <th class="py-4 px-6">Email</th>
                        <th class="py-4 px-6">Tanggal Dibuat</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                    @forelse ($users as $u)
                        @php
                            // Generate initials for avatar
                            $words = explode(' ', $u->name);
                            $initials = '';
                            foreach (array_slice($words, 0, 2) as $w) {
                                $initials .= strtoupper(substr($w, 0, 1));
                            }
                            // Assign a background color based on user ID
                            $colors = ['bg-blue-50 text-blue-600', 'bg-indigo-50 text-indigo-600', 'bg-purple-50 text-purple-600', 'bg-pink-50 text-pink-600', 'bg-emerald-50 text-emerald-600', 'bg-sky-50 text-sky-600'];
                            $colorIndex = $u->id % count($colors);
                            $avatarColor = $colors[$colorIndex];
                            $isCurrentUser = $u->id === session('admin_user_id');
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full {{ $avatarColor }} flex items-center justify-center font-extrabold text-sm select-none shrink-0">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 flex items-center gap-2">
                                        <span class="truncate">{{ $u->name }}</span>
                                        @if ($isCurrentUser)
                                            <span class="px-2 py-0.5 bg-blue-50 text-[#1e5eff] rounded-md text-[9px] font-extrabold uppercase tracking-wide shrink-0">Anda</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-slate-400 font-medium leading-none block mt-1">{{ $u->role === 'admin' ? 'Administrator' : 'User Umum' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-slate-500 font-semibold">
                                &#64;{{ $u->username }}
                            </td>
                            <td class="py-4 px-6 font-semibold">
                                {{ $u->email }}
                            </td>
                            <td class="py-4 px-6 text-slate-400 text-xs">
                                {{ $u->created_at ? $u->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Edit Link -->
                                    <a 
                                        href="{{ route('users.edit', $u->id) }}" 
                                        class="p-2 text-slate-400 hover:text-[#1e5eff] hover:bg-blue-50 rounded-xl transition-all"
                                        title="Ubah Data User"
                                    >
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </a>

                                    <!-- Delete Button -->
                                    @if ($isCurrentUser)
                                        <button 
                                            type="button" 
                                            class="p-2 text-slate-300 cursor-not-allowed" 
                                            title="Anda tidak dapat menghapus akun Anda sendiri"
                                            disabled
                                        >
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    @else
                                        <form 
                                            action="{{ route('users.destroy', $u->id) }}" 
                                            method="POST" 
                                            class="m-0 inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus user \'{{ $u->username }}\' dari sistem? Tindakan ini tidak dapat dibatalkan.')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all cursor-pointer"
                                                title="Hapus User"
                                            >
                                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <svg class="w-10 h-10 text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A9.642 9.642 0 0012 20.25a9.642 9.642 0 00-3-1.013V19.12c0-1.112-.285-2.16-.786-3.07M7.5 14.25a3 3 0 00-3-3m0 0a3 3 0 00-3 3m3-3v8.25m0-12a3 3 0 100-6 3 3 0 000 6zm6.5 12V20.25m0 0a3 3 0 11-6 0 3 3 0 016 0zm6.5-1.5V19.13a3 3 0 01-6 0 3 3 0 016 0zm-6-1.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Tidak ada data user ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
