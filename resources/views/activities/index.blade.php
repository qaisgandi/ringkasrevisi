@extends('layouts.admin')

@section('title', 'Aktivitas Sistem - Sistem Ringkasan & Manajemen Publikasi Statistik BPS')

@section('content')
<!-- Include Lucide CDN -->
<script src="https://unpkg.com/lucide@latest"></script>

<div class="space-y-8 fade-in">
    
    <!-- HEADER SECTION -->
    <div class="relative bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 overflow-hidden">
        <!-- Decorative Background Circle -->
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-blue-50/50 rounded-full blur-3xl -z-10"></div>
        
        <div class="space-y-1">
            <h1 class="heading-font text-2xl font-extrabold text-bps-navy tracking-tight">
                Aktivitas Sistem
            </h1>
            <p class="text-sm text-slate-400 font-medium leading-relaxed max-w-xl">
                Riwayat log aktivitas sistem, termasuk proses unggahan berkas, pembaruan, penghapusan, dan ringkasan AI.
            </p>
        </div>

        <div class="shrink-0 flex items-center gap-3">
            @if ($activities->total() > 0)
                <!-- Clear Logs Button -->
                <button 
                    onclick="openClearModal()"
                    class="inline-flex items-center gap-2 px-5 py-3 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-2xl font-bold border border-rose-100/50 transition-all duration-200 text-sm"
                >
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span>Hapus Semua Log</span>
                </button>
            @endif
        </div>
    </div>

    <!-- SUCCESS NOTIFICATION -->
    @if (session('success'))
        <div id="activity-alert" class="fade-in flex items-center justify-between gap-3 bg-emerald-50 border border-emerald-200/60 text-emerald-800 px-5 py-4 rounded-2xl text-sm shadow-sm transition-all duration-300 mb-6" role="alert">
            <div class="flex items-center gap-3">
                <i data-lucide="check" class="w-5 h-5 text-emerald-500 shrink-0"></i>
                <div class="font-medium">{{ session('success') }}</div>
            </div>
            <button onclick="document.getElementById('activity-alert').remove()" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg" title="Tutup Notifikasi">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    <!-- FILTER & TOOLBAR SECTION -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 md:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('activities.index') }}" method="GET" class="flex flex-col sm:flex-row flex-1 items-stretch sm:items-center gap-3 m-0">
            <!-- Search bar -->
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                </span>
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari aktivitas..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 font-medium focus:outline-none focus:border-bps-primary focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all duration-200"
                >
            </div>

            <!-- Type Filter -->
            <select 
                name="type" 
                onchange="this.form.submit()"
                class="px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-600 font-bold focus:outline-none focus:border-bps-primary focus:bg-white transition-all min-w-[150px]"
            >
                <option value="all" {{ request('type') === 'all' || !request('type') ? 'selected' : '' }}>Semua Tipe</option>
                <option value="upload" {{ request('type') === 'upload' ? 'selected' : '' }}>Unggahan</option>
                <option value="summary" {{ request('type') === 'summary' ? 'selected' : '' }}>Ekstraksi AI</option>
                <option value="delete" {{ request('type') === 'delete' ? 'selected' : '' }}>Penghapusan</option>
            </select>

            @if (request('search') || request('type'))
                <!-- Reset Filter Link -->
                <a 
                    href="{{ route('activities.index') }}" 
                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-200 rounded-xl font-bold text-xs transition-all"
                >
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- TIMELINE LIST -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8">
        @if ($activities->isEmpty())
            <div class="py-16 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mb-4 border border-slate-100">
                    <i data-lucide="info" class="w-8 h-8"></i>
                </div>
                <h3 class="heading-font text-base font-bold text-slate-700">Belum Ada Aktivitas</h3>
                <p class="text-sm text-slate-400 mt-1 max-w-sm">
                    @if (request('search') || request('type'))
                        Pencarian Anda tidak menemukan log aktivitas yang cocok.
                    @else
                        Belum ada aktivitas yang dicatat oleh sistem saat ini.
                    @endif
                </p>
            </div>
        @else
            <!-- Timeline Container -->
            <div class="relative pl-6 md:pl-8 border-l border-slate-100 space-y-8">
                @foreach ($activities as $activity)
                    @php
                        // Color mapping based on type
                        $bgColor = 'bg-blue-50 text-blue-600';
                        $iconName = 'info';

                        if ($activity->type === 'upload') {
                            $bgColor = 'bg-emerald-50 text-emerald-600 border border-emerald-100';
                            $iconName = 'upload-cloud';
                        } elseif ($activity->type === 'delete') {
                            $bgColor = 'bg-rose-50 text-rose-600 border border-rose-100';
                            $iconName = 'trash-2';
                        } elseif ($activity->type === 'summary') {
                            $bgColor = 'bg-blue-50 text-bps-primary border border-blue-100';
                            $iconName = 'sparkles';
                        }
                    @endphp

                    <!-- Timeline Item -->
                    <div class="relative group">
                        <!-- Timeline Point Marker -->
                        <div class="absolute -left-[43px] md:-left-[51px] top-0.5 w-8 h-8 rounded-full {{ $bgColor }} flex items-center justify-center shadow-sm z-10 transition-transform group-hover:scale-110">
                            <i data-lucide="{{ $iconName }}" class="w-4 h-4"></i>
                        </div>

                        <!-- Card Content -->
                        <div class="space-y-1">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                                <h3 class="text-sm font-bold text-slate-800 leading-tight">
                                    {{ $activity->title }}
                                </h3>
                                <span class="text-[10px] text-slate-400 font-bold shrink-0 flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    {{ $activity->created_at->diffForHumans() }}
                                    <span class="text-slate-200">|</span>
                                    {{ $activity->created_at->format('d M Y H:i') }}
                                </span>
                            </div>
                            
                            <p class="text-xs text-slate-500 leading-relaxed font-medium max-w-3xl">
                                {{ $activity->message }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- PAGINATION -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                {{ $activities->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- CLEAR LOGS CONFIRMATION MODAL -->
<div id="clear-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm select-none">
    <div id="clear-backdrop" onclick="closeClearModal()" class="absolute inset-0 bg-transparent transition-opacity duration-300"></div>
    <div id="clear-card" class="relative bg-white rounded-3xl p-6 md:p-8 max-w-md w-full shadow-2xl border border-slate-100 flex flex-col items-center text-center transform scale-90 opacity-0 transition-all duration-300 z-10">
        <!-- Close Button -->
        <button onclick="closeClearModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 hover:bg-slate-50 p-1.5 rounded-xl transition-all">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <!-- Warning Icon -->
        <div class="w-16 h-16 rounded-full bg-rose-50 border-4 border-rose-100/50 text-rose-500 flex items-center justify-center mb-5 shrink-0 shadow-inner">
            <i data-lucide="alert-triangle" class="w-8 h-8"></i>
        </div>

        <h3 class="heading-font text-lg font-extrabold text-bps-navy tracking-tight mb-2">Hapus Semua Riwayat?</h3>
        <p class="text-xs text-slate-400 leading-relaxed font-semibold mb-6">
            Apakah Anda yakin ingin menghapus seluruh log aktivitas sistem? Tindakan ini bersifat permanen dan seluruh riwayat catatan aktivitas akan hilang.
        </p>

        <div class="flex items-center gap-3 w-full">
            <button 
                onclick="closeClearModal()" 
                class="w-full py-3 px-4 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 font-bold rounded-2xl transition-all duration-200 text-xs"
            >
                Batal
            </button>
            <form action="{{ route('activities.clear') }}" method="POST" class="m-0 w-full">
                @csrf
                @method('DELETE')
                <button 
                    type="submit" 
                    class="w-full py-3 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/10 hover:shadow-rose-500/25 transition-all duration-200 text-xs"
                >
                    Ya, Hapus Semua
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) {
            window.lucide.createIcons();
        }

        const modal = document.getElementById('clear-modal');
        const backdrop = document.getElementById('clear-backdrop');
        const card = document.getElementById('clear-card');

        window.openClearModal = function() {
            if (modal && backdrop && card) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                    card.classList.remove('opacity-0', 'scale-90');
                    card.classList.add('opacity-100', 'scale-100');
                }, 50);
            }
        };

        window.closeClearModal = function() {
            if (modal && backdrop && card) {
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
    });
</script>
@endsection
