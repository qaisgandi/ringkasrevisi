<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RINGKAS - Sistem Ringkasan Publikasi Statistik BPS</title>
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 73px;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        .bg-grid-pattern {
            background-size: 44px 44px;
            background-image: 
                linear-gradient(to right, rgba(2, 102, 179, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(2, 102, 179, 0.04) 1px, transparent 1px);
        }
        .glow-blue {
            filter: blur(120px);
            background: radial-gradient(circle, rgba(30, 93, 240, 0.15) 0%, rgba(2, 102, 179, 0.03) 65%, transparent 100%);
        }
        .glow-purple {
            filter: blur(120px);
            background: radial-gradient(circle, rgba(147, 51, 234, 0.1) 0%, rgba(79, 70, 229, 0.02) 65%, transparent 100%);
        }
    </style>
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50/50 text-slate-800 antialiased selection:bg-bps-lightBlue selection:text-white">

    <!-- HEADER / NAVIGATION -->
    <header class="sticky top-0 z-50 bg-bps-navy/95 backdrop-blur-md border-b border-white/5 px-4 md:px-12 py-4 shadow-md shadow-black/5">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <div class="w-12 h-8 shrink-0 flex items-center justify-center">
                    <img src="{{ asset('images/logo-bps.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="heading-font text-base font-black text-white leading-none tracking-tight">RINGKAS</h1>
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block mt-0.5">SISTEM RINGKASAN PUBLIKASI STATISTIK BPS</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-xs md:text-sm font-semibold">
                <a href="#beranda" class="text-slate-300 hover:text-white transition-colors">
                    Beranda
                </a>
                <a href="#cara-kerja" class="text-slate-300 hover:text-white transition-colors">
                    Cara Kerja
                </a>
                <a href="#fitur" class="text-slate-300 hover:text-white transition-colors">
                    Fitur
                </a>
                <button type="button" onclick="openFooterModal('about-modal')" class="text-slate-300 hover:text-white transition-colors cursor-pointer focus:outline-none">
                    Tentang
                </button>
            </nav>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="text-xs md:text-sm font-semibold text-slate-300 hover:text-white px-4 py-2 rounded-full transition-all">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="text-xs md:text-sm font-bold bg-bps-lightBlue hover:bg-blue-700 text-white px-5 py-2.5 rounded-full shadow-md shadow-blue-500/10 hover:shadow-lg transition-all">
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section id="beranda" class="relative bg-[#fafbfe] pt-16 pb-12 md:pt-24 md:pb-16 px-4 md:px-12 overflow-hidden border-b border-slate-100 bg-grid-pattern">
        <!-- Glowing Blob Decorations (Vercel-style) -->
        <div class="absolute top-[-10%] left-[-10%] w-[50%] aspect-square glow-blue rounded-full pointer-events-none z-0"></div>
        <div class="absolute bottom-[-10%] right-[10%] w-[45%] aspect-square glow-purple rounded-full pointer-events-none z-0"></div>
        <div class="absolute top-[20%] right-[-10%] w-[35%] aspect-square glow-blue rounded-full opacity-60 pointer-events-none z-0"></div>
        <!-- Fade Mask to smooth grid lines -->
        <div class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent pointer-events-none z-0"></div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10">
            <!-- Text Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-full text-[11px] font-bold text-bps-lightBlue uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-bps-lightBlue animate-pulse"></span>
                        Integrasi Ringkasan AI Terkini
                    </div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 border border-slate-200/80 rounded-full text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <span>📄 Mendukung Ribuan Publikasi Statistik BPS</span>
                    </div>
                </div>
                <h2 class="heading-font text-3xl md:text-5xl lg:text-[2.85rem] font-black text-bps-navy tracking-tight leading-tight lg:leading-[1.15]">
                    Pahami Publikasi Statistik BPS <br>
                    <span class="text-bps-lightBlue bg-clip-text">Lebih Cepat & Praktis</span>
                </h2>
                <p class="text-sm md:text-lg text-slate-500 font-medium max-w-2xl leading-relaxed mx-auto lg:mx-0">
                    <strong>RINGKAS</strong> membantu masyarakat memahami ribuan halaman publikasi statistik hanya dalam hitungan detik.
                </p>

                <!-- Stats Cards (4 Berjajar Sejajar) -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 max-w-2xl mx-auto lg:mx-0">
                    <!-- Card 1 -->
                    <div class="bg-white border border-slate-150 p-4 rounded-2xl flex flex-col items-center lg:items-start text-center lg:text-left space-y-1 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                        <span class="text-xl mb-1 block">📚</span>
                        <span class="text-base font-extrabold text-bps-navy leading-none">4.500+</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block pt-1">Publikasi</span>
                    </div>
                    <!-- Card 2 -->
                    <div class="bg-white border border-slate-150 p-4 rounded-2xl flex flex-col items-center lg:items-start text-center lg:text-left space-y-1 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                        <span class="text-xl mb-1 block">🤖</span>
                        <span class="text-base font-extrabold text-bps-navy leading-none">AI</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block pt-1">Ringkasan</span>
                    </div>
                    <!-- Card 3 -->
                    <div class="bg-white border border-slate-150 p-4 rounded-2xl flex flex-col items-center lg:items-start text-center lg:text-left space-y-1 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                        <span class="text-xl mb-1 block">⚡</span>
                        <span class="text-base font-extrabold text-bps-navy leading-none">&lt; 30 Detik</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block pt-1">Proses Instan</span>
                    </div>
                    <!-- Card 4 -->
                    <div class="bg-white border border-slate-150 p-4 rounded-2xl flex flex-col items-center lg:items-start text-center lg:text-left space-y-1 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                        <span class="text-xl mb-1 block">👥</span>
                        <span class="text-base font-extrabold text-bps-navy leading-none">Gratis</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block pt-1">Akses Umum</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto text-center px-8 py-4 bg-bps-lightBlue hover:bg-blue-700 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 hover:-translate-y-0.5 transition-all">
                        Mulai Jelajahi Data
                    </a>
                    <a href="#fitur" class="w-full sm:w-auto text-center px-8 py-4 bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 rounded-2xl font-bold hover:border-slate-300 transition-all">
                        Pelajari Layanan
                    </a>
                </div>
            </div>

            <!-- Graphic Display -->
            <div class="lg:col-span-5 flex items-center justify-center">
                <div class="relative w-full max-w-[500px] h-[420px] md:h-[450px] select-none">
                    
                    <!-- Decorative Blue Dot -->
                    <div class="absolute top-4 right-52 w-3.5 h-3.5 bg-blue-500 rounded-full opacity-65 animate-ping z-10"></div>
                    <div class="absolute top-4 right-52 w-3.5 h-3.5 bg-blue-500 rounded-full opacity-80 z-10" title="Dekorasi Biru"></div>

                    <!-- Card 1: Contoh Ringkasan -->
                    <div class="absolute left-0 top-2 w-[20rem] bg-white rounded-3xl border border-slate-100 shadow-[0_20px_50px_rgba(15,42,74,0.06)] p-5 z-20 hover:scale-105 hover:-translate-y-1 transition-all duration-300">
                        <div class="space-y-3 text-slate-600 text-xs">
                            <!-- Header / Judul Buku -->
                            <div class="space-y-1">
                                <span class="text-[9px] font-extrabold text-bps-lightBlue uppercase tracking-widest block">Contoh Ringkasan</span>
                                <h4 class="heading-font text-sm font-extrabold text-bps-navy leading-snug">Kabupaten Tasikmalaya Dalam Angka 2026</h4>
                            </div>
                            
                            <hr class="border-slate-100">

                            <!-- Section Ringkasan -->
                            <div class="space-y-1">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Ringkasan</span>
                                <p class="text-[10.5px] text-slate-500 leading-relaxed">
                                    Kabupaten Tasikmalaya mencatatkan pertumbuhan IPM yang positif didorong oleh sektor pendidikan dan kesehatan, serta penurunan angka kemiskinan.
                                </p>
                            </div>

                            <hr class="border-slate-100">

                            <!-- Section Topik -->
                            <div class="space-y-1">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Topik</span>
                                <div class="flex flex-wrap gap-x-2.5 gap-y-1 pt-0.5 text-[9.5px] font-extrabold text-slate-600">
                                    <span class="flex items-center gap-0.5 text-emerald-600">✓ Penduduk</span>
                                    <span class="flex items-center gap-0.5 text-emerald-600">✓ Pendidikan</span>
                                    <span class="flex items-center gap-0.5 text-emerald-600">✓ Inflasi</span>
                                </div>
                            </div>

                            <hr class="border-slate-100">

                            <!-- Section Keyword -->
                            <div class="space-y-1.5">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Keyword</span>
                                <div class="flex flex-wrap gap-1">
                                    <span class="px-2 py-0.5 bg-blue-50 text-bps-lightBlue border border-blue-100 rounded text-[8.5px] font-extrabold">IPM</span>
                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded text-[8.5px] font-extrabold">PDRB</span>
                                    <span class="px-2 py-0.5 bg-emerald-50 text-bps-green border border-emerald-100 rounded text-[8.5px] font-extrabold">Kemiskinan</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Decorative Green Dot -->
                    <div class="absolute bottom-16 left-8 w-4 h-4 bg-emerald-400 rounded-full opacity-70 animate-bounce z-10" title="Dekorasi Hijau"></div>

                    <!-- Card 2: Keyword -->
                    <div class="absolute right-0 top-8 w-44 bg-white rounded-2xl border border-slate-100 shadow-[0_15px_35px_rgba(15,42,74,0.05)] p-4 z-30 hover:scale-105 hover:-translate-y-1 transition-all duration-300">
                        <div class="space-y-2.5">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block">Keyword</span>
                            <div class="flex flex-col gap-1.5">
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-xl text-xs font-semibold block text-center">Penduduk</span>
                                <span class="px-2.5 py-1 bg-red-50 text-red-700 border border-red-100 rounded-xl text-xs font-semibold block text-center">Inflasi</span>
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl text-xs font-semibold block text-center">IPM</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Statistik -->
                    <div class="absolute right-2 bottom-8 w-48 bg-white rounded-2xl border border-slate-100 shadow-[0_15px_35px_rgba(15,42,74,0.05)] p-4 z-40 hover:scale-105 hover:-translate-y-1 transition-all duration-300">
                        <div class="space-y-2.5">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block">Statistik</span>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs font-bold text-slate-600">
                                    <span>IPM</span>
                                    <span class="text-emerald-500 flex items-center gap-0.5 font-extrabold">75.12 <i data-lucide="trending-up" class="w-3.5 h-3.5"></i></span>
                                </div>
                                <div class="flex items-center justify-between text-xs font-bold text-slate-600">
                                    <span>Inflasi</span>
                                    <span class="text-blue-500 flex items-center gap-0.5 font-extrabold">1.95% <i data-lucide="trending-down" class="w-3.5 h-3.5"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- WORKFLOW SECTION -->
    <section id="cara-kerja" class="py-10 md:py-12 px-4 md:px-12 bg-white border-b border-slate-100 relative overflow-hidden">
        <!-- Background subtle glows for premium look -->
        <div class="absolute top-10 left-1/2 -translate-x-1/2 w-96 h-96 rounded-full bg-blue-100/10 blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto space-y-12">
            <!-- Section Title -->
            <div class="text-center space-y-3">
                <span class="text-[10px] font-bold text-bps-lightBlue bg-blue-50/50 px-3 py-1 rounded-full border border-blue-100/30">Alur Sistem</span>
                <h3 class="heading-font text-2xl md:text-3xl font-black text-bps-navy tracking-tight">Bagaimana RINGKAS Bekerja?</h3>
                <p class="text-sm text-slate-400 font-semibold max-w-xl mx-auto">Dari berkas data mentah hingga menjadi intisari laporan yang siap dibaca dalam sekejap.</p>
            </div>

            <!-- Workflow Steps Container -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 lg:gap-8 relative">
                <!-- Connecting Line for Desktop -->
                <div class="hidden md:block absolute top-[3.25rem] left-[10%] right-[10%] h-0.5 bg-gradient-to-r from-blue-100 via-bps-lightBlue/30 to-emerald-100 z-0"></div>

                <!-- Step 1: Upload PDF -->
                <div class="relative bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center space-y-4 z-10 group">
                    <div class="w-14 h-14 bg-blue-50 text-bps-lightBlue rounded-2xl flex items-center justify-center shadow-sm border border-blue-100/30 transition-transform group-hover:scale-110">
                        <i data-lucide="file-up" class="w-6 h-6"></i>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-extrabold text-blue-500 uppercase tracking-widest">Langkah 1</span>
                        <h4 class="heading-font text-sm font-extrabold text-bps-navy">Upload PDF</h4>
                        <p class="text-xs text-slate-400 leading-relaxed font-medium">Unggah berkas publikasi PDF resmi BPS yang ingin Anda pelajari.</p>
                    </div>
                </div>

                <!-- Step 2: Ekstraksi -->
                <div class="relative bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center space-y-4 z-10 group">
                    <div class="w-14 h-14 bg-indigo-50/50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm border border-indigo-100/30 transition-transform group-hover:scale-110">
                        <i data-lucide="scan-text" class="w-6 h-6"></i>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-extrabold text-indigo-500 uppercase tracking-widest">Langkah 2</span>
                        <h4 class="heading-font text-sm font-extrabold text-bps-navy">Ekstraksi</h4>
                        <p class="text-xs text-slate-400 leading-relaxed font-medium">Sistem membaca dan memilah teks, tabel data, serta struktur bab secara otomatis.</p>
                    </div>
                </div>

                <!-- Step 3: AI -->
                <div class="relative bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center space-y-4 z-10 group">
                    <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center shadow-sm border border-purple-100/30 transition-transform group-hover:scale-110">
                        <i data-lucide="cpu" class="w-6 h-6"></i>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-extrabold text-purple-500 uppercase tracking-widest">Langkah 3</span>
                        <h4 class="heading-font text-sm font-extrabold text-bps-navy">Pemrosesan AI</h4>
                        <p class="text-xs text-slate-400 leading-relaxed font-medium">Kecerdasan buatan menyusun ringkasan berdasarkan poin indikator strategis.</p>
                    </div>
                </div>

                <!-- Step 4: Ringkasan -->
                <div class="relative bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center space-y-4 z-10 group">
                    <div class="w-14 h-14 bg-amber-50 text-bps-orange rounded-2xl flex items-center justify-center shadow-sm border border-amber-100/30 transition-transform group-hover:scale-110">
                        <i data-lucide="file-text" class="w-6 h-6"></i>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-extrabold text-bps-orange uppercase tracking-widest">Langkah 4</span>
                        <h4 class="heading-font text-sm font-extrabold text-bps-navy">Hasil Ringkasan</h4>
                        <p class="text-xs text-slate-400 leading-relaxed font-medium">Informasi data inti disajikan dalam bentuk daftar poin penting yang terstruktur rapi.</p>
                    </div>
                </div>

                <!-- Step 5: Selesai -->
                <div class="relative bg-white border border-slate-100 p-6 rounded-3xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center space-y-4 z-10 group">
                    <div class="w-14 h-14 bg-emerald-50 text-bps-green rounded-2xl flex items-center justify-center shadow-sm border border-emerald-100/30 transition-transform group-hover:scale-110">
                        <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-extrabold text-bps-green uppercase tracking-widest">Langkah 5</span>
                        <h4 class="heading-font text-sm font-extrabold text-bps-navy">Selesai</h4>
                        <p class="text-xs text-slate-400 leading-relaxed font-medium">Ringkasan siap disalin, dirujuk, atau digunakan untuk riset dan penulisan laporan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="fitur" class="py-12 md:py-16 px-4 md:px-12 max-w-7xl mx-auto space-y-10">
        <div class="text-center space-y-4">
            <h3 class="heading-font text-2xl md:text-3xl font-black text-bps-navy tracking-tight">Kelebihan Menggunakan RINGKAS</h3>
            <p class="text-sm md:text-base text-slate-400 font-semibold max-w-xl mx-auto">Dirancang untuk memudahkan akademisi, instansi pemerintah, dan masyarakat dalam memahami statistik BPS.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="bg-white border border-slate-100 p-8 rounded-3xl space-y-4 hover:shadow-lg transition-all duration-300">
                <div class="w-12 h-12 bg-blue-50 text-bps-lightBlue rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h4 class="heading-font text-lg font-bold text-bps-navy">Proses Instan</h4>
                <p class="text-xs md:text-sm text-slate-500 leading-relaxed font-medium">
                    Tidak perlu lagi menelusuri ratusan halaman PDF. AI kami merangkum dokumen panjang hanya dalam beberapa detik dengan akurasi tinggi.
                </p>
            </div>
            <!-- Card 2 -->
            <div class="bg-white border border-slate-100 p-8 rounded-3xl space-y-4 hover:shadow-lg transition-all duration-300">
                <div class="w-12 h-12 bg-emerald-50 text-bps-green rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h4 class="heading-font text-lg font-bold text-bps-navy">Struktur Poin Penting</h4>
                <p class="text-xs md:text-sm text-slate-500 leading-relaxed font-medium">
                    Poin-poin ringkasan disajikan secara to-the-point dan informatif, memudahkan penyerapan indikator utama dengan format yang bersih.
                </p>
            </div>
            <!-- Card 3 -->
            <div class="bg-white border border-slate-100 p-8 rounded-3xl space-y-4 hover:shadow-lg transition-all duration-300">
                <div class="w-12 h-12 bg-amber-50 text-bps-orange rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h4 class="heading-font text-lg font-bold text-bps-navy">Akses Terbuka & Aman</h4>
                <p class="text-xs md:text-sm text-slate-500 leading-relaxed font-medium">
                    Akses publikasi terbuka untuk seluruh pengguna umum secara aman. Database terstruktur yang terlindungi dengan baik.
                </p>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    @include('layouts.footer')

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
