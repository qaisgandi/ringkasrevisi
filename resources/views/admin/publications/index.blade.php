@extends('layouts.admin')

@section('title', ($activeMenu === 'summaries' ? 'Daftar Ringkasan Publikasi' : 'Daftar Publikasi') . ' - Sistem Ringkasan & Manajemen Publikasi Statistik BPS')

@section('content')
<!-- Include PDF.js & Lucide CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<div class="space-y-8 fade-in">
    
    <!-- HEADER SECTION -->
    <div class="relative bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 overflow-hidden">
        <!-- Decorative Background Circle -->
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-blue-50/50 rounded-full blur-3xl -z-10"></div>
        
        <div class="space-y-1">
            <h1 class="heading-font text-2xl font-extrabold text-bps-navy tracking-tight">
                {{ $activeMenu === 'summaries' ? 'Ringkasan Publikasi' : 'Daftar Publikasi' }}
            </h1>
            <p class="text-sm text-slate-400 font-medium leading-relaxed max-w-xl">
                {{ $activeMenu === 'summaries' ? 'Lihat ringkasan hasil analisis AI dari publikasi yang telah diunggah.' : 'Kelola seluruh publikasi statistik BPS Kota & Kabupaten Tasikmalaya.' }}
            </p>
        </div>

        <div class="shrink-0 flex items-center gap-4">
            <!-- Decorative Icon Art -->
            <div class="hidden sm:flex w-16 h-16 rounded-2xl bg-blue-50/70 border border-blue-100/50 items-center justify-center relative shadow-sm">
                <i data-lucide="sparkles" class="w-6 h-6 text-bps-primary animate-pulse"></i>
                <div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-bps-orange rounded-lg flex items-center justify-center text-[9px] font-black text-white shadow-md">AI</div>
            </div>
            
            @if ($activeMenu !== 'summaries')
                <a 
                    href="{{ route('publications.create') }}" 
                    class="inline-flex items-center gap-2 px-5 py-3 bg-bps-primary hover:bg-blue-700 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/10 hover:shadow-blue-500/25 transition-all duration-200 text-sm hover:-translate-y-0.5 active:translate-y-0"
                >
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Upload Publikasi</span>
                </a>
            @endif
        </div>
    </div>

    <!-- SUCCESS NOTIFICATION & POPUP MODAL -->
    @if (session('success'))
        @php
            $successMsg = session('success');
            $isDelete = str_contains(strtolower($successMsg), 'hapus') || str_contains(strtolower($successMsg), 'delete');
            $isEdit = str_contains(strtolower($successMsg), 'perbarui') || str_contains(strtolower($successMsg), 'edit');
        @endphp

        <!-- Inline Alert Banner -->
        <div id="inline-success-alert" class="fade-in flex items-center justify-between gap-3 {{ $isDelete ? 'bg-rose-50 border border-rose-200/60 text-rose-800' : ($isEdit ? 'bg-amber-50 border border-amber-200/60 text-amber-800' : 'bg-emerald-50 border border-emerald-200/60 text-emerald-800') }} px-5 py-4 rounded-2xl text-sm shadow-sm transition-all duration-300 mb-6" role="alert">
            <div class="flex items-center gap-3">
                @if ($isDelete)
                    <i data-lucide="trash-2" class="w-5 h-5 text-rose-500 shrink-0"></i>
                @elseif ($isEdit)
                    <i data-lucide="edit-3" class="w-5 h-5 text-amber-500 shrink-0"></i>
                @else
                    <i data-lucide="check" class="w-5 h-5 text-emerald-500 shrink-0"></i>
                @endif
                <div>
                    <span class="font-bold">Sukses:</span> {{ $successMsg }}
                </div>
            </div>
            <button onclick="document.getElementById('inline-success-alert').remove()" class="{{ $isDelete ? 'text-rose-500 hover:text-rose-700 hover:bg-rose-100/50' : ($isEdit ? 'text-amber-500 hover:text-amber-700 hover:bg-amber-100/50' : 'text-emerald-500 hover:text-emerald-700 hover:bg-emerald-100/50') }} transition-colors p-1 rounded-lg" title="Tutup Notifikasi">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Success Modal Popup -->
        <div id="success-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <!-- Backdrop with blur -->
            <div id="modal-backdrop" onclick="closeSuccessModal()" class="fixed inset-0 bg-slate-900/45 backdrop-blur-sm transition-opacity duration-300 ease-out opacity-0"></div>
            
            <!-- Modal Card -->
            <div id="modal-card" class="relative bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl border border-slate-100/80 flex flex-col items-center text-center transform scale-90 opacity-0 transition-all duration-300 ease-out z-10">
                <!-- Close Button -->
                <button onclick="closeSuccessModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>

                @if ($isDelete)
                    <!-- Trash Icon Circle with micro-animation -->
                    <div class="w-20 h-20 rounded-full bg-rose-50 border-4 border-rose-100/50 text-rose-500 flex items-center justify-center mb-5 relative shrink-0 shadow-inner animate-bounce">
                        <i data-lucide="trash-2" class="w-10 h-10"></i>
                    </div>

                    <!-- Modal Text Content -->
                    <h3 class="heading-font text-xl font-extrabold text-bps-navy tracking-tight mb-2">Dokumen Berhasil Dihapus</h3>
                    <p class="text-sm text-slate-500 leading-relaxed font-medium mb-6">
                        Data publikasi beserta file PDF terkait telah berhasil dihapus secara permanen dari sistem.
                    </p>

                    <!-- Button Selesai (Rose) -->
                    <button 
                        onclick="closeSuccessModal()" 
                        class="w-full py-3 px-5 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/10 hover:shadow-rose-500/25 transition-all duration-200 text-sm hover:-translate-y-0.5 active:translate-y-0"
                    >
                        Selesai
                    </button>
                @elseif ($isEdit)
                    <!-- Pencil Icon Circle with micro-animation -->
                    <div class="w-20 h-20 rounded-full bg-amber-50 border-4 border-amber-100/50 text-amber-500 flex items-center justify-center mb-5 relative shrink-0 shadow-inner animate-bounce">
                        <i data-lucide="edit-3" class="w-10 h-10"></i>
                    </div>

                    <!-- Modal Text Content -->
                    <h3 class="heading-font text-xl font-extrabold text-bps-navy tracking-tight mb-2">Perubahan Berhasil Disimpan</h3>
                    <p class="text-sm text-slate-500 leading-relaxed font-medium mb-6">
                        Data publikasi statistik BPS telah berhasil diperbarui dan disimpan ke dalam sistem.
                    </p>

                    <!-- Button Selesai (Amber) -->
                    <button 
                        onclick="closeSuccessModal()" 
                        class="w-full py-3 px-5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-2xl shadow-lg shadow-amber-500/10 hover:shadow-amber-500/25 transition-all duration-200 text-sm hover:-translate-y-0.5 active:translate-y-0"
                    >
                        Selesai
                    </button>
                @else
                    <!-- Success Icon Circle with micro-animation -->
                    <div class="w-20 h-20 rounded-full bg-emerald-50 border-4 border-emerald-100/50 text-emerald-500 flex items-center justify-center mb-5 relative shrink-0 shadow-inner animate-bounce">
                        <i data-lucide="check" class="w-10 h-10"></i>
                    </div>

                    <!-- Modal Text Content -->
                    <h3 class="heading-font text-xl font-extrabold text-bps-navy tracking-tight mb-2">Dokumen Berhasil Diunggah</h3>
                    <p class="text-sm text-slate-500 leading-relaxed font-medium mb-6">
                        Berkas publikasi Anda telah diunggah dan sekarang siap diproses di dalam sistem.
                    </p>

                    <!-- Button Selesai (Emerald) -->
                    <button 
                        onclick="closeSuccessModal()" 
                        class="w-full py-3 px-5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/10 hover:shadow-emerald-500/25 transition-all duration-200 text-sm hover:-translate-y-0.5 active:translate-y-0"
                    >
                        Selesai
                    </button>
                @endif
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('success-modal');
                const backdrop = document.getElementById('modal-backdrop');
                const card = document.getElementById('modal-card');

                // Animate entry
                setTimeout(() => {
                    if (backdrop && card) {
                        backdrop.classList.remove('opacity-0');
                        backdrop.classList.add('opacity-100');
                        card.classList.remove('opacity-0', 'scale-90');
                        card.classList.add('opacity-100', 'scale-100');
                    }
                }, 50);

                // Auto-close modal after 6 seconds
                window.successModalTimer = setTimeout(closeSuccessModal, 6000);
            });

            window.closeSuccessModal = function() {
                const backdrop = document.getElementById('modal-backdrop');
                const card = document.getElementById('modal-card');
                const modal = document.getElementById('success-modal');

                if (window.successModalTimer) {
                    clearTimeout(window.successModalTimer);
                }

                if (backdrop && card) {
                    // Animate exit
                    backdrop.classList.remove('opacity-100');
                    backdrop.classList.add('opacity-0');
                    card.classList.remove('opacity-100', 'scale-100');
                    card.classList.add('opacity-0', 'scale-90');

                    // Remove from DOM after transition completes
                    setTimeout(() => {
                        if (modal) modal.remove();
                    }, 300);
                }
            }
        </script>
    @endif

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Ringkasan -->
        <div class="group bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-all duration-300">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-bps-primary flex items-center justify-center group-hover:bg-bps-primary group-hover:text-white transition-all duration-300 shrink-0">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Ringkasan</p>
                <h3 class="heading-font text-2xl font-black text-bps-navy mt-0.5">{{ $totalPublicationsCount }}</h3>
                <p class="text-[10px] text-slate-400 font-semibold">Semua publikasi</p>
            </div>
        </div>

        <!-- Card 2: Selesai -->
        <div class="group bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-all duration-300">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shrink-0">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Selesai</p>
                <h3 class="heading-font text-2xl font-black text-bps-navy mt-0.5">{{ $totalSelesaiCount }}</h3>
                <p class="text-[10px] text-slate-400 font-semibold">Ringkasan tersedia</p>
            </div>
        </div>

        <!-- Card 3: Sedang Diproses -->
        <div class="group bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-all duration-300">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-bps-orange flex items-center justify-center group-hover:bg-bps-orange group-hover:text-white transition-all duration-300 shrink-0">
                <i data-lucide="clock" class="w-6 h-6 animate-spin-slow"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Diproses</p>
                <h3 class="heading-font text-2xl font-black text-bps-navy mt-0.5">{{ $totalPendingCount }}</h3>
                <p class="text-[10px] text-slate-400 font-semibold">Sedang dianalisis</p>
            </div>
        </div>

        <!-- Card 4: Kategori -->
        <div class="group bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-all duration-300">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shrink-0">
                <i data-lucide="folder" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori</p>
                <h3 class="heading-font text-2xl font-black text-bps-navy mt-0.5">{{ $totalCategoriesCount }}</h3>
                <p class="text-[10px] text-slate-400 font-semibold">Kategori publikasi</p>
            </div>
        </div>
    </div>

    <!-- FILTER & TOOLBAR SECTION -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 md:p-5 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        
        <!-- Left: Search & Select Dropdowns -->
        <div class="flex flex-wrap items-center gap-3 flex-1">
            <!-- Search bar -->
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                </span>
                <input 
                    type="text" 
                    id="local-search"
                    placeholder="Cari berdasarkan judul, topik, atau kata kunci..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 font-medium focus:outline-none focus:border-bps-primary focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200"
                >
            </div>

            <!-- Category Filter -->
            <select 
                id="filter-category" 
                class="custom-select px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-600 font-bold focus:outline-none focus:border-bps-primary focus:bg-white transition-all"
            >
                <option value="all" {{ request('category') == '' ? 'selected' : '' }}>Semua Kategori</option>
                @foreach (['Publikasi Umum', 'Statistik Sosial', 'Statistik Ekonomi', 'Statistik Pertanian', 'Statistik Industri', 'Statistik Distribusi', 'Statistik Lingkungan', 'Sensus & Survei'] as $catOption)
                    <option value="{{ $catOption }}" {{ request('category') === $catOption ? 'selected' : '' }}>{{ $catOption }}</option>
                @endforeach
            </select>

            <!-- Year Filter -->
            @php
                $years = \App\Models\Publication::pluck('year')->unique()->sortDesc();
            @endphp
            <select 
                id="filter-year" 
                class="custom-select px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-600 font-bold focus:outline-none focus:border-bps-primary focus:bg-white transition-all"
            >
                <option value="all" {{ request('year') == '' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach ($years as $yr)
                    <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                @endforeach
            </select>

            <!-- Status Filter -->
            <select 
                id="filter-status" 
                class="custom-select px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-600 font-bold focus:outline-none focus:border-bps-primary focus:bg-white transition-all"
            >
                <option value="all" {{ request('status') == '' ? 'selected' : '' }}>Semua Status</option>
                <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
            </select>

            <!-- Reset Button -->
            <button 
                id="btn-reset-filters" 
                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-200 rounded-xl font-bold text-xs transition-all"
            >
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                <span>Reset</span>
            </button>
        </div>

        <!-- Right: Sort & Layout Toggles -->
        <div class="flex items-center justify-between xl:justify-end gap-4 border-t xl:border-t-0 border-slate-100 pt-3 xl:pt-0">
            <!-- Sort selector -->
            <select 
                id="sort-order" 
                class="custom-select px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-600 font-bold focus:outline-none focus:border-bps-primary focus:bg-white transition-all"
            >
                <option value="newest">Terbaru</option>
                <option value="oldest">Terlama</option>
                <option value="title-az">Judul A-Z</option>
                <option value="title-za">Judul Z-A</option>
            </select>

            <!-- Layout Switchers -->
            <div class="flex items-center border border-slate-200 rounded-xl p-1 bg-slate-50/50 shrink-0">
                <button 
                    id="btn-layout-grid" 
                    class="p-1.5 rounded-lg text-bps-primary bg-white shadow-sm transition-all" 
                    title="Grid View"
                >
                    <i data-lucide="layout-grid" class="w-4 h-4"></i>
                </button>
                <button 
                    id="btn-layout-list" 
                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 transition-all" 
                    title="List View"
                >
                    <i data-lucide="list" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- CARDS GRID LIST -->
    @if ($publications->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-16 text-center flex flex-col items-center justify-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mb-4">
                <i data-lucide="file-warning" class="w-10 h-10"></i>
            </div>
            <h3 class="heading-font text-base font-bold text-slate-700">Belum Ada Publikasi</h3>
            <p class="text-sm text-slate-400 mt-1 max-w-sm">Daftar publikasi statistik Anda masih kosong. Silakan unggah dokumen PDF baru untuk memulai.</p>
        </div>
    @else
        <!-- Grid layout container -->
        <div 
            id="publications-container" 
            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 transition-all duration-300"
        >
            @foreach ($publications as $publication)
                @php
                    // Retrieve up to 3 topics/keywords as search tags
                    $tagStr = '';
                    if ($publication->topics && is_array($publication->topics)) {
                        $tagStr .= implode(' ', $publication->topics);
                    }
                    if ($publication->keywords && is_array($publication->keywords)) {
                        $tagStr .= ' ' . implode(' ', $publication->keywords);
                    }
                @endphp
                <div 
                    class="pub-card bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 p-5 flex gap-4 h-full relative" 
                    data-title="{{ strtolower($publication->title) }}"
                    data-category="{{ $publication->category }}"
                    data-year="{{ $publication->year }}"
                    data-status="{{ $publication->status }}"
                    data-tags="{{ strtolower($tagStr) }}"
                    data-date="{{ $publication->created_at->timestamp }}"
                >
                    <!-- Left side: Cover Canvas (rendered via PDF.js) -->
                    <div class="w-24 sm:w-28 h-36 sm:h-40 shrink-0 rounded-xl relative overflow-hidden border border-slate-200 bg-slate-50 shadow-inner group/cover select-none">
                        <canvas class="pdf-canvas w-full h-full object-contain hidden" data-pdf-url="/storage/{{ $publication->pdf_path }}"></canvas>
                        
                        <!-- Fallback BPS CSS Cover -->
                        <div class="pdf-placeholder w-full h-full bg-gradient-to-br from-bps-navy to-blue-900 flex flex-col justify-between p-3 text-white transition-opacity duration-300">
                            <span class="text-[7px] font-black uppercase text-bps-orange tracking-widest leading-none">BPS</span>
                            <i data-lucide="book-open" class="w-4 h-4 text-white/70 self-center"></i>
                            <span class="text-[8px] font-bold text-center truncate max-w-full leading-tight select-none">{{ $publication->year }}</span>
                        </div>
                    </div>

                    <!-- Right side: Details -->
                    <div class="flex-1 flex flex-col justify-between min-w-0">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] font-extrabold text-bps-primary tracking-wide truncate">
                                    {{ $publication->category }}
                                </span>
                                
                                <!-- Status Badge -->
                                @if ($publication->status === 'Selesai')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[9px] font-bold select-none shrink-0" title="Proses ekstraksi telah selesai">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-100 text-[9px] font-bold select-none shrink-0 animate-pulse" title="Dokumen siap untuk proses ekstraksi">
                                        <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @endif
                            </div>

                            <h3 class="heading-font text-sm font-extrabold text-bps-navy line-clamp-2 leading-snug hover:text-bps-primary transition-colors">
                                <a href="{{ route('publications.show', $publication) }}" title="{{ $publication->title }}">
                                    {{ $publication->title }}
                                </a>
                            </h3>

                            <div class="text-[9px] text-slate-400 font-bold flex items-center gap-1">
                                <span>{{ $publication->year }}</span>
                            </div>

                            <p class="text-slate-500 text-[10px] leading-relaxed line-clamp-2 font-medium">
                                @php
                                    $displaySummary = $publication->summary ?: ($publication->aiResult?->summary ?: '');
                                @endphp
                                {{ $displaySummary ? explode("\n", $displaySummary)[0] : 'Data ringkasan publikasi statistik belum diproses oleh AI.' }}
                            </p>
                        </div>

                        <div class="space-y-2 mt-2">
                            <!-- Badges / Topics -->
                            @php
                                $displayTopics = $publication->topics ?: ($publication->aiResult?->topics ?: []);
                            @endphp
                            @if (is_array($displayTopics) && count($displayTopics) > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach (array_slice($displayTopics, 0, 2) as $topic)
                                        <span class="px-2 py-0.5 bg-slate-50 text-slate-500 border border-slate-100 rounded-lg text-[9px] font-bold">
                                            {{ $topic }}
                                        </span>
                                    @endforeach
                                    @if (count($displayTopics) > 2)
                                        <span class="px-2 py-0.5 bg-blue-50 text-bps-primary border border-blue-100 rounded-lg text-[9px] font-bold">
                                            +{{ count($displayTopics) - 2 }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- Metadata Row -->
                            <div class="flex items-center justify-between text-[10px] text-slate-400 font-semibold border-t border-slate-100/70 pt-2.5">
                                <div class="flex items-center gap-3">
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-slate-300"></i>
                                        {{ $publication->page_count ? $publication->page_count . ' Hlm' : '- Hlm' }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-300"></i>
                                        {{ $publication->created_at->format('d M Y') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Buttons Row -->
                            <div class="flex items-center gap-2 pt-2">
                                @if ($publication->status !== 'Selesai')
                                    <!-- Lakukan Ringkas Form Button -->
                                    <form action="{{ route('publications.extract', $publication) }}" method="POST" class="m-0 flex-1">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="w-full py-2 bg-bps-primary hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/10 hover:shadow-blue-500/25 transition-all text-xs text-center flex items-center justify-center gap-1.5"
                                        >
                                            <i data-lucide="play" class="w-3 h-3"></i>
                                            <span>Lakukan Ringkas</span>
                                        </button>
                                    </form>
                                @else
                                    <!-- Full Width Detail Link -->
                                    <a 
                                        href="{{ route('publications.show', $publication) }}" 
                                        class="flex-1 py-2 bg-blue-50 hover:bg-blue-100 text-bps-primary border border-blue-100 hover:border-blue-200 font-bold rounded-xl transition-all text-xs text-center flex items-center justify-center gap-1 group/btn"
                                    >
                                        <span>Lihat Detail</span>
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 transition-transform group-hover/btn:translate-x-0.5"></i>
                                    </a>
                                @endif

                                <!-- Edit Link -->
                                <a 
                                    href="{{ route('publications.edit', $publication) }}"
                                    class="px-2.5 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 font-bold rounded-xl transition-all flex items-center justify-center shrink-0"
                                    title="Edit Publikasi"
                                >
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                </a>

                                <!-- Hapus Button -->
                                <button 
                                    type="button" 
                                    data-delete-url="{{ route('publications.destroy', $publication, false) }}"
                                    data-pub-title="{{ $publication->title }}"
                                    class="btn-delete px-2.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100 transition-all font-bold rounded-xl flex items-center justify-center shrink-0"
                                    title="Hapus Publikasi"
                                >
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Search/Filter empty notification -->
        <div id="search-empty-state" class="hidden bg-white rounded-2xl shadow-sm border border-slate-100 p-16 text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mb-4">
                <i data-lucide="search" class="w-8 h-8"></i>
            </div>
            <h3 class="heading-font text-base font-bold text-slate-700">Pencarian Tidak Ditemukan</h3>
            <p class="text-sm text-slate-400 mt-1 max-w-sm">Tidak ada ringkasan publikasi yang cocok dengan kriteria filter atau kata kunci Anda.</p>
            <button 
                onclick="resetFilters()"
                class="mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all"
            >
                Reset Filter
            </button>
        </div>
    @endif

    <!-- DELETE CONFIRMATION MODAL -->
    <div id="delete-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <!-- Backdrop -->
        <div id="delete-backdrop" onclick="closeDeleteModal()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>
        
        <!-- Card -->
        <div id="delete-card" class="relative bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl border border-slate-100 flex flex-col items-center text-center transform scale-90 opacity-0 transition-all duration-300 ease-out z-10">
            <!-- Close Button -->
            <button onclick="closeDeleteModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 hover:bg-slate-50 p-1.5 rounded-xl transition-all">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <!-- Warning Icon -->
            <div class="w-16 h-16 rounded-full bg-rose-50 border-4 border-rose-100 text-rose-500 flex items-center justify-center mb-4 shrink-0 shadow-inner">
                <i data-lucide="alert-triangle" class="w-8 h-8"></i>
            </div>

            <!-- Title & Subtitle -->
            <h3 class="heading-font text-lg font-extrabold text-bps-navy tracking-tight mb-2">Hapus Publikasi?</h3>
            <p class="text-sm text-slate-500 leading-relaxed font-medium mb-6">
                Apakah Anda yakin ingin menghapus publikasi "<span id="delete-pub-title" class="font-bold text-slate-700"></span>"? Tindakan ini akan menghapus data beserta file PDF secara permanen.
            </p>

            <!-- Buttons Grid -->
            <div class="grid grid-cols-2 gap-3 w-full">
                <button 
                    onclick="closeDeleteModal()" 
                    class="w-full py-3 px-4 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 font-bold rounded-2xl transition-all duration-200 text-sm"
                >
                    Batal
                </button>
                <form id="delete-form" method="POST" class="m-0 w-full">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="w-full py-3 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/10 hover:shadow-rose-500/25 transition-all duration-200 text-sm"
                    >
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- LOADING OVERLAY -->
    <div id="loading-overlay" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md select-none">
        <div class="relative bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl border border-slate-100/80 flex flex-col items-center text-center">
            <!-- Floating BPS-Navy Logo & Spinning Ring -->
            <div class="relative w-24 h-24 mb-6">
                <!-- Spinner Ring -->
                <div class="absolute inset-0 rounded-full border-4 border-slate-100 border-t-bps-primary animate-spin"></div>
                <!-- BPS Inner Icon Circle -->
                <div class="absolute inset-3 bg-blue-50 rounded-full flex items-center justify-center text-bps-primary animate-pulse">
                    <i data-lucide="sparkles" class="w-8 h-8 text-bps-primary"></i>
                </div>
            </div>

            <!-- Text Title -->
            <h3 class="heading-font text-lg font-extrabold text-bps-navy tracking-tight mb-2">Mengekstrak & Meringkas</h3>
            
            <!-- Text Subtitle / Progress -->
            <p id="loading-text" class="text-sm text-slate-500 leading-relaxed font-semibold">
                Menjalankan AI untuk menganalisis isi dokumen PDF...
            </p>

            <!-- Small warning -->
            <span class="text-[10px] text-slate-400 font-medium mt-4">Mohon tunggu sebentar, proses ini memerlukan waktu beberapa detik.</span>
        </div>
    </div>
</div>

<!-- CLIENT-SIDE FILTERING, SORTING, LAYOUT TOGGLING, AND PDF COVER RENDERING -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // 1. PDF.js Cover Page Rendering Loop
        // Configure PDF.js worker URL
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
        
        const canvases = document.querySelectorAll('.pdf-canvas');
        canvases.forEach(function(canvas) {
            const pdfUrl = canvas.getAttribute('data-pdf-url');
            const placeholder = canvas.nextElementSibling; // the placeholder div
            
            if (pdfUrl) {
                // Load PDF and render first page
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
                    // Success: Show canvas and hide placeholder
                    placeholder.classList.add('hidden');
                    canvas.classList.remove('hidden');
                }).catch(function(err) {
                    console.warn("Failed to render PDF cover thumbnail on index card:", err);
                    // Fail silently, keeps displaying default CSS BPS cover placeholder
                });
            }
        });

        // 2. Real-time Search and Dropdown Filters
        const searchInput = document.getElementById('local-search');
        const categoryFilter = document.getElementById('filter-category');
        const yearFilter = document.getElementById('filter-year');
        const statusFilter = document.getElementById('filter-status');
        const btnReset = document.getElementById('btn-reset-filters');
        const sortOrder = document.getElementById('sort-order');
        const emptyState = document.getElementById('search-empty-state');
        const container = document.getElementById('publications-container');
        const cardElements = document.querySelectorAll('.pub-card');

        function filterAndSort() {
            if (!container) return;

            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const category = categoryFilter ? categoryFilter.value : 'all';
            const year = yearFilter ? yearFilter.value : 'all';
            const status = statusFilter ? statusFilter.value : 'all';
            const sort = sortOrder ? sortOrder.value : 'newest';

            let visibleCount = 0;
            let cardsArray = Array.from(cardElements);

            // Filter cards
            cardsArray.forEach(card => {
                const title = card.getAttribute('data-title');
                const cat = card.getAttribute('data-category');
                const yr = card.getAttribute('data-year');
                const stat = card.getAttribute('data-status');
                const tags = card.getAttribute('data-tags');

                const matchesSearch = title.includes(query) || tags.includes(query);
                const matchesCategory = category === 'all' || cat === category;
                const matchesYear = year === 'all' || yr === year;
                const matchesStatus = status === 'all' || stat === status;

                if (matchesSearch && matchesCategory && matchesYear && matchesStatus) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Sort cards (only of visible ones to be clean, or sort whole array then append)
            cardsArray.sort((a, b) => {
                if (sort === 'newest') {
                    return b.getAttribute('data-date') - a.getAttribute('data-date');
                } else if (sort === 'oldest') {
                    return a.getAttribute('data-date') - b.getAttribute('data-date');
                } else if (sort === 'title-az') {
                    return a.getAttribute('data-title').localeCompare(b.getAttribute('data-title'));
                } else if (sort === 'title-za') {
                    return b.getAttribute('data-title').localeCompare(a.getAttribute('data-title'));
                }
                return 0;
            });

            // Re-append sorted cards to the DOM container
            cardsArray.forEach(card => {
                container.appendChild(card);
            });

            // Handle empty states
            if (visibleCount === 0) {
                if (container) container.classList.add('hidden');
                if (emptyState) emptyState.classList.remove('hidden');
            } else {
                if (container) container.classList.remove('hidden');
                if (emptyState) emptyState.classList.add('hidden');
            }
        }

        // Bind filter event listeners
        if (searchInput) searchInput.addEventListener('input', filterAndSort);
        if (categoryFilter) categoryFilter.addEventListener('change', filterAndSort);
        if (yearFilter) yearFilter.addEventListener('change', filterAndSort);
        if (statusFilter) statusFilter.addEventListener('change', filterAndSort);
        if (sortOrder) sortOrder.addEventListener('change', filterAndSort);
        
        function resetFilters() {
            if (searchInput) searchInput.value = '';
            if (categoryFilter) {
                categoryFilter.value = 'all';
                categoryFilter.dispatchEvent(new Event('change'));
            }
            if (yearFilter) {
                yearFilter.value = 'all';
                yearFilter.dispatchEvent(new Event('change'));
            }
            if (statusFilter) {
                statusFilter.value = 'all';
                statusFilter.dispatchEvent(new Event('change'));
            }
            if (sortOrder) {
                sortOrder.value = 'newest';
                sortOrder.dispatchEvent(new Event('change'));
            }
            filterAndSort();
        }

        if (btnReset) {
            btnReset.addEventListener('click', resetFilters);
        }

        window.resetFilters = resetFilters;

        // 3. Layout Toggle (Grid vs List)
        const btnGrid = document.getElementById('btn-layout-grid');
        const btnList = document.getElementById('btn-layout-list');

        if (btnGrid && btnList && container) {
            btnGrid.addEventListener('click', function() {
                // Set active class styling
                btnGrid.className = "p-1.5 rounded-lg text-bps-primary bg-white shadow-sm transition-all";
                btnList.className = "p-1.5 rounded-lg text-slate-400 hover:text-slate-600 transition-all";

                // Set container grid columns class
                container.className = "grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 transition-all duration-300";
            });

            btnList.addEventListener('click', function() {
                // Set active class styling
                btnList.className = "p-1.5 rounded-lg text-bps-primary bg-white shadow-sm transition-all";
                btnGrid.className = "p-1.5 rounded-lg text-slate-400 hover:text-slate-600 transition-all";

                // Set container list columns class (one-column spans full width)
                container.className = "grid grid-cols-1 gap-4 transition-all duration-300";
            });
        }

        // 4. Delete Modal Functions
        const deleteButtons = document.querySelectorAll('.btn-delete');
        const deleteModal = document.getElementById('delete-modal');
        const deleteBackdrop = document.getElementById('delete-backdrop');
        const deleteCard = document.getElementById('delete-card');
        const deleteForm = document.getElementById('delete-form');
        const deleteTitleSpan = document.getElementById('delete-pub-title');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const actionUrl = this.getAttribute('data-delete-url');
                const publicationTitle = this.getAttribute('data-pub-title');

                if (deleteForm && deleteTitleSpan && deleteModal && deleteBackdrop && deleteCard) {
                    deleteForm.action = actionUrl;
                    deleteTitleSpan.textContent = publicationTitle;

                    deleteModal.classList.remove('hidden');
                    deleteModal.classList.add('flex');

                    setTimeout(() => {
                        deleteBackdrop.classList.remove('opacity-0');
                        deleteBackdrop.classList.add('opacity-100');
                        deleteCard.classList.remove('opacity-0', 'scale-90');
                        deleteCard.classList.add('opacity-100', 'scale-100');
                    }, 50);
                }
            });
        });

        window.closeDeleteModal = function() {
            const modal = document.getElementById('delete-modal');
            const backdrop = document.getElementById('delete-backdrop');
            const card = document.getElementById('delete-card');

            if (backdrop && card && modal) {
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                card.classList.remove('opacity-100', 'scale-100');
                card.classList.add('opacity-0', 'scale-90');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 300);
            }
        };

        // 5. Loading Overlay for Extraction Forms
        const extractionForms = document.querySelectorAll('form[action*="extract"]');
        const loadingOverlay = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');

        extractionForms.forEach(form => {
            form.addEventListener('submit', function() {
                if (loadingOverlay) {
                    loadingOverlay.classList.remove('hidden');
                    loadingOverlay.classList.add('flex');
                }

                const messages = [
                    "Membaca berkas PDF...",
                    "Mengekstrak seluruh teks halaman...",
                    "Menjalankan AI untuk analisis isi...",
                    "Mendeteksi indikator statistik utama...",
                    "Menyusun poin-poin ringkasan...",
                    "Menghubungkan referensi halaman..."
                ];
                let idx = 0;
                setInterval(() => {
                    if (loadingText) {
                        loadingText.textContent = messages[idx % messages.length];
                        idx++;
                    }
                }, 2500);
            });
        });

        // 6. Custom Select Dropdown System
        function initializeCustomSelects() {
            const selects = document.querySelectorAll('select.custom-select');
            selects.forEach(select => {
                if (select.nextElementSibling && select.nextElementSibling.classList.contains('custom-select-wrapper')) {
                    return;
                }

                const options = Array.from(select.options);
                const selectedOption = select.options[select.selectedIndex] || options[0];

                const wrapper = document.createElement('div');
                wrapper.className = 'relative w-full md:w-auto custom-select-wrapper min-w-[140px]';

                const trigger = document.createElement('button');
                trigger.type = 'button';
                trigger.className = 'w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-600 font-bold focus:outline-none focus:border-bps-primary focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all select-none';
                
                const label = document.createElement('span');
                label.className = 'custom-select-label truncate';
                label.textContent = selectedOption.textContent;
                trigger.appendChild(label);

                const icon = document.createElement('i');
                icon.setAttribute('data-lucide', 'chevron-down');
                icon.className = 'w-4 h-4 text-slate-400 transition-transform shrink-0';
                trigger.appendChild(icon);

                const menu = document.createElement('div');
                menu.className = 'absolute z-50 left-0 right-0 mt-2 bg-white rounded-2xl border border-slate-100 shadow-xl py-2 hidden flex-col transition-all transform scale-95 opacity-0 origin-top';
                
                const scrollContainer = document.createElement('div');
                scrollContainer.className = 'max-h-60 overflow-y-auto space-y-0.5 px-1.5';
                
                options.forEach(opt => {
                    const optBtn = document.createElement('button');
                    optBtn.type = 'button';
                    optBtn.className = 'w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center justify-between';
                    optBtn.setAttribute('data-value', opt.value);

                    const textSpan = document.createElement('span');
                    textSpan.textContent = opt.textContent;
                    optBtn.appendChild(textSpan);

                    const checkIcon = document.createElement('i');
                    checkIcon.setAttribute('data-lucide', 'check');
                    checkIcon.className = 'w-3.5 h-3.5 text-bps-primary shrink-0 ml-2';
                    optBtn.appendChild(checkIcon);

                    if (opt.value === select.value) {
                        optBtn.classList.add('bg-blue-50/70', 'text-bps-primary');
                        checkIcon.classList.remove('hidden');
                    } else {
                        optBtn.classList.add('text-slate-600', 'hover:bg-slate-50');
                        checkIcon.classList.add('hidden');
                    }

                    optBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        select.value = opt.value;
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        
                        label.textContent = opt.textContent;

                        Array.from(scrollContainer.children).forEach(child => {
                            const check = child.querySelector('[data-lucide="check"]');
                            if (child.getAttribute('data-value') === opt.value) {
                                child.className = 'w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold bg-blue-50/70 text-bps-primary transition-all flex items-center justify-between';
                                if (check) check.classList.remove('hidden');
                            } else {
                                child.className = 'w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all flex items-center justify-between';
                                if (check) check.classList.add('hidden');
                            }
                        });

                        closeMenu();
                    });

                    scrollContainer.appendChild(optBtn);
                });

                menu.appendChild(scrollContainer);
                wrapper.appendChild(trigger);
                wrapper.appendChild(menu);

                select.classList.add('hidden');
                select.style.display = 'none';
                select.parentNode.insertBefore(wrapper, select.nextSibling);

                let isOpen = false;
                
                function openMenu() {
                    isOpen = true;
                    menu.classList.remove('hidden');
                    menu.classList.add('flex');
                    icon.classList.add('rotate-180');
                    setTimeout(() => {
                        menu.classList.remove('scale-95', 'opacity-0');
                        menu.classList.add('scale-100', 'opacity-100');
                    }, 10);
                }

                function closeMenu() {
                    isOpen = false;
                    icon.classList.remove('rotate-180');
                    menu.classList.remove('scale-100', 'opacity-100');
                    menu.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        if (!isOpen) {
                            menu.classList.remove('flex');
                            menu.classList.add('hidden');
                        }
                    }, 150);
                }

                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                        if (w !== wrapper) {
                            const otherIcon = w.querySelector('.rotate-180');
                            const otherMenu = w.querySelector('.absolute');
                            if (otherMenu && !otherMenu.classList.contains('hidden')) {
                                if (otherIcon) otherIcon.classList.remove('rotate-180');
                                otherMenu.classList.remove('scale-100', 'opacity-100');
                                otherMenu.classList.add('scale-95', 'opacity-0');
                                setTimeout(() => {
                                    otherMenu.classList.remove('flex');
                                    otherMenu.classList.add('hidden');
                                }, 150);
                            }
                        }
                    });

                    if (menu.classList.contains('hidden')) {
                        openMenu();
                    } else {
                        closeMenu();
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!wrapper.contains(e.target)) {
                        closeMenu();
                    }
                });

                // Sync programmatic updates
                select.addEventListener('change', () => {
                    const currentOpt = Array.from(select.options).find(o => o.value === select.value);
                    if (currentOpt) {
                        label.textContent = currentOpt.textContent;
                    }
                    
                    Array.from(scrollContainer.children).forEach(child => {
                        const check = child.querySelector('[data-lucide="check"]');
                        const val = child.getAttribute('data-value');
                        if (val === select.value) {
                            child.className = 'w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold bg-blue-50/70 text-bps-primary transition-all flex items-center justify-between';
                            if (check) check.classList.remove('hidden');
                        } else {
                            child.className = 'w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all flex items-center justify-between';
                            if (check) check.classList.add('hidden');
                        }
                    });
                });
            });

            if (window.lucide) {
                window.lucide.createIcons();
            }
        }

        initializeCustomSelects();

        // Run initial filtering on page load to apply initial values of dropdowns
        filterAndSort();
    });
</script>
@endsection
