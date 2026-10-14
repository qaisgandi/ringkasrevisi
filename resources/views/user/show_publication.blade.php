@extends('layouts.user')

@php
    // Determine cover colors and icon based on official category name
    $catLower = strtolower($publication->category);
    $coverGradient = 'from-blue-600 to-indigo-700'; // Default
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
    $infoRegion = $info['region'] ?? ($publication->region ?: 'Nasional');
    $infoCategory = $info['category'] ?? $publication->category;
    $infoPageCount = isset($info['page_count']) ? $info['page_count'] : ($publication->page_count ? $publication->page_count . ' Halaman' : 'Data tidak tersedia');
    $infoFileSize = $info['file_size'] ?? ($publication->file_size ?: 'Data tidak tersedia');
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

@section('title', $publication->title)

@section('content')
    <div class="max-w-7xl w-full mx-auto px-4 py-8 md:py-12 space-y-8">
        
        <!-- Top Navigation / Breadcrumbs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <a href="{{ route('home') }}" class="hover:text-bps-lightBlue transition-colors">Beranda</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    <a href="{{ route('home', ['category' => $publication->category]) }}" class="hover:text-bps-lightBlue transition-colors">{{ $publication->category }}</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    <span class="text-slate-600 font-bold truncate max-w-[200px] sm:max-w-[400px]">{{ $publication->title }}</span>
                </div>
            </div>
            
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-bps-lightBlue transition-colors bg-slate-50 hover:bg-slate-100 px-3.5 py-2 rounded-xl border border-slate-200 shadow-sm self-start">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

        <!-- Main Content Columns -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12 items-start">
            
            <!-- Left Column: Cover & Details (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Physical Book Cover Shadow Container -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col items-center text-center space-y-6 relative overflow-hidden">
                    <!-- Background decor blob -->
                    <div class="absolute -right-16 -top-16 w-32 h-32 rounded-full bg-blue-50/40 blur-xl pointer-events-none"></div>

                    <!-- Book Cover Container -->
                    <div class="w-40 h-52 shrink-0 rounded-xl shadow-xl relative overflow-hidden border border-slate-200 bg-slate-50 select-none group">
                        <!-- PDF Canvas Cover (rendered via PDF.js) -->
                        <canvas id="pdf-cover-canvas" class="w-full h-full object-contain hidden rounded-xl"></canvas>

                        <!-- CSS Fallback Placeholder (shown while loading or if PDF.js fails) -->
                        <div id="pdf-cover-placeholder" class="relative w-full h-full bg-gradient-to-br {{ $coverGradient }} text-white p-4 flex flex-col justify-between border-r-4 border-black/10 overflow-hidden shrink-0 transform hover:scale-[1.03] transition-transform duration-300">
                            <!-- Spine shadow effect -->
                            <div class="absolute left-0 top-0 bottom-0 w-2.5 bg-white/5 border-r border-white/10 rounded-l-xl"></div>
                            
                            <!-- Top Code -->
                            <div class="flex items-center gap-1 pl-1">
                                <div class="w-1.5 h-1.5 bg-bps-orange rounded-full"></div>
                                <span class="text-[8px] font-black tracking-widest text-slate-200 uppercase">{{ substr($publication->category, 0, 15) }}</span>
                            </div>

                            <!-- Title -->
                            <div class="pl-1 text-left">
                                <h4 class="font-extrabold text-[9px] tracking-wide leading-tight line-clamp-4 uppercase text-left">{{ $publication->title }}</h4>
                                <span class="text-[8px] text-bps-orange font-black block mt-1 text-left">{{ $publication->year }}</span>
                            </div>

                            <!-- Footer -->
                            <div class="pl-1 border-t border-white/5 pt-1.5 flex items-center justify-between text-[7px] font-bold text-slate-300">
                                <span>BPS INDONESIA</span>
                                <i data-lucide="{{ $coverIcon }}" class="w-3 h-3 text-bps-orange"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Simple Metadata grid -->
                    <div class="w-full divide-y divide-slate-100 text-xs font-semibold text-slate-600">
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-400">Kategori</span>
                            <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-md uppercase border {{ $badgeStyle }}">{{ $publication->category }}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-400">Tahun</span>
                            <span>{{ $publication->year }}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-400">Wilayah</span>
                            <span>{{ $publication->region ?: 'Nasional' }}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-400">Tanggal Rilis</span>
                            <span>{{ $publication->release_date ? date('d-m-Y', strtotime($publication->release_date)) : '-' }}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-400">Status</span>
                            @if ($publication->status === 'Selesai')
                                <span class="px-2.5 py-0.5 bg-emerald-50 text-[10px] font-bold text-bps-green border border-emerald-100 rounded-full flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 bg-slate-50 text-[10px] font-bold text-slate-400 border border-slate-200 rounded-full flex items-center gap-1 select-none">
                                    <i data-lucide="loader" class="w-3 h-3 animate-spin text-slate-400"></i> Diproses AI
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- PDF Download Button -->
                    @if ($publication->pdf_path)
                        <a 
                            href="{{ Storage::url($publication->pdf_path) }}" 
                            download
                            class="w-full py-3 bg-bps-lightBlue hover:bg-blue-700 text-white rounded-2xl font-bold text-sm shadow-md shadow-blue-500/15 hover:shadow-lg hover:shadow-blue-500/25 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i data-lucide="download" class="w-4 h-4"></i>
                            <span>Download PDF Asli</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right Column: Summary Panels (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Book Title header -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wide rounded-md border {{ $badgeStyle }}">
                            {{ $publication->category }}
                        </span>
                        <span class="text-xs text-slate-400 font-bold">Rilis: {{ $publication->year }}</span>
                    </div>
                    <h2 class="heading-font text-xl md:text-2xl font-black text-bps-navy tracking-tight leading-snug">
                        {{ $publication->title }}
                    </h2>
                </div>

                @if ($publication->status === 'Selesai' && ($summary || $aiResult))
                    <!-- AI RESULT DETAILS IN AN EXECUTIVE DASHBOARD LAYOUT -->
                    <div class="space-y-6">
                        
                        <!-- Row 1: 1. Ringkasan Publikasi (Full Width) -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8 space-y-4">
                            <h3 class="heading-font text-lg font-extrabold text-bps-navy flex items-center gap-2 border-b border-slate-50 pb-3">
                                <i data-lucide="sparkles" class="w-5 h-5 text-bps-orange animate-pulse"></i>
                                <span>1. Ringkasan Publikasi</span>
                            </h3>
                            @if (!empty($summaryParagraphs))
                                <div class="text-slate-600 text-sm leading-relaxed space-y-4 font-medium">
                                    @foreach ($summaryParagraphs as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-slate-400 italic">Data ringkasan tidak tersedia pada publikasi ini.</p>
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
                                        <div class="flex flex-col gap-0.5 bg-slate-50/50 p-3 rounded-xl border border-slate-100 sm:col-span-2">
                                            <span class="text-slate-400 text-[9px] font-bold uppercase tracking-wider">Wilayah</span>
                                            <span class="font-bold text-slate-700">{{ $infoRegion }}</span>
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
                                        <p class="text-xs text-slate-400 italic">Data topik tidak tersedia.</p>
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
                                        <p class="text-xs text-slate-400 italic">Data kata kunci tidak tersedia.</p>
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
                                <ul class="space-y-3 text-sm text-slate-600 font-medium">
                                    @foreach ($keyPoints as $point)
                                        <li class="flex items-start gap-3 bg-slate-50/40 p-3.5 rounded-xl border border-slate-100/60">
                                            <span class="w-5 h-5 shrink-0 rounded-full bg-blue-50 text-bps-primary flex items-center justify-center text-[10px] font-bold mt-0.5 shadow-sm">
                                                {{ $loop->iteration }}
                                            </span>
                                            <span class="leading-relaxed">{{ $point }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-slate-400 italic">Data poin penting tidak tersedia pada publikasi ini.</p>
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
                                    <p class="text-sm text-slate-400 italic">Data indikator tidak tersedia pada publikasi ini.</p>
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
                                                    $trendBg = 'bg-slate-100 text-slate-600 border-slate-200';
                                                    if ($trend['trend'] === 'Meningkat') {
                                                        $trendBg = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                                                    } elseif ($trend['trend'] === 'Menurun') {
                                                        $trendBg = 'bg-rose-50 text-rose-600 border-rose-100';
                                                    }
                                                @endphp
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full border {{ $trendBg }} text-[10px] font-bold">
                                                    <span>{{ $trend['icon'] }}</span>
                                                    <span>{{ $trend['trend'] }}</span>
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-slate-400 italic">Data tren tidak tersedia pada publikasi ini.</p>
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
                                    <p class="text-sm text-slate-400 italic">Data lokasi pembahasan tidak tersedia pada publikasi ini.</p>
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
                                        <p class="text-xs text-slate-400 italic">Data kesimpulan belum tersedia.</p>
                                    @endif
                                </div>
                                <div class="flex justify-end pt-4 border-t border-blue-100/20 text-[9px] text-slate-400 font-bold tracking-wider uppercase select-none">
                                    ikhtisar eksekutif AI
                                </div>
                            </div>
                        </div>

                    </div>
                @else
                    <!-- Summary Processing State or General Description -->
                    <div class="bg-white rounded-3xl border border-slate-100 p-10 text-center flex flex-col items-center justify-center space-y-4 shadow-sm">
                        <div class="w-14 h-14 bg-blue-50 text-bps-lightBlue rounded-2xl flex items-center justify-center">
                            <i data-lucide="file-text" class="w-7 h-7"></i>
                        </div>
                        <div class="space-y-2 max-w-lg mx-auto">
                            <h3 class="heading-font text-base font-bold text-bps-navy">Informasi Publikasi</h3>
                            <p class="text-xs text-slate-500 leading-relaxed font-medium">
                                Publikasi ini merupakan dokumen resmi Badan Pusat Statistik dalam kategori <strong>{{ $publication->category }}</strong> untuk tahun rilis <strong>{{ $publication->year }}</strong>. Anda dapat mengunduh dokumen PDF lengkap melalui tombol unduh di panel samping.
                            </p>
                        </div>
                    </div>
                @endif
            </div> <!-- Close Right Column -->
        </div> <!-- Close Grid -->
    </div> <!-- Close Outer Div -->

    <!-- Include PDF.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();

            // PDF.js Cover Page Rendering
            const canvas = document.getElementById('pdf-cover-canvas');
            const placeholder = document.getElementById('pdf-cover-placeholder');
            const pdfUrl = '{{ Storage::url($publication->pdf_path) }}';

            if (pdfUrl) {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

                const loadingTask = pdfjsLib.getDocument(pdfUrl);

                window.addEventListener('beforeunload', function() {
                    if (loadingTask && typeof loadingTask.destroy === 'function') {
                        loadingTask.destroy();
                    }
                });

                loadingTask.promise.then(function(pdf) {
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
                            console.warn("Failed to render PDF cover thumbnail:", err);
                        });
                    }
                }).catch(function(err) {
                    console.warn("PDF.js load task failed:", err);
                });
            }
        });
    </script>
@endsection
