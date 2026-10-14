@extends('layouts.user')

@section('title', 'Beranda')

@section('styles')
    /* Float Animation for Hero Image */
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(-1deg); }
    }
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
@endsection

@section('content')

    <!-- HERO BANNER SECTION -->
    <section class="bg-gradient-to-r from-blue-50/50 via-indigo-50/20 to-white py-12 md:py-16 px-4 md:px-12 border-b border-slate-50 relative overflow-hidden">
        <!-- Decorative blur background circle -->
        <div class="absolute top-1/2 left-1/4 w-80 h-80 rounded-full bg-blue-100/20 blur-3xl pointer-events-none -translate-y-1/2"></div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <!-- Left Content: Headings & Search -->
            <div class="lg:col-span-7 space-y-6 text-left">
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 border border-blue-100/80 rounded-full text-[11px] font-bold text-bps-lightBlue uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-bps-lightBlue animate-pulse"></span>
                        Portal Ringkasan Publikasi Statistik BPS
                    </div>
                    <h2 class="heading-font text-3xl md:text-4.5xl font-extrabold text-bps-navy tracking-tight leading-tight">
                        Akses Publikasi Statistik BPS <br class="hidden md:inline">
                        <span class="text-bps-lightBlue">Lebih Cepat dengan Ringkasan Cerdas</span>
                    </h2>
                    <p class="text-sm md:text-base text-slate-500 font-medium max-w-2xl leading-relaxed">
                        Temukan intisari dan indikator penting dari publikasi statistik BPS dalam ringkasan otomatis terstruktur yang mudah dipahami.
                    </p>
                </div>

                <!-- Big Search Box -->
                <form action="{{ route('home') }}" method="GET" class="flex items-center bg-white border border-slate-200/80 rounded-2xl p-2 max-w-xl shadow-md shadow-slate-100/40 focus-within:border-bps-lightBlue focus-within:ring-4 focus-within:ring-blue-100/30 transition-all duration-300">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="relative flex-1 flex items-center pl-3">
                        <i data-lucide="search" class="w-5 h-5 text-slate-400 shrink-0"></i>
                        <input 
                            type="text" 
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari publikasi atau kata kunci (contoh: kemiskinan, IPM, inflasi...)"
                            class="w-full bg-transparent pl-3 pr-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none font-medium"
                        >
                    </div>
                    <button type="submit" class="px-7 py-3 bg-bps-lightBlue hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-md shadow-blue-500/10 hover:shadow-lg transition-all cursor-pointer shrink-0">
                        Cari
                    </button>
                </form>

                <!-- Popular Tags -->
                <div class="flex flex-wrap items-center gap-2 pt-2 text-xs font-semibold text-slate-400">
                    <span>Populer sekarang:</span>
                    <a href="{{ route('home', ['category' => 'Statistik Sosial']) }}" class="px-3 py-1 bg-blue-50/50 hover:bg-blue-100/60 text-bps-lightBlue rounded-full transition-colors cursor-pointer">Statistik Sosial</a>
                    <a href="{{ route('home', ['category' => 'Statistik Ekonomi']) }}" class="px-3 py-1 bg-blue-50/50 hover:bg-blue-100/60 text-bps-lightBlue rounded-full transition-colors cursor-pointer">Statistik Ekonomi</a>
                    <a href="{{ route('home', ['search' => 'Kemiskinan']) }}" class="px-3 py-1 bg-blue-50/50 hover:bg-blue-100/60 text-bps-lightBlue rounded-full transition-colors cursor-pointer">Kemiskinan</a>
                    <a href="{{ route('home', ['search' => 'IPM']) }}" class="px-3 py-1 bg-blue-50/50 hover:bg-blue-100/60 text-bps-lightBlue rounded-full transition-colors cursor-pointer">IPM</a>
                    <a href="{{ route('home', ['search' => 'Inflasi']) }}" class="px-3 py-1 bg-blue-50/50 hover:bg-blue-100/60 text-bps-lightBlue rounded-full transition-colors cursor-pointer">Inflasi</a>
                    <a href="{{ route('home', ['category' => 'Sensus & Survei']) }}" class="px-3 py-1 bg-blue-50/50 hover:bg-blue-100/60 text-bps-lightBlue rounded-full transition-colors cursor-pointer">Sensus & Survei</a>
                </div>
            </div>

            <!-- Right Content: Premium CSS Graphic Mockup -->
            <div class="lg:col-span-5 hidden lg:flex items-center justify-center relative">
                <!-- Decorative Backboard Chart Card -->
                <div class="absolute -right-4 -top-8 w-60 h-44 bg-white/95 rounded-2xl shadow-xl shadow-slate-100/50 border border-slate-100/80 p-4 space-y-3 z-10 transition-all hover:scale-105 duration-300">
                    <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">STATISTIK PERKEMBANGAN</span>
                        <i data-lucide="trending-up" class="w-4 h-4 text-emerald-500"></i>
                    </div>
                    <div class="h-24 flex items-end gap-2.5 justify-center pt-2">
                        <div class="w-6 bg-slate-100 rounded-t-lg h-[40%]"></div>
                        <div class="w-6 bg-slate-100 rounded-t-lg h-[65%]"></div>
                        <div class="w-6 bg-bps-lightBlue rounded-t-lg h-[85%] transition-all hover:bg-blue-700"></div>
                        <div class="w-6 bg-slate-100 rounded-t-lg h-[50%]"></div>
                    </div>
                </div>

                <!-- Floating Main Book Mockup -->
                <div class="relative w-52 h-72 bg-gradient-to-br from-bps-navy to-slate-900 text-white rounded-2xl shadow-2xl p-6 flex flex-col justify-between border-r-4 border-bps-blue/30 transform -rotate-6 animate-float z-20 hover:scale-105 transition-transform duration-300">
                    <!-- Subtle book spine line -->
                    <div class="absolute left-0 top-0 bottom-0 w-2.5 bg-white/5 border-r border-white/10 rounded-l-2xl"></div>

                    <!-- Top BPS Badge -->
                    <div class="flex items-center gap-1.5 pl-2">
                        <div class="w-1.5 h-1.5 bg-bps-orange rounded-full"></div>
                        <span class="text-[8px] font-black tracking-widest text-slate-300 uppercase">PUBLIKASI RESMI</span>
                    </div>

                    <!-- Middle Title -->
                    <div class="pl-2 space-y-2">
                        <h4 class="heading-font text-base font-black tracking-wide leading-tight">STATISTIK INDONESIA</h4>
                        <span class="text-[10px] font-extrabold text-bps-orange tracking-widest uppercase">TAHUN 2026</span>
                    </div>

                    <!-- Bottom Graphic -->
                    <div class="pl-2 pt-4 border-t border-white/10 flex items-center justify-between text-slate-400">
                        <span class="text-[8px] font-bold">BPS INDONESIA</span>
                        <i data-lucide="globe" class="w-4 h-4 text-bps-orange"></i>
                    </div>
                </div>

                <!-- Floating Secondary Book Cover -->
                <div class="absolute left-0 -bottom-6 w-40 h-56 bg-gradient-to-tr from-sky-600 to-bps-lightBlue text-white rounded-2xl shadow-xl p-5 flex flex-col justify-between border-r-4 border-blue-400/20 transform rotate-12 z-10 transition-all hover:scale-105 duration-300">
                    <!-- Spine -->
                    <div class="absolute left-0 top-0 bottom-0 w-2 bg-white/5 border-r border-white/10 rounded-l-2xl"></div>

                    <span class="text-[7px] font-black tracking-widest text-blue-100 uppercase pl-1">PROFIL DATA</span>
                    <div class="pl-1">
                        <h4 class="heading-font text-xs font-black tracking-wide leading-tight">PROFIL SOSIAL</h4>
                        <span class="text-[8px] font-extrabold text-blue-200 block mt-1">INDONESIA</span>
                    </div>
                    <div class="pl-1 flex items-center justify-between text-blue-100 border-t border-white/5 pt-2">
                        <span class="text-[7px] font-bold">EDISI 2026</span>
                        <i data-lucide="bar-chart-2" class="w-3.5 h-3.5 text-blue-200"></i>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- CONTENT WRAPPER -->
    <main class="flex-1 p-6 md:p-12 max-w-7xl mx-auto w-full space-y-16">
        
        <!-- SECTION 1: KATEGORI PUBLIKASI (8 Kategori Resmi Admin BPS) -->
        <section id="kategori-publikasi" class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="heading-font text-xl md:text-2xl font-black text-bps-navy tracking-tight">Kategori Publikasi</h3>
                    <p class="text-xs md:text-sm text-slate-400 font-medium mt-1">Saring publikasi berdasarkan 8 kategori data sektoral BPS</p>
                </div>
                @if(request('category'))
                    <a href="{{ route('home') }}" class="text-rose-500 hover:text-rose-700 font-bold text-xs md:text-sm flex items-center gap-1.5 transition-colors self-start sm:self-auto">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        <span>Hapus Filter Kategori</span>
                    </a>
                @else
                    <a href="#publikasi-terbaru" class="text-bps-lightBlue hover:text-blue-700 font-bold text-xs md:text-sm flex items-center gap-1 transition-colors self-start sm:self-auto">
                        <span>Lihat publikasi</span>
                        <i data-lucide="arrow-down" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>

            <!-- 8 Official Categories Grid -->
            @php
                $categoryConfig = [
                    'Publikasi Umum' => [
                        'icon' => 'book-open',
                        'bg' => 'bg-blue-50',
                        'text' => 'text-blue-600',
                        'border' => 'border-blue-100',
                        'hover' => 'hover:border-blue-300 hover:shadow-blue-500/5',
                        'badge' => 'bg-blue-100/60 text-blue-700',
                    ],
                    'Statistik Sosial' => [
                        'icon' => 'users',
                        'bg' => 'bg-amber-50',
                        'text' => 'text-amber-600',
                        'border' => 'border-amber-100',
                        'hover' => 'hover:border-amber-300 hover:shadow-amber-500/5',
                        'badge' => 'bg-amber-100/60 text-amber-700',
                    ],
                    'Statistik Ekonomi' => [
                        'icon' => 'trending-up',
                        'bg' => 'bg-emerald-50',
                        'text' => 'text-emerald-600',
                        'border' => 'border-emerald-100',
                        'hover' => 'hover:border-emerald-300 hover:shadow-emerald-500/5',
                        'badge' => 'bg-emerald-100/60 text-emerald-700',
                    ],
                    'Statistik Pertanian' => [
                        'icon' => 'sprout',
                        'bg' => 'bg-lime-50',
                        'text' => 'text-lime-700',
                        'border' => 'border-lime-100',
                        'hover' => 'hover:border-lime-300 hover:shadow-lime-500/5',
                        'badge' => 'bg-lime-100/60 text-lime-800',
                    ],
                    'Statistik Industri' => [
                        'icon' => 'factory',
                        'bg' => 'bg-purple-50',
                        'text' => 'text-purple-600',
                        'border' => 'border-purple-100',
                        'hover' => 'hover:border-purple-300 hover:shadow-purple-500/5',
                        'badge' => 'bg-purple-100/60 text-purple-700',
                    ],
                    'Statistik Distribusi' => [
                        'icon' => 'shopping-cart',
                        'bg' => 'bg-rose-50',
                        'text' => 'text-rose-600',
                        'border' => 'border-rose-100',
                        'hover' => 'hover:border-rose-300 hover:shadow-rose-500/5',
                        'badge' => 'bg-rose-100/60 text-rose-700',
                    ],
                    'Statistik Lingkungan' => [
                        'icon' => 'trees',
                        'bg' => 'bg-teal-50',
                        'text' => 'text-teal-600',
                        'border' => 'border-teal-100',
                        'hover' => 'hover:border-teal-300 hover:shadow-teal-500/5',
                        'badge' => 'bg-teal-100/60 text-teal-700',
                    ],
                    'Sensus & Survei' => [
                        'icon' => 'clipboard-list',
                        'bg' => 'bg-indigo-50',
                        'text' => 'text-indigo-600',
                        'border' => 'border-indigo-100',
                        'hover' => 'hover:border-indigo-300 hover:shadow-indigo-500/5',
                        'badge' => 'bg-indigo-100/60 text-indigo-700',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 md:gap-5">
                @foreach($officialCategories as $catName)
                    @php
                        $cfg = $categoryConfig[$catName] ?? [
                            'icon' => 'folder',
                            'bg' => 'bg-slate-50',
                            'text' => 'text-slate-600',
                            'border' => 'border-slate-100',
                            'hover' => 'hover:border-slate-300',
                            'badge' => 'bg-slate-100 text-slate-700',
                        ];
                        $count = $categoryCounts[$catName] ?? 0;
                        $isActive = request('category') === $catName;
                        // Clicking an active category clears the filter, otherwise applies it
                        $targetUrl = $isActive ? route('home') : route('home', array_merge(request()->except('page'), ['category' => $catName]));
                    @endphp

                    <a 
                        href="{{ $targetUrl }}" 
                        class="group relative bg-white rounded-2xl border p-4 md:p-5 flex items-center gap-3.5 transition-all duration-300 cursor-pointer shadow-sm hover:shadow-md hover:-translate-y-0.5 {{ $isActive ? 'border-bps-lightBlue ring-2 ring-blue-100 bg-blue-50/20' : $cfg['border'] . ' ' . $cfg['hover'] }}"
                    >
                        <!-- Category Icon -->
                        <div class="w-11 h-11 md:w-12 md:h-12 rounded-xl {{ $cfg['bg'] }} {{ $cfg['text'] }} flex items-center justify-center shrink-0 border {{ $cfg['border'] }} transition-transform duration-300 group-hover:scale-105">
                            <i data-lucide="{{ $cfg['icon'] }}" class="w-5 h-5 md:w-5.5 md:h-5.5"></i>
                        </div>

                        <!-- Text Details -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-1">
                                <h4 class="font-bold text-xs md:text-sm text-slate-800 truncate leading-snug group-hover:text-bps-lightBlue transition-colors">
                                    {{ $catName }}
                                </h4>
                                @if($isActive)
                                    <span class="w-2 h-2 rounded-full bg-bps-lightBlue shrink-0" title="Filter aktif"></span>
                                @endif
                            </div>
                            <span class="text-[10px] md:text-xs text-slate-400 font-medium block mt-0.5">
                                {{ $count }} Publikasi
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- SECTION 2: PUBLIKASI TERBARU -->
        <section id="publikasi-terbaru" class="space-y-6 scroll-mt-24">
            
            <!-- Section Header & Filter Status -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="heading-font text-xl md:text-2xl font-black text-bps-navy tracking-tight">Daftar Publikasi</h3>
                    <p class="text-xs md:text-sm text-slate-400 font-medium mt-1">
                        Daftar publikasi statistik resmi BPS beserta ringkasan cerdas
                    </p>
                </div>

                <!-- Active Filter Pill Indicator -->
                @if(request('category') || request('search'))
                    <div class="flex flex-wrap items-center gap-2">
                        @if(request('category'))
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 border border-blue-100 text-bps-lightBlue text-xs font-bold rounded-xl shadow-sm">
                                <span>Kategori: {{ request('category') }}</span>
                                <a href="{{ route('home', request()->except('category')) }}" class="hover:text-rose-600 transition-colors p-0.5" title="Hapus filter kategori">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </a>
                            </span>
                        @endif

                        @if(request('search'))
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold rounded-xl shadow-sm">
                                <span>Pencarian: "{{ request('search') }}"</span>
                                <a href="{{ route('home', request()->except('search')) }}" class="hover:text-rose-600 transition-colors p-0.5" title="Hapus pencarian">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </a>
                            </span>
                        @endif

                        <a href="{{ route('home') }}" class="text-xs font-bold text-rose-500 hover:text-rose-700 hover:underline px-2 py-1 transition-colors">
                            Reset Semua Filter
                        </a>
                    </div>
                @else
                    <span class="text-xs text-slate-400 font-bold bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-xl self-start md:self-auto">
                        Total: {{ $publications->count() }} Publikasi
                    </span>
                @endif
            </div>

            <!-- Horizontal Category Filter Pills (Quick 1-Click Filter) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 pt-1 no-scrollbar select-none">
                <!-- All Pill -->
                <a 
                    href="{{ route('home', request()->except('category')) }}" 
                    class="px-4 py-2 rounded-xl text-xs font-bold shrink-0 transition-all cursor-pointer {{ !request('category') ? 'bg-bps-navy text-white shadow-md shadow-slate-900/10' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
                >
                    Semua ({{ $totalAllPublications }})
                </a>

                <!-- 8 Categories Pills -->
                @foreach($officialCategories as $catName)
                    @php
                        $isPillActive = request('category') === $catName;
                        $catCount = $categoryCounts[$catName] ?? 0;
                    @endphp
                    <a 
                        href="{{ $isPillActive ? route('home', request()->except('category')) : route('home', array_merge(request()->except('page'), ['category' => $catName])) }}" 
                        class="px-4 py-2 rounded-xl text-xs font-bold shrink-0 transition-all cursor-pointer flex items-center gap-1.5 {{ $isPillActive ? 'bg-bps-lightBlue text-white shadow-md shadow-blue-500/20' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
                    >
                        <span>{{ $catName }}</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $isPillActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                            {{ $catCount }}
                        </span>
                    </a>
                @endforeach
            </div>

            <!-- Publications Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @forelse ($publications as $pub)
                    @php
                        // Category theme mappings
                        $catLower = strtolower($pub->category);
                        $coverGradient = 'from-blue-600 to-indigo-700';
                        $coverIcon = 'book-open';
                        $badgeStyle = 'bg-blue-50 text-blue-700 border-blue-100';

                        if (str_contains($catLower, 'sosial')) {
                            $coverGradient = 'from-amber-500 to-orange-600';
                            $coverIcon = 'users';
                            $badgeStyle = 'bg-amber-50 text-amber-700 border-amber-100';
                        } elseif (str_contains($catLower, 'ekonomi')) {
                            $coverGradient = 'from-emerald-500 to-teal-600';
                            $coverIcon = 'trending-up';
                            $badgeStyle = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                        } elseif (str_contains($catLower, 'pertanian')) {
                            $coverGradient = 'from-lime-600 to-emerald-700';
                            $coverIcon = 'sprout';
                            $badgeStyle = 'bg-lime-50 text-lime-800 border-lime-100';
                        } elseif (str_contains($catLower, 'industri')) {
                            $coverGradient = 'from-purple-500 to-violet-600';
                            $coverIcon = 'factory';
                            $badgeStyle = 'bg-purple-50 text-purple-700 border-purple-100';
                        } elseif (str_contains($catLower, 'distribusi')) {
                            $coverGradient = 'from-rose-500 to-pink-600';
                            $coverIcon = 'shopping-cart';
                            $badgeStyle = 'bg-rose-50 text-rose-700 border-rose-100';
                        } elseif (str_contains($catLower, 'lingkungan')) {
                            $coverGradient = 'from-teal-500 to-cyan-600';
                            $coverIcon = 'trees';
                            $badgeStyle = 'bg-teal-50 text-teal-700 border-teal-100';
                        } elseif (str_contains($catLower, 'sensus') || str_contains($catLower, 'survei')) {
                            $coverGradient = 'from-indigo-600 to-blue-700';
                            $coverIcon = 'clipboard-list';
                            $badgeStyle = 'bg-indigo-50 text-indigo-700 border-indigo-100';
                        }
                    @endphp

                    <div class="bg-white rounded-3xl border border-slate-100/80 p-5 md:p-6 shadow-sm flex flex-col sm:flex-row gap-5 hover:shadow-md hover:border-slate-200/60 transition-all duration-300 relative group">
                        
                        <!-- Left: Dynamic Book Cover (PDF Canvas OR CSS Fallback) -->
                        <a href="{{ route('user.publications.show', $pub->id) }}" class="shrink-0 flex items-center justify-center cursor-pointer">
                            <div class="relative w-28 h-36 shrink-0 rounded-xl shadow-md overflow-hidden border border-slate-100 bg-slate-50 select-none group-hover:scale-[1.02] transition-transform duration-300">
                                <!-- PDF Canvas Cover (rendered via PDF.js) -->
                                <canvas id="pdf-cover-canvas-{{ $pub->id }}" class="w-full h-full object-contain hidden rounded-xl" data-pdf-url="{{ $pub->pdf_path ? Storage::url($pub->pdf_path) : '' }}"></canvas>

                                <!-- CSS Fallback Placeholder (shown while loading or if PDF.js fails) -->
                                <div id="pdf-cover-placeholder-{{ $pub->id }}" class="relative w-full h-full bg-gradient-to-br {{ $coverGradient }} text-white p-3 flex flex-col justify-between border-r-3 border-black/10 overflow-hidden">
                                    <!-- Spine -->
                                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-white/5 border-r border-white/10 rounded-l-xl"></div>
                                    
                                    <!-- Top category code -->
                                    <div class="flex items-center gap-1 pl-1">
                                        <div class="w-1.5 h-1.5 bg-bps-orange rounded-full"></div>
                                        <span class="text-[6px] font-black tracking-widest text-slate-200 uppercase">{{ substr($pub->category, 0, 12) }}</span>
                                    </div>

                                    <!-- Center Cover Title -->
                                    <div class="pl-1 text-left">
                                        <h4 class="font-extrabold text-[8.5px] tracking-wide leading-tight line-clamp-3 uppercase text-left">{{ $pub->title }}</h4>
                                        <span class="text-[7.5px] text-bps-orange font-black block mt-0.5 text-left">{{ $pub->year }}</span>
                                    </div>

                                    <!-- Footer -->
                                    <div class="pl-1 border-t border-white/5 pt-1.5 flex items-center justify-between text-[6px] font-bold text-slate-300">
                                        <span>BPS KOTA</span>
                                        <i data-lucide="{{ $coverIcon }}" class="w-2.5 h-2.5 text-bps-orange"></i>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Right: Details Column -->
                        <div class="flex-1 flex flex-col justify-between min-w-0">
                            <div class="space-y-2">
                                <!-- Title -->
                                <h4 class="heading-font text-base font-extrabold text-bps-navy hover:text-bps-lightBlue transition-colors leading-snug line-clamp-2" title="{{ $pub->title }}">
                                    <a href="{{ route('user.publications.show', $pub->id) }}">
                                        {{ $pub->title }}
                                    </a>
                                </h4>

                                <!-- Category & Year Badge -->
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wide rounded-md border {{ $badgeStyle }}">
                                        {{ $pub->category }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-bold">{{ $pub->year }}</span>
                                    @if($pub->region)
                                        <span class="text-xs text-slate-300 font-medium">•</span>
                                        <span class="text-xs text-slate-500 font-semibold truncate">{{ $pub->region }}</span>
                                    @endif
                                </div>

                                <!-- Summary Excerpt Description -->
                                <p class="text-xs text-slate-500 font-medium leading-relaxed line-clamp-2">
                                    @php
                                        $displaySummary = $pub->summary ?: ($pub->aiResult?->summary ?: '');
                                    @endphp
                                    {{ $displaySummary ? explode("\n", $displaySummary)[0] : "Publikasi data statistik resmi Badan Pusat Statistik yang menyajikan informasi terkini serta indikator sektoral." }}
                                </p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-2.5 pt-4 border-t border-slate-50 mt-4">
                                <a 
                                    href="{{ route('user.publications.show', $pub->id) }}" 
                                    class="flex-1 py-2 px-3 bg-[#e8f0fe] hover:bg-[#d2e3fc] text-bps-lightBlue rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm"
                                >
                                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                    <span>Lihat Ringkasan</span>
                                </a>

                                @if ($pub->pdf_path)
                                    <a 
                                        href="{{ Storage::url($pub->pdf_path) }}" 
                                        download
                                        class="px-4 py-2 bg-bps-lightBlue hover:bg-blue-700 text-white rounded-xl font-bold text-xs transition-colors flex items-center justify-center gap-1 shadow-sm"
                                        title="Unduh Berkas PDF Asli"
                                    >
                                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                        <span>Unduh PDF</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="col-span-full bg-white rounded-3xl border border-slate-100 p-12 text-center flex flex-col items-center justify-center space-y-4 shadow-sm">
                        <div class="w-16 h-16 bg-blue-50 text-bps-lightBlue rounded-2xl flex items-center justify-center">
                            <i data-lucide="inbox" class="w-8 h-8"></i>
                        </div>
                        <div class="max-w-md space-y-1">
                            <h3 class="heading-font text-base font-bold text-bps-navy">Tidak ada publikasi ditemukan</h3>
                            <p class="text-xs text-slate-400 font-medium">
                                @if(request('category') || request('search'))
                                    Tidak ada publikasi yang cocok dengan kriteria filter atau pencarian Anda.
                                @else
                                    Publikasi yang diunggah oleh administrator akan otomatis muncul di sini.
                                @endif
                            </p>
                        </div>
                        @if(request('category') || request('search'))
                            <a href="{{ route('home') }}" class="px-5 py-2.5 bg-bps-lightBlue hover:bg-blue-700 text-white rounded-xl font-bold text-xs transition-all shadow-md shadow-blue-500/10">
                                Tampilkan Semua Publikasi
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>
        </section>

        <!-- SECTION 3: KEY FEATURE HIGHLIGHTS (TENTANG) -->
        <section id="tentang-layanan" class="bg-gradient-to-br from-slate-900 to-bps-navy text-white rounded-[2.5rem] p-8 md:p-12 relative overflow-hidden shadow-xl">
            <!-- Decorative circle blur -->
            <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Card 1 -->
                <div class="space-y-3">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-blue-300">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-extrabold text-sm md:text-base">Ringkasan Otomatis</h4>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">
                        Dapatkan inti informasi dari publikasi panjang dalam hitungan detik didukung analisis kecerdasan buatan.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="space-y-3">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-blue-300">
                        <i data-lucide="book-open" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-extrabold text-sm md:text-base">Mudah Dipahami</h4>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">
                        Hasil ringkasan disajikan dengan tata bahasa yang sederhana, poin-poin yang to-the-point dan informatif.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="space-y-3">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-blue-300">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-extrabold text-sm md:text-base">Akses Terbuka & Gratis</h4>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">
                        Seluruh publikasi terdaftar beserta ringkasannya dapat diakses secara gratis oleh seluruh pengguna umum.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="space-y-3">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-blue-300">
                        <i data-lucide="database" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-extrabold text-sm md:text-base">Sumber Resmi BPS</h4>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">
                        Seluruh data bersumber langsung dari Badan Pusat Statistik resmi sehingga akurasi kebenaran informasi terjamin.
                    </p>
                </div>
            </div>
        </section>

    </main>

    <!-- Include PDF.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();

            // Configure PDF.js worker URL
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

            // Loop and render publication cover pages
            @foreach($publications as $pub)
                @if($pub->pdf_path)
                    (function() {
                        const canvas = document.getElementById('pdf-cover-canvas-{{ $pub->id }}');
                        const placeholder = document.getElementById('pdf-cover-placeholder-{{ $pub->id }}');
                        const pdfUrl = '{{ Storage::url($pub->pdf_path) }}';

                        if (canvas && pdfUrl) {
                            pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
                                return pdf.getPage(1);
                            }).then(function(page) {
                                const viewport = page.getViewport({ scale: 0.8 });
                                const ctx = canvas.getContext('2d');
                                canvas.height = viewport.height;
                                canvas.width = viewport.width;

                                const renderContext = {
                                    canvasContext: ctx,
                                    viewport: viewport
                                };

                                return page.render(renderContext).promise;
                            }).then(function() {
                                if (placeholder) placeholder.classList.add('hidden');
                                canvas.classList.remove('hidden');
                            }).catch(function(err) {
                                console.warn("Failed to render thumbnail for publication {{ $pub->id }}:", err);
                            });
                        }
                    })();
                @endif
            @endforeach
        });
    </script>
@endsection
