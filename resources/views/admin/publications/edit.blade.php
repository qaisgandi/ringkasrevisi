@extends('layouts.admin')

@section('title', 'Edit Publikasi - Sistem Ringkasan & Manajemen Publikasi Statistik BPS')

@section('content')
<!-- Load PDF.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
</script>

@php
    $filePath = storage_path('app/public/' . $publication->pdf_path);
    $fileSizeInMB = 0;
    if (file_exists($filePath)) {
        $fileSizeInMB = round(filesize($filePath) / (1024 * 1024), 2);
    }
@endphp
<div class="w-full space-y-6">
    
    <!-- HEADER & BACK BUTTON -->
    <div class="flex items-center gap-4">
        <a 
            href="{{ route('publications.index') }}" 
            class="p-2 bg-white hover:bg-slate-50 border border-slate-100 rounded-xl text-slate-500 hover:text-slate-700 transition-colors shadow-sm"
            title="Kembali ke Daftar Publikasi"
        >
            <!-- Arrow Left Icon -->
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="heading-font text-2xl font-extrabold text-bps-navy tracking-tight">Edit Publikasi</h1>
            <p class="text-sm text-slate-400 font-medium mt-0.5">Ubah berkas publikasi atau metadata yang sudah terdaftar.</p>
        </div>
    </div>

    <!-- MAIN FORM CARD -->
    <div class="bg-white rounded-xl shadow-md border border-slate-100 p-8">
        
        <form action="{{ route('publications.update', $publication) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            @if ($errors->any())
                <div id="error-alert" class="fade-in flex items-center justify-between gap-3 bg-rose-50 border border-rose-200/60 text-rose-800 px-5 py-4 rounded-2xl text-sm shadow-sm transition-all duration-300 mb-6" role="alert">
                    <div class="flex items-center gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0"></i>
                        <div>
                            <span class="font-bold">Gagal Memperbarui Publikasi:</span>
                            <ul class="list-disc list-inside mt-1.5 space-y-1 font-semibold text-xs text-rose-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('error-alert').remove()" class="text-rose-500 hover:text-rose-700 hover:bg-rose-100/50 transition-colors p-1 rounded-lg" title="Tutup Notifikasi">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endif
            
            <!-- Judul Publikasi Field -->
            <div class="space-y-1.5">
                <label for="title" class="text-xs font-bold text-slate-600 uppercase tracking-wider">Judul Publikasi</label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    placeholder="Masukkan judul lengkap publikasi" 
                    value="{{ old('title', $publication->title) }}"
                    required
                    class="w-full px-4 py-3 bg-white border @error('title') border-rose-300 focus:ring-rose-100 @else border-slate-200 focus:ring-blue-100 @enderror rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-bps-primary focus:ring-4 transition-all text-sm font-medium"
                >
                @error('title')
                    <p class="text-rose-500 text-xs font-semibold mt-1 flex items-center gap-1">
                        <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Grid Kategori & Tahun -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                
                <!-- Kategori Dropdown -->
                <div class="space-y-1.5 relative" id="category-dropdown-container">
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Kategori Topik</label>
                    
                    <!-- Hidden native select for validation and form submit -->
                    <select name="category" id="category" required class="sr-only">
                        <option value="" disabled>Pilih Kategori</option>
                        <option value="Publikasi Umum" {{ old('category', $publication->category) === 'Publikasi Umum' ? 'selected' : '' }}>Publikasi Umum</option>
                        <option value="Statistik Sosial" {{ old('category', $publication->category) === 'Statistik Sosial' ? 'selected' : '' }}>Statistik Sosial</option>
                        <option value="Statistik Ekonomi" {{ old('category', $publication->category) === 'Statistik Ekonomi' ? 'selected' : '' }}>Statistik Ekonomi</option>
                        <option value="Statistik Pertanian" {{ old('category', $publication->category) === 'Statistik Pertanian' ? 'selected' : '' }}>Statistik Pertanian</option>
                        <option value="Statistik Industri" {{ old('category', $publication->category) === 'Statistik Industri' ? 'selected' : '' }}>Statistik Industri</option>
                        <option value="Statistik Distribusi" {{ old('category', $publication->category) === 'Statistik Distribusi' ? 'selected' : '' }}>Statistik Distribusi</option>
                        <option value="Statistik Lingkungan" {{ old('category', $publication->category) === 'Statistik Lingkungan' ? 'selected' : '' }}>Statistik Lingkungan</option>
                        <option value="Sensus & Survei" {{ old('category', $publication->category) === 'Sensus & Survei' ? 'selected' : '' }}>Sensus & Survei</option>
                    </select>

                    <!-- Custom Dropdown Trigger Button -->
                    <button 
                        type="button" 
                        id="category-trigger" 
                        class="w-full flex items-center justify-between px-4 py-3 bg-white border @error('category') border-rose-300 focus:ring-rose-100 @else border-slate-200 focus:ring-blue-100 @enderror rounded-xl text-slate-800 focus:outline-none transition-all text-sm font-medium"
                    >
                        <span id="category-selected-text" class="text-slate-400">Pilih Kategori</span>
                        <!-- Chevron Icon -->
                        <svg id="category-chevron" class="w-5 h-5 text-slate-400 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <!-- Custom Dropdown Menu -->
                    <div 
                        id="category-menu" 
                        class="hidden absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-slate-100/80 py-2 z-30 max-h-64 overflow-y-auto"
                    >
                        <div class="space-y-0.5 px-2">
                            <button 
                                type="button" 
                                data-value="Publikasi Umum" 
                                class="category-item w-full text-left px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 flex items-center justify-between transition-colors"
                            >
                                <span>Publikasi Umum</span>
                                <svg class="check-icon w-4 h-4 text-blue-600 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                            <button 
                                type="button" 
                                data-value="Statistik Sosial" 
                                class="category-item w-full text-left px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 flex items-center justify-between transition-colors"
                            >
                                <span>Statistik Sosial</span>
                                <svg class="check-icon w-4 h-4 text-blue-600 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                            <button 
                                type="button" 
                                data-value="Statistik Ekonomi" 
                                class="category-item w-full text-left px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 flex items-center justify-between transition-colors"
                            >
                                <span>Statistik Ekonomi</span>
                                <svg class="check-icon w-4 h-4 text-blue-600 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                            <button 
                                type="button" 
                                data-value="Statistik Pertanian" 
                                class="category-item w-full text-left px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 flex items-center justify-between transition-colors"
                            >
                                <span>Statistik Pertanian</span>
                                <svg class="check-icon w-4 h-4 text-blue-600 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                            <button 
                                type="button" 
                                data-value="Statistik Industri" 
                                class="category-item w-full text-left px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 flex items-center justify-between transition-colors"
                            >
                                <span>Statistik Industri</span>
                                <svg class="check-icon w-4 h-4 text-blue-600 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                            <button 
                                type="button" 
                                data-value="Statistik Distribusi" 
                                class="category-item w-full text-left px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 flex items-center justify-between transition-colors"
                            >
                                <span>Statistik Distribusi</span>
                                <svg class="check-icon w-4 h-4 text-blue-600 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                            <button 
                                type="button" 
                                data-value="Statistik Lingkungan" 
                                class="category-item w-full text-left px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 flex items-center justify-between transition-colors"
                            >
                                <span>Statistik Lingkungan</span>
                                <svg class="check-icon w-4 h-4 text-blue-600 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                            <button 
                                type="button" 
                                data-value="Sensus & Survei" 
                                class="category-item w-full text-left px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 flex items-center justify-between transition-colors"
                            >
                                <span>Sensus & Survei</span>
                                <svg class="check-icon w-4 h-4 text-blue-600 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @error('category')
                        <p class="text-rose-500 text-xs font-semibold mt-1 flex items-center gap-1">
                            <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Tanggal Rilis Publikasi (Custom Calendar) -->
                <div class="space-y-1.5 relative" id="calendar-dropdown-container">
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Tanggal Rilis Publikasi</label>
                    
                    <!-- Hidden native input for form submit -->
                    <input 
                        type="hidden" 
                        name="release_date" 
                        id="release_date" 
                        value="{{ old('release_date', $publication->release_date ? $publication->release_date->format('Y-m-d') : date('Y-m-d')) }}"
                        required
                    >

                    <!-- Custom Dropdown Trigger Button -->
                    <button 
                        type="button" 
                        id="calendar-trigger" 
                        class="w-full flex items-center justify-between px-4 py-3 bg-white border @error('release_date') border-rose-300 focus:ring-rose-100 @else border-slate-200 focus:ring-blue-100 @enderror rounded-xl text-slate-800 focus:outline-none transition-all text-sm font-medium"
                    >
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            <span id="calendar-selected-text" class="text-slate-800">Pilih Tanggal</span>
                        </div>
                        <!-- Chevron Icon -->
                        <svg id="calendar-chevron" class="w-5 h-5 text-slate-400 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <!-- Custom Calendar Dropdown Card -->
                    <div 
                        id="calendar-menu" 
                        class="hidden absolute left-1/2 -translate-x-[53%] mt-2 bg-white rounded-[2rem] shadow-2xl border border-slate-100/80 p-5 z-30 w-full sm:w-[340px] select-none"
                    >
                        <!-- Calendar Header -->
                        <div class="flex items-center justify-between mb-4 px-1">
                            <div class="flex items-center gap-2 text-[#1e5eff]">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <button type="button" id="prev-month" class="p-1 rounded-lg hover:bg-slate-50 text-[#1e5eff] transition-colors">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                    </svg>
                                </button>
                                <button type="button" id="calendar-month-year" class="text-base font-extrabold text-[#1e5eff] tracking-tight min-w-[120px] text-center hover:bg-blue-50/50 px-2.5 py-0.5 rounded-xl transition-all cursor-pointer">Juni 2026</button>
                                <button type="button" id="next-month" class="p-1 rounded-lg hover:bg-slate-50 text-[#1e5eff] transition-colors">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Days View -->
                        <div id="calendar-days-view">
                            <!-- Days of Week Headers -->
                            <div class="grid grid-cols-7 gap-y-2 text-center mb-3">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Mon</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Tue</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Wed</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Thu</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Fri</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Sat</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Sun</span>
                            </div>

                            <!-- Days Grid -->
                            <div id="calendar-days" class="grid grid-cols-7 gap-y-1.5 text-center">
                                <!-- Dynamic days here -->
                            </div>
                        </div>

                        <!-- Years View -->
                        <div id="calendar-years-view" class="hidden py-2">
                            <div id="calendar-years-grid" class="grid grid-cols-3 gap-3 text-center">
                                <!-- Dynamic years here -->
                            </div>
                        </div>

                        <!-- Floating Badge (Bottom Right 3D-like Style) -->
                        <div class="absolute -bottom-6 -right-6 w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-xl border border-slate-100/50 hover:scale-105 transition-transform duration-200">
                            <!-- 3D Style Calendar Icon -->
                            <div class="w-10 h-10 bg-blue-600 rounded-lg shadow-md border-t-4 border-blue-500 relative overflow-hidden flex flex-col justify-end p-1 select-none">
                                <!-- Binder Holes representation -->
                                <div class="absolute top-0.5 left-2 w-1.5 h-1 bg-white rounded-full"></div>
                                <div class="absolute top-0.5 right-2 w-1.5 h-1 bg-white rounded-full"></div>
                                <!-- Current Day Number inside Badge -->
                                <span id="floating-badge-day" class="text-white text-xs font-bold text-center leading-none mt-1">31</span>
                            </div>
                        </div>
                    </div>
                    @error('release_date')
                        <p class="text-rose-500 text-xs font-semibold mt-1 flex items-center gap-1">
                            <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Upload PDF File Field -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Unggah Berkas PDF (Opsional)</label>
                
                <!-- File Drop Area/Wrapper -->
                <div class="relative border-2 border-dashed @error('pdf') border-rose-300 bg-rose-50/10 @else border-slate-200 bg-slate-50/50 hover:bg-slate-50 @enderror hover:border-bps-primary rounded-2xl p-6 transition-all duration-200 text-center">
                    <input 
                        type="file" 
                        name="pdf" 
                        id="pdf" 
                        accept=".pdf" 
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        onchange="updateFileName(this)"
                    >
                    <input type="hidden" name="page_count" id="hidden_page_count" value="">
                    <input type="hidden" name="file_size" id="hidden_file_size" value="">
                    
                    <!-- Drag-and-drop prompt (hidden because file is already attached) -->
                    <div class="hidden flex-col items-center justify-center space-y-2 pointer-events-none" id="upload-prompt">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-bps-primary flex items-center justify-center">
                            <!-- Cloud Upload Icon -->
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Klik atau seret file PDF baru Anda di sini</p>
                        <p class="text-[10px] text-slate-400 font-medium">Hanya format .pdf dengan ukuran maksimal 25 MB</p>
                    </div>

                    <!-- Selected File Info (visible because file is already attached) -->
                    <div class="flex flex-col items-center justify-center space-y-2" id="file-info">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center animate-pulse">
                            <!-- Document Check Icon -->
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-emerald-700" id="file-name">{{ basename($publication->pdf_path) }}</p>
                        <p class="text-[10px] text-slate-400 font-medium" id="file-size">Ukuran: {{ $fileSizeInMB }} MB (Sudah Terlampir)</p>
                        <button type="button" class="text-[10px] font-bold text-rose-500 hover:underline z-20" onclick="resetFileInput(event)">Hapus & Ganti Berkas</button>
                    </div>
                </div>
                
                @error('pdf')
                    <p class="text-rose-500 text-xs font-semibold mt-1 flex items-center gap-1">
                        <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a 
                    href="{{ route('publications.index') }}" 
                    class="px-5 py-3 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-2xl font-semibold transition-all duration-200 text-sm"
                >
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="flex items-center gap-2 px-6 py-3 bg-bps-primary hover:bg-blue-700 text-white rounded-2xl font-semibold shadow-lg shadow-blue-500/10 hover:shadow-blue-500/25 transition-all duration-200 text-sm hover:-translate-y-0.5 active:translate-y-0"
                >
                    <!-- Check Icon -->
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>

        </form>
    </div>
</div>

<!-- JS File helper script -->
<script>
    function updateFileName(input) {
        const file = input.files[0];
        const prompt = document.getElementById('upload-prompt');
        const fileInfo = document.getElementById('file-info');
        
        if (file) {
            // Check file type
            if (file.type !== 'application/pdf' && !file.name.endsWith('.pdf')) {
                alert('Format file tidak didukung! Mohon pilih berkas dengan format PDF.');
                input.value = '';
                return;
            }

            // Check size (25 MB = 25 * 1024 * 1024 bytes)
            const maxSize = 25 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Ukuran file terlalu besar! Batas maksimal adalah 25 MB.');
                input.value = '';
                return;
            }

            const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
            let formattedSize = sizeInMB + ' MB';
            if (file.size < 1024 * 1024) {
                formattedSize = (file.size / 1024).toFixed(2) + ' KB';
            }
            
            document.getElementById('hidden_file_size').value = formattedSize;
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('file-size').textContent = 'Mengambil informasi halaman...';
            
            prompt.classList.add('hidden');
            fileInfo.classList.remove('hidden');
            fileInfo.classList.add('flex');

            // Baca jumlah halaman menggunakan pdf.js secara real-time
            const reader = new FileReader();
            reader.onload = function(e) {
                const typedarray = new Uint8Array(e.target.result);
                pdfjsLib.getDocument(typedarray).promise.then(function(pdf) {
                    const pageCount = pdf.numPages;
                    document.getElementById('hidden_page_count').value = pageCount;
                    document.getElementById('file-size').textContent = 'Ukuran: ' + formattedSize + ' | ' + pageCount + ' Halaman';
                }).catch(function(err) {
                    console.error('PdfJS error:', err);
                    document.getElementById('file-size').textContent = 'Ukuran: ' + formattedSize;
                });
            };
            reader.readAsArrayBuffer(file);
        }
    }

    function resetFileInput(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const input = document.getElementById('pdf');
        const prompt = document.getElementById('upload-prompt');
        const fileInfo = document.getElementById('file-info');
        
        input.value = '';
        document.getElementById('hidden_page_count').value = '';
        document.getElementById('hidden_file_size').value = '';
        prompt.classList.remove('hidden');
        fileInfo.classList.add('hidden');
        fileInfo.classList.remove('flex');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('category-dropdown-container');
        if (!container) return;

        const trigger = document.getElementById('category-trigger');
        const menu = document.getElementById('category-menu');
        const chevron = document.getElementById('category-chevron');
        const selectedText = document.getElementById('category-selected-text');
        const hiddenSelect = document.getElementById('category');
        const items = document.querySelectorAll('.category-item');

        function selectOption(value) {
            hiddenSelect.value = value;
            selectedText.textContent = value;
            selectedText.classList.remove('text-slate-400');
            selectedText.classList.add('text-slate-800');

            items.forEach(item => {
                const isSelected = item.getAttribute('data-value') === value;
                const checkIcon = item.querySelector('.check-icon');
                
                if (isSelected) {
                    item.classList.remove('text-slate-600', 'hover:bg-slate-50');
                    item.classList.add('bg-blue-50/50', 'text-blue-600', 'font-semibold');
                    if (checkIcon) checkIcon.classList.remove('hidden');
                } else {
                    item.classList.remove('bg-blue-50/50', 'text-blue-600', 'font-semibold');
                    item.classList.add('text-slate-600', 'hover:bg-slate-50');
                    if (checkIcon) checkIcon.classList.add('hidden');
                }
            });
        }

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = !menu.classList.contains('hidden');
            if (isOpen) {
                menu.classList.add('hidden');
                chevron.classList.remove('rotate-180');
                trigger.classList.remove('border-bps-primary', 'ring-4', 'ring-blue-100');
            } else {
                menu.classList.remove('hidden');
                chevron.classList.add('rotate-180');
                trigger.classList.add('border-bps-primary', 'ring-4', 'ring-blue-100');
            }
        });

        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                menu.classList.add('hidden');
                chevron.classList.remove('rotate-180');
                trigger.classList.remove('border-bps-primary', 'ring-4', 'ring-blue-100');
            }
        });

        items.forEach(item => {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                const val = this.getAttribute('data-value');
                selectOption(val);
                menu.classList.add('hidden');
                chevron.classList.remove('rotate-180');
                trigger.classList.remove('border-bps-primary', 'ring-4', 'ring-blue-100');
            });
        });

        const initialVal = "{{ old('category', $publication->category) }}";
        if (initialVal) {
            selectOption(initialVal);
        }

        // Custom Calendar Date Picker
        const calendarContainer = document.getElementById('calendar-dropdown-container');
        if (calendarContainer) {
            const calendarTrigger = document.getElementById('calendar-trigger');
            const calendarMenu = document.getElementById('calendar-menu');
            const calendarChevron = document.getElementById('calendar-chevron');
            const calendarSelectedText = document.getElementById('calendar-selected-text');
            const releaseDateInput = document.getElementById('release_date');
            const calendarDaysContainer = document.getElementById('calendar-days');
            const calendarMonthYearLabel = document.getElementById('calendar-month-year');
            const prevMonthBtn = document.getElementById('prev-month');
            const nextMonthBtn = document.getElementById('next-month');
            const floatingBadgeDay = document.getElementById('floating-badge-day');

            // Month names in Indonesian
            const monthNames = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];

            // Parse initial date from hidden input
            let currentDate = new Date();
            if (releaseDateInput.value) {
                currentDate = new Date(releaseDateInput.value);
            }

            let viewDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            let selectedDate = new Date(currentDate);

            // Initial view state
            let currentView = 'days';
            let viewedYearsStart = 2020;

            function formatDateIndonesian(date) {
                const day = date.getDate();
                const month = monthNames[date.getMonth()];
                const year = date.getFullYear();
                return `${day} ${month} ${year}`;
            }

            function updateTriggerText() {
                calendarSelectedText.textContent = formatDateIndonesian(selectedDate);
                floatingBadgeDay.textContent = selectedDate.getDate();
            }

            function renderCalendar() {
                const year = viewDate.getFullYear();
                const month = viewDate.getMonth();

                const daysView = document.getElementById('calendar-days-view');
                const yearsView = document.getElementById('calendar-years-view');

                if (currentView === 'days') {
                    daysView.classList.remove('hidden');
                    yearsView.classList.add('hidden');

                    calendarMonthYearLabel.textContent = `${monthNames[month]} ${year}`;

                    // Clear days grid
                    calendarDaysContainer.innerHTML = '';

                    // Get first day of month (0 = Sun, 1 = Mon, ..., 6 = Sat)
                    const firstDayIndexRaw = new Date(year, month, 1).getDay();
                    const firstDayIndex = firstDayIndexRaw === 0 ? 6 : firstDayIndexRaw - 1;

                    // Total days in current month
                    const totalDays = new Date(year, month + 1, 0).getDate();

                    // Total days in previous month
                    const prevTotalDays = new Date(year, month, 0).getDate();

                    // Trailing days from previous month
                    for (let i = firstDayIndex - 1; i >= 0; i--) {
                        const prevDay = prevTotalDays - i;
                        const prevMonthDate = new Date(year, month - 1, prevDay);
                        
                        const dayEl = document.createElement('button');
                        dayEl.type = 'button';
                        dayEl.className = 'text-slate-300 font-medium text-xs py-2 text-center rounded-xl hover:bg-slate-50 cursor-pointer transition-all flex flex-col items-center justify-center relative w-8 h-8 mx-auto';
                        dayEl.innerHTML = `<span>${prevDay}</span>`;
                        dayEl.addEventListener('click', function(e) {
                            e.stopPropagation();
                            selectedDate = prevMonthDate;
                            viewDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
                            releaseDateInput.value = `${selectedDate.getFullYear()}-${String(selectedDate.getMonth() + 1).padStart(2, '0')}-${String(selectedDate.getDate()).padStart(2, '0')}`;
                            updateTriggerText();
                            renderCalendar();
                            closeCalendar();
                        });
                        calendarDaysContainer.appendChild(dayEl);
                    }

                    // Days of current month
                    for (let day = 1; day <= totalDays; day++) {
                        const thisDate = new Date(year, month, day);
                        const isSelected = thisDate.getFullYear() === selectedDate.getFullYear() &&
                                         thisDate.getMonth() === selectedDate.getMonth() &&
                                         thisDate.getDate() === selectedDate.getDate();

                        const dayEl = document.createElement('button');
                        dayEl.type = 'button';
                        dayEl.className = 'font-bold text-xs py-1.5 text-center rounded-full cursor-pointer transition-all flex flex-col items-center justify-center relative w-8 h-8 mx-auto';
                        
                        const isToday = new Date().toDateString() === thisDate.toDateString();
                        const hasDot = isToday || isSelected || day === 2 || day === 5 || day === 12 || day === 18 || day === 25 || day === 29 || day === 30;

                        if (isSelected) {
                            dayEl.className += ' bg-[#1e5eff] text-white shadow-md shadow-blue-500/20 font-extrabold scale-110';
                        } else {
                            dayEl.className += ' text-slate-600 hover:bg-slate-100';
                        }

                        let dotHtml = '';
                        if (hasDot) {
                            const dotColor = isSelected ? 'bg-white/70' : 'bg-slate-300';
                            dotHtml = `<span class="w-1.5 h-1.5 rounded-full ${dotColor} absolute bottom-0.5 left-1/2 transform -translate-x-1/2"></span>`;
                        }

                        dayEl.innerHTML = `<span class="${isSelected ? '' : 'text-slate-600'}">${day}</span>${dotHtml}`;

                        dayEl.addEventListener('click', function(e) {
                            e.stopPropagation();
                            selectedDate = thisDate;
                            releaseDateInput.value = `${selectedDate.getFullYear()}-${String(selectedDate.getMonth() + 1).padStart(2, '0')}-${String(selectedDate.getDate()).padStart(2, '0')}`;
                            updateTriggerText();
                            renderCalendar();
                            closeCalendar();
                        });
                        
                        calendarDaysContainer.appendChild(dayEl);
                    }

                    // Remaining days to fill 42-grid
                    const gridRemaining = 42 - calendarDaysContainer.children.length;
                    for (let i = 1; i <= gridRemaining; i++) {
                        const nextDayDate = new Date(year, month + 1, i);
                        const dayEl = document.createElement('button');
                        dayEl.type = 'button';
                        dayEl.className = 'text-slate-300 font-medium text-xs py-2 text-center rounded-xl hover:bg-slate-50 cursor-pointer transition-all flex flex-col items-center justify-center relative w-8 h-8 mx-auto';
                        dayEl.innerHTML = `<span>${i}</span>`;
                        dayEl.addEventListener('click', function(e) {
                            e.stopPropagation();
                            selectedDate = nextDayDate;
                            viewDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
                            releaseDateInput.value = `${selectedDate.getFullYear()}-${String(selectedDate.getMonth() + 1).padStart(2, '0')}-${String(selectedDate.getDate()).padStart(2, '0')}`;
                            updateTriggerText();
                            renderCalendar();
                            closeCalendar();
                        });
                        calendarDaysContainer.appendChild(dayEl);
                    }
                } else if (currentView === 'years') {
                    daysView.classList.add('hidden');
                    yearsView.classList.remove('hidden');

                    const startYear = viewedYearsStart;
                    const endYear = startYear + 11;
                    calendarMonthYearLabel.textContent = `${startYear} - ${endYear}`;

                    const yearsGrid = document.getElementById('calendar-years-grid');
                    yearsGrid.innerHTML = '';

                    for (let y = startYear; y <= endYear; y++) {
                        const isSelectedYear = y === selectedDate.getFullYear();
                        const yearEl = document.createElement('button');
                        yearEl.type = 'button';
                        yearEl.className = 'font-bold text-xs py-3 text-center rounded-2xl cursor-pointer transition-all hover:bg-slate-50 text-slate-600 border border-slate-100';
                        
                        if (isSelectedYear) {
                            yearEl.className = 'font-extrabold text-xs py-3 text-center rounded-2xl cursor-pointer transition-all bg-[#1e5eff] text-white shadow-md shadow-blue-500/20 border border-blue-200';
                        }

                        yearEl.textContent = y;
                        yearEl.addEventListener('click', function(e) {
                            e.stopPropagation();
                            viewDate.setFullYear(y);
                            currentView = 'days';
                            renderCalendar();
                        });
                        yearsGrid.appendChild(yearEl);
                    }
                }
            }

            function openCalendar() {
                currentView = 'days';
                calendarMenu.classList.remove('hidden');
                calendarChevron.classList.add('rotate-180');
                calendarTrigger.classList.add('border-bps-primary', 'ring-4', 'ring-blue-100');
                renderCalendar();
            }

            function closeCalendar() {
                calendarMenu.classList.add('hidden');
                calendarChevron.classList.remove('rotate-180');
                calendarTrigger.classList.remove('border-bps-primary', 'ring-4', 'ring-blue-100');
            }

            calendarTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = !calendarMenu.classList.contains('hidden');
                if (isOpen) {
                    closeCalendar();
                } else {
                    openCalendar();
                }
            });

            document.addEventListener('click', function(e) {
                if (!calendarContainer.contains(e.target)) {
                    closeCalendar();
                }
            });

            // Toggle views when clicking the header label
            calendarMonthYearLabel.addEventListener('click', function(e) {
                e.stopPropagation();
                if (currentView === 'days') {
                    currentView = 'years';
                    viewedYearsStart = viewDate.getFullYear() - 5;
                } else {
                    currentView = 'days';
                }
                renderCalendar();
            });

            prevMonthBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (currentView === 'days') {
                    viewDate.setMonth(viewDate.getMonth() - 1);
                } else {
                    viewedYearsStart -= 12;
                }
                renderCalendar();
            });

            nextMonthBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (currentView === 'days') {
                    viewDate.setMonth(viewDate.getMonth() + 1);
                } else {
                    viewedYearsStart += 12;
                }
                renderCalendar();
            });

            // Initial view
            updateTriggerText();
            renderCalendar();
        }
    });
</script>
@endsection
