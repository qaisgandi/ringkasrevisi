@extends('layouts.admin')

@section('title', 'Detail Publikasi - Sistem Ringkasan & Manajemen Publikasi Statistik BPS')

@section('content')
<!-- Include Lucide Icons CDN -->
<script src="https://unpkg.com/lucide@latest"></script>

<div class="w-full space-y-8 fade-in">

    <!-- HEADER / NAVIGATION BACK -->
    <div class="flex items-center gap-4">
        <a 
            href="{{ route('publications.index') }}" 
            class="p-2 bg-white hover:bg-slate-50 border border-slate-100 rounded-xl text-slate-500 hover:text-slate-700 transition-colors shadow-sm"
            title="Kembali ke Daftar Publikasi"
        >
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="heading-font text-2xl font-extrabold text-bps-navy tracking-tight">Detail Publikasi</h1>
            <p class="text-sm text-slate-400 font-medium mt-0.5">Lihat informasi detail dan analisis AI hasil ekstraksi PDF.</p>
        </div>
    </div>

    <!-- SUCCESS NOTIFICATION -->
    @if (session('success'))
        <div id="success-alert" class="fade-in flex items-center justify-between gap-3 bg-emerald-50 border border-emerald-200/60 text-emerald-800 px-5 py-4 rounded-2xl text-sm shadow-sm transition-all duration-300" role="alert">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <span class="font-bold">Sukses:</span> {{ session('success') }}
                </div>
            </div>
            <button onclick="document.getElementById('success-alert').remove()" class="text-emerald-500 hover:text-emerald-700 hover:bg-emerald-100/50 transition-colors p-1 rounded-lg" title="Tutup Notifikasi">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- AI PROCESSING / CORRECTION NOTIFICATION -->
    @if (empty($publication->extracted_text) || strlen($publication->extracted_text) < 10)
        <div id="ai-processing-notice" class="fade-in flex items-center justify-between gap-3 bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-2xl text-sm shadow-sm transition-all duration-300 animate-pulse" role="alert">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-500 animate-spin shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div>
                    <span class="font-bold">Mengoptimalkan Analisis AI:</span> Dokumen terdeteksi terlindungi. Sistem sedang mengekstrak teks melalui browser secara aman untuk menyelaraskan ringkasan, topik, dan indikator secara presisi... <span id="extraction-progress" class="font-bold">0%</span>
                </div>
            </div>
        </div>
    @endif

    <!-- MAIN CARD: HEADER INFO -->
    <div class="bg-white rounded-xl shadow-md border border-slate-100 p-6 md:p-8">
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <!-- COLUMN 1: BOOK COVER THUMBNAIL (PDF OR CSS FALLBACK) -->
            <div class="w-full sm:w-56 h-80 shrink-0 rounded-2xl shadow-lg relative overflow-hidden border border-slate-200 bg-slate-50 select-none group">
                <!-- PDF Canvas Cover (rendered via PDF.js) -->
                <canvas id="pdf-cover-canvas" class="w-full h-full object-contain hidden rounded-2xl"></canvas>

                <!-- CSS Fallback Placeholder (shown while loading or if PDF.js fails) -->
                <div id="pdf-cover-placeholder" class="w-full h-full bg-gradient-to-br from-bps-navy to-blue-900 flex flex-col justify-between p-5 text-white relative">
                    <!-- Decorative Top Graphic -->
                    <div class="absolute -top-10 -right-10 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-bps-orange"></div>
                    
                    <!-- BPS Top Banner -->
                    <div class="flex items-center justify-between z-10">
                        <span class="text-[9px] font-black tracking-widest text-bps-orange uppercase">Badan Pusat Statistik</span>
                        <i data-lucide="book-open" class="w-4 h-4 text-white/80"></i>
                    </div>

                    <!-- Book Middle Contents -->
                    <div class="space-y-2 z-10">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-white/10 text-[9px] font-bold text-bps-orange border border-white/15">
                            {{ $publication->category }}
                        </span>
                        <h3 class="font-extrabold text-sm line-clamp-4 leading-snug tracking-tight heading-font">
                            {{ $publication->title }}
                        </h3>
                    </div>

                    <!-- Book Footer Contents -->
                    <div class="border-t border-white/10 pt-3 flex items-center justify-between z-10 text-[10px] text-white/70 font-semibold">
                        <span>Tahun {{ $publication->year }}</span>
                        <span class="text-right truncate max-w-32">{{ $publication->region ?? 'Kota Tasikmalaya' }}</span>
                    </div>

                    <!-- Spine Overlay Effect -->
                    <div class="absolute inset-y-0 left-0 w-3 bg-gradient-to-r from-black/35 via-white/5 to-transparent"></div>
                </div>
            </div>

            <!-- COLUMN 2: TITLE & ACTION BUTTONS -->
            <div class="flex-1 space-y-5 w-full">
                <div class="space-y-2">
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-bps-primary border border-blue-100 text-xs font-bold select-none">
                            <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                            {{ $publication->category }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-bps-orange border border-amber-100 text-xs font-bold select-none">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                            {{ $publication->region ?? 'Kota Tasikmalaya' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold select-none">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            Tahun {{ $publication->year }}
                        </span>
                    </div>

                    <h2 class="heading-font text-2xl font-black text-bps-navy leading-snug tracking-tight">
                        {{ $publication->title }}
                    </h2>
                </div>

                <div class="h-px bg-slate-100"></div>

                <!-- Action Button Grid -->
                <div class="flex flex-wrap gap-3">
                    <a 
                        href="/storage/{{ $publication->pdf_path }}" 
                        target="_blank" 
                        class="inline-flex items-center gap-2 px-5 py-3 bg-bps-primary hover:bg-blue-700 text-white rounded-2xl font-semibold shadow-lg shadow-blue-500/10 hover:shadow-blue-500/25 transition-all duration-200 text-sm hover:-translate-y-0.5 active:translate-y-0"
                    >
                        <i data-lucide="eye" class="w-4 h-4"></i>
                        <span>Lihat PDF</span>
                    </a>
                    
                    <a 
                        href="/storage/{{ $publication->pdf_path }}" 
                        download="{{ basename($publication->pdf_path) }}"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-2xl font-semibold transition-all duration-200 text-sm hover:-translate-y-0.5 active:translate-y-0"
                    >
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Download PDF</span>
                    </a>
                    
                    <a 
                        href="{{ route('publications.edit', $publication) }}"
                        class="inline-flex items-center gap-2 px-4 py-3 bg-white hover:bg-slate-50 text-slate-500 border border-slate-200 rounded-2xl font-semibold transition-all duration-200 text-sm"
                        title="Edit Metadata"
                    >
                        <i data-lucide="edit" class="w-4 h-4"></i>
                        <span>Edit</span>
                    </a>

                    <form action="{{ route('publications.extract', $publication) }}" method="POST" class="inline-flex" onsubmit="return confirmRegeneration(this);">
                        @csrf
                        <button 
                            type="submit"
                            class="inline-flex items-center gap-2 px-5 py-3 bg-bps-orange hover:bg-orange-600 text-white rounded-2xl font-semibold shadow-lg shadow-orange-500/10 hover:shadow-orange-500/25 transition-all duration-200 text-sm hover:-translate-y-0.5 active:translate-y-0"
                            title="Generate ulang ringkasan dan analisis menggunakan AI"
                        >
                            <i data-lucide="sparkles" class="w-4 h-4 loading-icon"></i>
                            <span>Generate Ulang AI</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- 9-SECTION AI RESULT DETAILS -->
    @php
        $aiResult = $publication->aiResult;
        
        // Normalization / Fallback for existing publications
        $summary = $aiResult ? $aiResult->summary : $publication->summary;
        $topics = $aiResult ? $aiResult->topics : $publication->topics;
        $keywords = $aiResult ? $aiResult->keywords : $publication->keywords;
        $keyPoints = $aiResult ? $aiResult->key_points : $publication->key_points;
        $indicators = $aiResult ? $aiResult->indicators : $publication->indicators;
        $trends = $aiResult ? $aiResult->trends : $publication->trends;
        $discussionLocations = $aiResult ? $aiResult->discussion_locations : $publication->page_locations;
        $conclusion = $aiResult ? $aiResult->conclusion : $publication->conclusion;

        // 1. Normalize summary paragraphs
        $summaryParagraphs = [];
        if (!empty($summary)) {
            $summaryParagraphs = explode("\n\n", $summary);
        }

        // 2. Normalize publication information metadata
        $info = $aiResult ? $aiResult->publication_information : null;
        $infoTitle = $info['title'] ?? $publication->title;
        $infoYear = $info['year'] ?? $publication->year;
        $infoRegion = $info['region'] ?? ($publication->region ?: 'Data tidak tersedia pada publikasi ini.');
        $infoCategory = $info['category'] ?? $publication->category;
        $infoPageCount = isset($info['page_count']) ? $info['page_count'] : ($publication->page_count ? $publication->page_count . ' Halaman' : 'Data tidak tersedia pada publikasi ini.');
        $infoFileSize = $info['file_size'] ?? ($publication->file_size ?: 'Data tidak tersedia pada publikasi ini.');
        $infoUploadDate = $info['upload_date'] ?? $publication->created_at->format('d-m-Y H:i');

        // 3. Normalize indicators format
        $normalizedIndicators = [];
        if (!empty($indicators) && is_array($indicators)) {
            foreach ($indicators as $ind) {
                if (is_array($ind)) {
                    $unit = $ind['unit'] ?? '';
                    if (strtolower($unit) === 'persen' || $unit === '%') {
                        $unit = '%';
                    } elseif (strtolower($unit) === 'poin') {
                        $unit = 'poin';
                    }
                    $normalizedIndicators[] = [
                        'name' => $ind['name'] ?? ($ind['indicator'] ?? ''),
                        'value' => $ind['value'] ?? '',
                        'unit' => $unit
                    ];
                } elseif (is_string($ind)) {
                    // Attempt to parse string e.g. "IPM sebesar 76,03 persen"
                    $pattern = '/^(.*?)\s+(?:sebesar|mencapai|yaitu|adalah)\s+([\d\.,]+)\s*(.*)$/i';
                    if (preg_match($pattern, $ind, $m)) {
                        $unit = trim($m[3]);
                        if (strtolower($unit) === 'persen' || $unit === '%') {
                            $unit = '%';
                        } elseif (strtolower($unit) === 'poin') {
                            $unit = 'poin';
                        }
                        $normalizedIndicators[] = [
                            'name' => trim($m[1]),
                            'value' => trim($m[2]),
                            'unit' => $unit
                        ];
                    } else {
                        $normalizedIndicators[] = [
                            'name' => $ind,
                            'value' => '-',
                            'unit' => '-'
                        ];
                    }
                }
            }
        }

        // 4. Normalize trends format
        $normalizedTrends = [];
        if (!empty($trends) && is_array($trends)) {
            foreach ($trends as $tr) {
                if (is_array($tr)) {
                    $normalizedTrends[] = [
                        'indicator' => $tr['indicator'] ?? ($tr['name'] ?? ''),
                        'trend' => $tr['trend'] ?? 'Stabil',
                        'icon' => $tr['icon'] ?? '➖'
                    ];
                } elseif (is_string($tr)) {
                    $trendText = 'Stabil';
                    $trendIcon = '➖';
                    if (preg_match('/(meningkat|naik|tumbuh|bertambah|tinggi)/i', $tr)) {
                        $trendText = 'Meningkat';
                        $trendIcon = '📈';
                    } elseif (preg_match('/(menurun|turun|menyusut|berkurang|rendah)/i', $tr)) {
                        $trendText = 'Menurun';
                        $trendIcon = '📉';
                    }
                    
                    $indicatorName = preg_replace('/\s*(meningkat|naik|tumbuh|bertambah|tinggi|menurun|turun|menyusut|berkurang|rendah|stabil)\b/i', '', $tr);
                    $normalizedTrends[] = [
                        'indicator' => trim($indicatorName),
                        'trend' => $trendText,
                        'icon' => $trendIcon
                    ];
                }
            }
        }

        // 5. Normalize discussion locations (page locations)
        $normalizedLocations = [];
        if (!empty($discussionLocations) && is_array($discussionLocations)) {
            foreach ($discussionLocations as $loc) {
                if (is_array($loc)) {
                    $normalizedLocations[] = [
                        'topic' => $loc['topic'] ?? ($loc['name'] ?? ''),
                        'page' => $loc['page'] ?? 0
                    ];
                }
            }
        }
    @endphp

    <div class="w-full space-y-6">
        
        <!-- Row 1: 1. Ringkasan Publikasi (Full Width) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 space-y-4">
            <h3 class="heading-font text-lg font-extrabold text-bps-navy flex items-center gap-2 border-b border-slate-50 pb-3">
                <i data-lucide="sparkles" class="w-5 h-5 text-bps-orange animate-pulse"></i>
                <span>1. Ringkasan Publikasi</span>
            </h3>
            @if (!empty($summaryParagraphs))
                <div class="text-slate-600 text-sm leading-relaxed space-y-4 font-semibold">
                    @foreach ($summaryParagraphs as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-400 italic">Data tidak tersedia pada publikasi ini.</p>
            @endif
        </div>

        <!-- Row 2: 2. Informasi Publikasi & 3. Topik & 4. Kata Kunci (Side-by-Side) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Left: 2. Informasi Publikasi -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 space-y-4 flex flex-col justify-between">
                <div class="space-y-4">
                    <h3 class="heading-font text-base font-extrabold text-bps-navy flex items-center gap-2 border-b border-slate-50 pb-3">
                        <i data-lucide="info" class="w-4 h-4 text-bps-primary"></i>
                        <span>2. Informasi Publikasi</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-semibold text-slate-600">
                        <div class="flex flex-col gap-0.5 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                            <span class="text-slate-400 text-[9px] font-bold uppercase tracking-wider">Tahun</span>
                            <span class="font-bold text-slate-700">{{ $infoYear }}</span>
                        </div>
                        <div class="flex flex-col gap-0.5 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                            <span class="text-slate-400 text-[9px] font-bold uppercase tracking-wider">Kategori</span>
                            <span class="font-bold text-slate-700 truncate" title="{{ $infoCategory }}">{{ $infoCategory }}</span>
                        </div>
                        <div class="flex flex-col gap-0.5 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                            <span class="text-slate-400 text-[9px] font-bold uppercase tracking-wider">Halaman</span>
                            <span class="font-bold text-slate-700">
                                {{ is_numeric($infoPageCount) ? $infoPageCount . ' Hlm' : $infoPageCount }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-0.5 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                            <span class="text-slate-400 text-[9px] font-bold uppercase tracking-wider">Ukuran File</span>
                            <span class="font-bold text-slate-700">{{ $infoFileSize }}</span>
                        </div>
                        <div class="flex flex-col gap-0.5 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                            <span class="text-slate-400 text-[9px] font-bold uppercase tracking-wider">Upload</span>
                            <span class="font-bold text-slate-700 truncate" title="{{ $infoUploadDate }}">{{ explode(' ', $infoUploadDate)[0] }}</span>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100/60 mt-3 text-[10px] text-slate-400 font-semibold truncate">
                    <span class="font-bold text-slate-500">Judul:</span> {{ $infoTitle }}
                </div>
            </div>

            <!-- Right: 3. Topik & 4. Kata Kunci -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 space-y-5 flex flex-col justify-between">
                <!-- Topik -->
                <div class="space-y-3">
                    <h3 class="heading-font text-base font-extrabold text-bps-navy flex items-center gap-2 border-b border-slate-50 pb-2">
                        <i data-lucide="hash" class="w-4 h-4 text-bps-orange"></i>
                        <span>3. Topik Pembahasan</span>
                    </h3>
                    @if (!empty($topics) && is_array($topics))
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($topics as $topic)
                                <span class="px-2.5 py-1 bg-blue-50/50 text-bps-primary rounded-lg text-xs font-bold border border-blue-100/40 shadow-sm">
                                    # {{ $topic }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Data tidak tersedia.</p>
                    @endif
                </div>

                <!-- Kata Kunci -->
                <div class="space-y-3 border-t border-slate-50 pt-3">
                    <h3 class="heading-font text-base font-extrabold text-bps-navy flex items-center gap-2 border-b border-slate-50 pb-2">
                        <i data-lucide="key-round" class="w-4 h-4 text-bps-primary"></i>
                        <span>4. Kata Kunci</span>
                    </h3>
                    @if (!empty($keywords) && is_array($keywords))
                        <div class="flex flex-wrap gap-1">
                            @foreach ($keywords as $keyword)
                                <span class="px-2 py-0.5 bg-slate-50 text-slate-600 rounded-md text-[10px] font-semibold border border-slate-200">
                                    {{ $keyword }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Data tidak tersedia.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Row 3: 5. Poin-Poin Penting (Full Width) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 space-y-4">
            <h3 class="heading-font text-lg font-extrabold text-bps-navy flex items-center gap-2 border-b border-slate-50 pb-3">
                <i data-lucide="list-checks" class="w-5 h-5 text-bps-primary"></i>
                <span>5. Poin-Poin Penting</span>
            </h3>
            @if (!empty($keyPoints) && is_array($keyPoints))
                <ul class="space-y-3.5 text-sm text-slate-600 font-semibold">
                    @foreach ($keyPoints as $point)
                        <li class="flex items-start gap-3 bg-slate-50/30 p-3.5 rounded-xl border border-slate-100/50">
                            <span class="w-5 h-5 shrink-0 rounded-full bg-blue-50 text-bps-primary flex items-center justify-center text-[10px] font-bold mt-0.5 shadow-sm">
                                {{ $loop->iteration }}
                            </span>
                            <span class="leading-relaxed">{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-slate-400 italic">Data tidak tersedia pada publikasi ini.</p>
            @endif
        </div>

        <!-- Row 4: 6. Indikator Utama & 7. Tren Utama (Side-by-Side) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Left: 6. Indikator Utama -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 space-y-4">
                <h3 class="heading-font text-base font-extrabold text-bps-navy flex items-center gap-2 border-b border-slate-50 pb-3">
                    <i data-lucide="database" class="w-4 h-4 text-bps-primary"></i>
                    <span>6. Indikator Utama</span>
                </h3>
                @if (!empty($normalizedIndicators))
                    <div class="overflow-x-auto rounded-xl border border-slate-100">
                        <table class="w-full text-xs text-left text-slate-600">
                            <thead class="bg-slate-50 text-[10px] font-bold text-bps-navy uppercase tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="px-4 py-3">Indikator</th>
                                    <th class="px-4 py-3 text-center">Nilai</th>
                                    <th class="px-4 py-3">Satuan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 font-semibold">
                                @foreach ($normalizedIndicators as $ind)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-4 py-2.5 font-bold text-slate-700">{{ $ind['name'] }}</td>
                                        <td class="px-4 py-2.5 text-center font-extrabold text-bps-primary">{{ $ind['value'] }}</td>
                                        <td class="px-4 py-2.5 text-slate-500 font-semibold">{{ $ind['unit'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic">Data tidak tersedia pada publikasi ini.</p>
                @endif
            </div>

            <!-- Right: 7. Tren Utama -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 space-y-4">
                <h3 class="heading-font text-base font-extrabold text-bps-navy flex items-center gap-2 border-b border-slate-50 pb-3">
                    <i data-lucide="trending-up" class="w-4 h-4 text-bps-primary"></i>
                    <span>7. Tren Utama</span>
                </h3>
                @if (!empty($normalizedTrends))
                    <div class="space-y-3 text-xs font-semibold">
                        @foreach ($normalizedTrends as $trend)
                            <div class="flex items-center justify-between bg-slate-50/60 p-3 rounded-xl border border-slate-100 shadow-sm hover:shadow transition-all duration-200">
                                <span class="text-slate-700 truncate max-w-40" title="{{ $trend['indicator'] }}">{{ $trend['indicator'] }}</span>
                                @php
                                    $badgeBg = 'bg-slate-100 text-slate-600 border-slate-200';
                                    if ($trend['trend'] === 'Meningkat') {
                                        $badgeBg = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                                    } elseif ($trend['trend'] === 'Menurun') {
                                        $badgeBg = 'bg-rose-50 text-rose-600 border-rose-100';
                                    }
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full border {{ $badgeBg }} text-[10px] font-bold">
                                    <span>{{ $trend['icon'] }}</span>
                                    <span>{{ $trend['trend'] }}</span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic">Data tidak tersedia pada publikasi ini.</p>
                @endif
            </div>
        </div>

        <!-- Row 5: 8. Lokasi Pembahasan & 9. Kesimpulan (Side-by-Side) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Left: 8. Lokasi Pembahasan -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 space-y-4">
                <h3 class="heading-font text-base font-extrabold text-bps-navy flex items-center gap-2 border-b border-slate-50 pb-3">
                    <i data-lucide="map" class="w-4 h-4 text-bps-primary"></i>
                    <span>8. Lokasi Pembahasan</span>
                </h3>
                @if (!empty($normalizedLocations) && count($normalizedLocations) > 0)
                    <div class="space-y-3 max-h-[220px] overflow-y-auto pr-1">
                        @foreach ($normalizedLocations as $loc)
                            <div class="flex items-end text-xs font-semibold">
                                <span class="text-slate-700 bg-white pr-2 shrink-0">{{ $loc['topic'] }}</span>
                                <span class="border-b border-dashed border-slate-300 grow mb-1"></span>
                                <span class="text-slate-500 bg-white pl-2 shrink-0">Halaman {{ $loc['page'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic">Data tidak tersedia pada publikasi ini.</p>
                @endif
            </div>

            <!-- Right: 9. Kesimpulan -->
            <div class="bg-gradient-to-br from-blue-50/50 to-indigo-50/30 rounded-2xl border border-blue-100/40 p-6 md:p-8 space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <h3 class="heading-font text-base font-extrabold text-bps-navy flex items-center gap-2 border-b border-blue-100/50 pb-2">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-bps-primary"></i>
                        <span>9. Kesimpulan</span>
                    </h3>
                    @if (!empty($conclusion))
                        <p class="text-slate-600 text-xs leading-relaxed font-semibold italic">
                            "{{ $conclusion }}"
                        </p>
                    @else
                        <p class="text-xs text-slate-400 italic">Data tidak tersedia pada publikasi ini.</p>
                    @endif
                </div>
                <div class="flex justify-end pt-4 border-t border-blue-100/20 text-[9px] text-slate-400 font-bold tracking-wider uppercase select-none">
                    ikhtisar eksekutif AI
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Initialize Lucide Icons & PDF.js Cover Rendering -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // PDF.js Document Loading (Single pass for thumbnail cover + text extraction)
        const pdfUrl = '/storage/{{ $publication->pdf_path }}';
        const canvas = document.getElementById('pdf-cover-canvas');
        const placeholder = document.getElementById('pdf-cover-placeholder');

        if (pdfUrl) {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

            const loadingTask = pdfjsLib.getDocument(pdfUrl);

            // Abort active PDF network request on page unload/navigation to prevent browser connection saturation
            window.addEventListener('beforeunload', function() {
                if (loadingTask && typeof loadingTask.destroy === 'function') {
                    loadingTask.destroy();
                }
            });

            loadingTask.promise.then(function(pdf) {
                // 1. Render Cover Page if Canvas exists
                if (canvas && placeholder) {
                    pdf.getPage(1).then(function(page) {
                        const viewport = page.getViewport({ scale: 1.5 });
                        const ctx = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        const renderContext = {
                            canvasContext: ctx,
                            viewport: viewport
                        };

                        page.render(renderContext).promise.then(function() {
                            placeholder.classList.add('hidden');
                            canvas.classList.remove('hidden');
                        });
                    }).catch(function(err) {
                        console.error("Failed to render PDF cover thumbnail:", err);
                    });
                }

                // 2. Client-side PDF text extraction fallback for locked/secured PDFs
                @if (empty($publication->extracted_text) || strlen($publication->extracted_text) < 10)
                const totalPages = pdf.numPages;
                const pagesToParse = Math.min(totalPages, 8);
                let pagesData = {};
                let parsedCount = 0;
                const progressEl = document.getElementById('extraction-progress');

                const updateProgress = () => {
                    const percent = Math.round((parsedCount / pagesToParse) * 100);
                    if (progressEl) progressEl.innerText = percent + '%';
                };

                updateProgress();

                for (let i = 1; i <= pagesToParse; i++) {
                    (function(pageNum) {
                        pdf.getPage(pageNum).then(function(page) {
                            page.getTextContent().then(function(textContent) {
                                let pageText = textContent.items.map(item => item.str).join(' ');
                                pagesData[pageNum] = pageText;
                                parsedCount++;
                                updateProgress();

                                if (parsedCount === pagesToParse) {
                                    // All pages extracted! Combine text.
                                    let combinedText = "";
                                    for (let p = 1; p <= pagesToParse; p++) {
                                        combinedText += (pagesData[p] || "") + " ";
                                    }

                                    // Get human readable file size
                                    const bytes = {{ file_exists(storage_path('app/public/' . $publication->pdf_path)) ? filesize(storage_path('app/public/' . $publication->pdf_path)) : 0 }};
                                    let fileSizeStr = bytes + ' B';
                                    if (bytes >= 1048576) {
                                        fileSizeStr = (bytes / 1048576).toFixed(2) + ' MB';
                                    } else if (bytes >= 1024) {
                                        fileSizeStr = (bytes / 1024).toFixed(2) + ' KB';
                                    }

                                    // Send to server via fetch
                                    fetch('{{ route('publications.saveExtractedText', $publication, false) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            text: combinedText,
                                            pages: pagesData,
                                            page_count: totalPages,
                                            file_size: fileSizeStr
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            window.location.reload();
                                        } else {
                                            console.error("Server-side save failed:", data.message);
                                        }
                                    })
                                    .catch(err => {
                                        console.error("Network error during save:", err);
                                    });
                                }
                            }).catch(function(err) {
                                console.error("Text content extraction failed for page:", pageNum, err);
                                parsedCount++;
                                updateProgress();
                            });
                        }).catch(function(err) {
                            console.error("Page get failed:", pageNum, err);
                            parsedCount++;
                            updateProgress();
                        });
                    })(i);
                }
                @endif
            });
        }
    });

    function confirmRegeneration(form) {
        if (confirm('Apakah Anda yakin ingin men-generate ulang analisis AI untuk publikasi ini? Data analisis lama akan diperbarui.')) {
            const btn = form.querySelector('button');
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.7';
            btn.querySelector('span').innerText = 'Memproses AI...';
            const icon = btn.querySelector('.loading-icon') || btn.querySelector('svg');
            if (icon) {
                icon.classList.add('animate-spin');
            }
            return true;
        }
        return false;
    }
</script>
@endsection
