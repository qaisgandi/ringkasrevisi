<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Ringkasan & Manajemen Publikasi Statistik BPS')</title>
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar for dark sidebar nav */
        #sidebar nav::-webkit-scrollbar {
            width: 4px;
        }
        #sidebar nav::-webkit-scrollbar-track {
            background: transparent;
        }
        #sidebar nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 99px;
        }
        #sidebar nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-bps-bg text-slate-800 antialiased min-h-screen flex">

    <!-- MOBILE SIDEBAR OVERLAY -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/40 z-40 hidden md:hidden transition-opacity duration-300"></div>

    <!-- SIDEBAR LEFT -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#0b1329] border-r border-white/[0.04] flex flex-col z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
        
        <!-- Sidebar Brand / Logo BPS -->
        <div class="h-20 px-6 border-b border-white/[0.03] flex items-center gap-3 shrink-0">
            <div class="w-12 h-8 shrink-0 flex items-center justify-center">
                <img src="{{ asset('images/logo-bps.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
            </div>
            <div class="flex flex-col">
                <span class="heading-font text-xs font-black text-white tracking-tight leading-tight mt-1 uppercase">RINGKAS</span>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-none">SISTEM RINGKASAN PUBLIKASI STATISTIK BPS</span>
            </div>
            <!-- Close button for mobile sidebar -->
            <button id="sidebar-close" class="md:hidden ml-auto text-slate-400 hover:text-white focus:outline-none" title="Tutup Menu">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        @php
            $isDashboardActive = ($activeMenu ?? '') === 'dashboard';
            $isDaftarActive = ($activeMenu ?? '') === 'publications' && !request()->routeIs('publications.create') && request('status') !== 'Selesai' && !request()->has('category');
            $isUploadActive = request()->routeIs('publications.create');
            $isKategoriActive = request()->has('category');
            $isRingkasanActive = ($activeMenu ?? '') === 'summaries' || request()->routeIs('admin.summaries') || (request()->routeIs('publications.index') && request('status') === 'Selesai');
            $isPencarianActive = false;
            $isStatistikActive = request()->routeIs('admin.dashboard') && ($activeMenu ?? '') === 'dashboard';
            $isAktivitasActive = ($activeMenu ?? '') === 'activities' || request()->routeIs('activities.index');
            $isPengaturanActive = ($activeMenu ?? '') === 'account_settings' || request()->routeIs('account.settings');
            $isManajemenActive = ($activeMenu ?? '') === 'user_management' || request()->routeIs('users.*');

            // Fetch distinct categories and count their publications
            $sidebarCategories = \App\Models\Publication::select('category', \DB::raw('count(*) as count'))
                ->groupBy('category')
                ->orderBy('category')
                ->get();
        @endphp

        <!-- Sidebar Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="{{ $isDashboardActive ? 'bg-[#1e5eff] text-white shadow-md shadow-blue-500/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.04] font-medium' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                </svg>
                <span class="text-sm">Dashboard</span>
            </a>

            <!-- PUBLIKASI SECTION -->
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-4 pt-5 pb-2 select-none">Publikasi</div>

            <a href="{{ route('publications.index') }}" class="{{ $isDaftarActive ? 'bg-[#1e5eff] text-white shadow-md shadow-blue-500/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.04] font-medium' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="text-sm">Daftar Publikasi</span>
            </a>

            <a href="{{ route('publications.create') }}" class="{{ $isUploadActive ? 'bg-[#1e5eff] text-white shadow-md shadow-blue-500/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.04] font-medium' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span class="text-sm">Upload Publikasi</span>
            </a>

            <!-- Collapsible Kategori Menu -->
            <details class="group select-none" {{ request()->has('category') ? 'open' : '' }}>
                <summary class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl {{ $isKategoriActive ? 'text-white bg-white/[0.04]' : 'text-slate-400' }} hover:text-white hover:bg-white/[0.04] font-medium cursor-pointer list-none transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="text-sm">Kategori</span>
                    </div>
                    <!-- Chevron icon with rotation when details is open -->
                    <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 group-open:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pl-8 pr-2 py-1.5 space-y-1 transition-all duration-200">
                    @if ($sidebarCategories->isEmpty())
                        <span class="block text-[11px] text-slate-500 py-1 pl-2 italic">Belum ada kategori</span>
                    @else
                        @foreach ($sidebarCategories as $cat)
                            @php
                                $isThisCategoryActive = request('category') === $cat->category;
                            @endphp
                            <a 
                                href="{{ route('publications.index', ['category' => $cat->category]) }}" 
                                class="flex items-center justify-between py-1.5 pl-3 pr-2 text-xs transition-all duration-150 {{ $isThisCategoryActive ? 'font-bold text-white bg-blue-600/30 border-l-2 border-[#1e5eff] rounded-r-lg' : 'text-slate-400 hover:text-white hover:pl-4 border-l border-white/10' }}"
                            >
                                <span class="truncate pr-1" title="{{ $cat->category }}">{{ $cat->category }}</span>
                                <span class="text-[10px] text-slate-500 shrink-0 font-bold">({{ $cat->count }})</span>
                            </a>
                        @endforeach
                    @endif
                </div>
            </details>

            <!-- RINGKASAN SECTION -->
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-4 pt-5 pb-2 select-none">Ringkasan</div>

            <a href="{{ route('admin.summaries') }}" class="{{ $isRingkasanActive ? 'bg-[#1e5eff] text-white shadow-md shadow-blue-500/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.04] font-medium' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="text-sm">Semua Ringkasan</span>
            </a>

            <!-- LAPORAN SECTION -->
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-4 pt-5 pb-2 select-none">Laporan</div>

            <a href="{{ route('admin.dashboard') }}" class="{{ $isStatistikActive ? 'bg-[#1e5eff] text-white shadow-md shadow-blue-500/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.04] font-medium' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21h12a2 2 0 002-2V7a2 2 0 00-2-2H8a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm">Statistik Ringkasan</span>
            </a>

            <a href="{{ route('activities.index') }}" class="{{ $isAktivitasActive ? 'bg-[#1e5eff] text-white shadow-md shadow-blue-500/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.04] font-medium' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Aktivitas Sistem</span>
            </a>

            <!-- PENGATURAN SECTION -->
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-4 pt-5 pb-2 select-none">Pengaturan</div>
 
            <a href="{{ route('account.settings') }}" class="{{ $isPengaturanActive ? 'bg-[#1e5eff] text-white shadow-md shadow-blue-500/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.04] font-medium' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-sm">Pengaturan Akun</span>
            </a>
 
            <a href="{{ route('users.index') }}" class="{{ $isManajemenActive ? 'bg-[#1e5eff] text-white shadow-md shadow-blue-500/10 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/[0.04] font-medium' }} flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-sm">Manajemen User</span>
            </a>
        </nav>

        <!-- Sidebar Profile (Bottom Section) -->
        <div class="p-4 border-t border-white/[0.05] shrink-0 relative">
            <!-- Profile Card -->
            <div id="profile-card" class="flex items-center justify-between gap-3 p-2.5 rounded-xl bg-[#111e36]/60 border border-white/[0.04] cursor-pointer hover:bg-[#111e36]/90 transition-all duration-200">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-[#1e5eff] text-white flex items-center justify-center shadow-sm select-none shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-bold text-white truncate" title="{{ session('admin_name', 'Admin BPS') }}">{{ session('admin_name', 'Admin BPS') }}</h4>
                        <p class="text-[10px] text-slate-400 font-medium truncate leading-tight mt-0.5" title="{{ session('admin_email', 'admin@bps.go.id') }}">{{ session('admin_email', 'admin@bps.go.id') }}</p>
                    </div>
                </div>
                <svg class="w-3.5 h-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            <!-- Profile Dropdown (Popup) -->
            <div id="profile-dropdown" class="hidden absolute bottom-16 left-4 right-4 bg-[#111e36] border border-white/[0.06] rounded-xl shadow-2xl py-1.5 z-50">
                <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="w-full text-left px-3.5 py-2 text-xs text-rose-400 hover:bg-rose-500/10 transition-colors flex items-center gap-2 font-semibold">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- CONTENT WRAPPER -->
    <div class="flex-1 md:pl-64 flex flex-col min-w-0">
        
        <!-- NAVBAR TOP -->
        <header class="h-20 bg-bps-navy border-b border-white/5 flex items-center justify-between px-6 md:px-8 z-30 sticky top-0 shrink-0 shadow-md shadow-black/5">
            <div class="flex items-center gap-4">
                <!-- Hamburger Menu for Mobile -->
                <button id="sidebar-toggle" class="md:hidden text-slate-400 hover:text-white p-2 rounded-lg hover:bg-white/5 focus:outline-none" title="Buka Menu">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="hidden sm:block">
                    <h2 class="text-sm font-bold text-white leading-tight">Selamat Datang, Admin BPS</h2>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Kelola publikasi statistik dengan lebih mudah.</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <!-- Search Box -->
                <form action="{{ route('publications.index') }}" method="GET" class="relative hidden lg:block w-64 m-0">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari judul / kategori..." 
                        class="w-full pl-9 pr-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs text-white placeholder-slate-400 focus:outline-none focus:bg-white/10 focus:border-bps-primary focus:ring-2 focus:ring-blue-500/30 transition-all duration-200"
                    >
                </form>

                @php
                    $notifications = \App\Models\Notification::latest()->take(5)->get();
                    $unreadCount = \App\Models\Notification::where('is_read', false)->count();
                @endphp
                <!-- Notification Bell & Dropdown Wrapper -->
                <div class="relative">
                    <button id="notification-bell-btn" class="relative p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition-colors focus:outline-none" title="Notifikasi">
                        @if ($unreadCount > 0)
                            <span class="absolute top-0.5 right-0.5 w-4 h-4 bg-rose-500 border-2 border-bps-navy rounded-full text-[9px] font-bold text-white flex items-center justify-center select-none animate-pulse">
                                {{ $unreadCount }}
                            </span>
                        @endif
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>

                    <!-- Dropdown Panel (hidden by default) -->
                    <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100/80 py-2 z-50 transform origin-top-right transition-all duration-200">
                        <div class="px-4 py-2.5 border-b border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-xs text-slate-800">Notifikasi</span>
                            @if ($unreadCount > 0)
                                <form action="{{ route('notifications.read-all') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-bps-primary hover:underline font-bold">Tandai semua dibaca</button>
                                </form>
                            @endif
                        </div>

                        <!-- Notification List -->
                        <div class="max-h-64 overflow-y-auto divide-y divide-slate-50">
                            @if ($notifications->isEmpty())
                                <div class="px-4 py-6 text-center text-xs text-slate-400 font-medium">
                                    Tidak ada notifikasi baru.
                                </div>
                            @else
                                @foreach ($notifications as $notification)
                                    <div class="px-4 py-3 hover:bg-slate-50/70 transition-colors flex items-start gap-3 {{ !$notification->is_read ? 'bg-blue-50/20' : '' }}">
                                        <!-- Type Icon -->
                                        @if ($notification->type === 'upload')
                                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0 mt-0.5">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                                </svg>
                                            </div>
                                        @elseif ($notification->type === 'delete')
                                            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center shrink-0 mt-0.5">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </div>
                                        @else
                                            <!-- Summary Icon -->
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-bps-primary flex items-center justify-center shrink-0 mt-0.5">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                </svg>
                                            </div>
                                        @endif

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-1">
                                                <span class="font-bold text-[11px] text-slate-800 truncate">{{ $notification->title }}</span>
                                                <span class="text-[9px] text-slate-400 shrink-0 font-medium">{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-[10px] text-slate-500 font-medium mt-0.5 leading-snug break-words">
                                                {{ $notification->message }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="h-6 w-px bg-white/10"></div>

                <!-- Admin Identity -->
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-xs select-none">
                        AB
                    </div>
                    <div class="hidden md:block text-left">
                        <h4 class="text-xs font-bold text-white">Admin BPS</h4>
                        <span class="text-[9px] text-slate-400 font-semibold block leading-none mt-0.5">Administrator</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 p-6 md:p-8 overflow-y-auto">
            @yield('content')
        </main>

        <!-- FOOTER -->
        <footer class="h-16 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between px-6 md:px-8 text-xs text-slate-400 bg-white shrink-0 gap-2 py-3 md:py-0">
            <div class="flex items-center gap-2">
                <div class="w-6 h-5 shrink-0 flex items-center justify-center bg-slate-50 p-0.5 rounded border border-slate-100">
                    <img src="{{ asset('images/logo-bps.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
                </div>
                <p>&copy; {{ date('Y') }} Badan Pusat Statistik. Hak Cipta Dilindungi.</p>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 font-semibold">
                <button type="button" onclick="openFooterModal('about-modal')" class="hover:text-bps-primary transition-colors focus:outline-none cursor-pointer">Tentang</button>
                <button type="button" onclick="openFooterModal('guide-modal')" class="hover:text-bps-primary transition-colors focus:outline-none cursor-pointer">Panduan</button>
                <button type="button" onclick="openFooterModal('contact-modal')" class="hover:text-bps-primary transition-colors focus:outline-none cursor-pointer">Kontak</button>
                <a href="https://www.bps.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-bps-primary transition-colors flex items-center gap-1">
                    <i data-lucide="globe" class="w-3.5 h-3.5"></i> BPS
                </a>
            </div>
        </footer>

    </div>



    @include('layouts.footer_modals')
</body>
</html>
