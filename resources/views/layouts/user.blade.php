<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal RINGKAS') - Badan Pusat Statistik</title>
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fcfdfe;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        @yield('styles')
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[#fcfdfe] text-slate-800 antialiased">

    <!-- HEADER NAVIGATION -->
    <header class="h-20 bg-bps-navy border-b border-white/5 flex items-center justify-between px-4 md:px-12 sticky top-0 z-50 shrink-0 shadow-md shadow-black/5">
        <!-- Logo Brand Left -->
        <div class="flex items-center gap-3">
            <div class="w-12 h-8 shrink-0 flex items-center justify-center">
                <img src="{{ asset('images/logo-bps.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
            </div>
            <div>
                <h1 class="heading-font text-base font-black text-white leading-none tracking-tight">RINGKAS</h1>
                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block mt-0.5">SISTEM RINGKASAN PUBLIKASI STATISTIK BPS</span>
            </div>
        </div>

        <!-- Center Menu Nav -->
        <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold">
            @php
                $isBerandaActive = request()->routeIs('home');
                $isProfilActive = request()->routeIs('profile');
            @endphp
            <a href="{{ route('home') }}" class="py-2 px-1 transition-colors {{ $isBerandaActive ? 'text-white border-b-2 border-white' : 'text-slate-400 hover:text-white' }}">Beranda</a>
            <a href="{{ route('profile') }}" class="py-2 px-1 transition-colors {{ $isProfilActive ? 'text-white border-b-2 border-white' : 'text-slate-400 hover:text-white' }}">Profil Saya</a>
        </nav>

        <!-- Right Side Controls -->
        <div class="flex items-center gap-4">
            <!-- Profile & Logout Dropdown -->
            <div class="flex items-center gap-3">
                <a href="{{ route('profile') }}" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white border border-white/10 flex items-center justify-center font-extrabold text-xs select-none uppercase shadow-sm transition-all" title="Profil Saya ({{ Auth::user()->name ?? Auth::user()->username }})">
                    {{ substr(Auth::user()->username ?? 'U', 0, 2) }}
                </a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-white/5 rounded-xl transition-all cursor-pointer" title="Keluar">
                        <i data-lucide="log-out" class="w-4.5 h-4.5"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- FOOTER -->
    @include('layouts.footer')

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
    @yield('scripts')
</body>
</html>
