@extends('layouts.admin')

@section('title', 'Dashboard Admin - Sistem Ringkasan & Manajemen Publikasi Statistik BPS')

@section('content')
<div class="space-y-8">
    
    <!-- SECTION 1: 4 STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Publikasi -->
        <a href="{{ route('publications.index') }}" class="group bg-white p-6 rounded-xl shadow-md border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Publikasi</p>
                    <h3 class="heading-font text-3xl font-black text-bps-navy mt-2 group-hover:text-bps-primary transition-colors">{{ $totalPublications }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-bps-primary flex items-center justify-center group-hover:bg-bps-primary group-hover:text-white transition-all duration-300">
                    <!-- Document Icon -->
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
            </div>
        </a>

        <!-- Card 2: Total Ringkasan -->
        <a href="{{ route('publications.index', ['status' => 'Selesai']) }}" class="group bg-white p-6 rounded-xl shadow-md border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Ringkasan</p>
                    <h3 class="heading-font text-3xl font-black text-bps-navy mt-2 group-hover:text-bps-orange transition-colors">{{ $totalSummaries }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-bps-orange flex items-center justify-center group-hover:bg-bps-orange group-hover:text-white transition-all duration-300">
                    <!-- Document Text Icon -->
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zM9 11v6m3-3h3m-3-3h3" />
                    </svg>
                </div>
            </div>
        </a>

        <!-- Card 3: Kategori -->
        <a href="{{ route('publications.index') }}" class="group bg-white p-6 rounded-xl shadow-md border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 block">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori</p>
                    <h3 class="heading-font text-3xl font-black text-bps-navy mt-2 group-hover:text-bps-green transition-colors">{{ $totalCategories }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-bps-green flex items-center justify-center group-hover:bg-bps-green group-hover:text-white transition-all duration-300">
                    <!-- Folder/Tag Icon -->
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v13.5A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V10.5m-11.432-7.5l2.25 2.25m-2.25-2.25l2.75-2.75M21 10.5H11.568" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h19.5M2 6.75h20M12 9v9m-3-3h6" />
                    </svg>
                </div>
            </div>
        </a>

        <!-- Card 4: Total Pengguna -->
        <div class="group bg-white p-6 rounded-xl shadow-md border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pengguna</p>
                    <h3 class="heading-font text-3xl font-black text-bps-navy mt-2 group-hover:text-indigo-600 transition-colors">1</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                    <!-- Users Icon -->
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A2.25 2.25 0 0112.75 21.5h-1.5a2.25 2.25 0 01-2.25-2.263V19.13m0 0a9.338 9.338 0 00-2.625.372 9.337 9.337 0 00-4.121-.952 4.125 4.125 0 007.533-2.493M9 19.128v-.003c0-1.113.285-2.16.786-3.07M12 11.25a3.375 3.375 0 100-6.75 3.375 3.375 0 000 6.75zM19.5 10.5a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM4.5 10.5a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: 2 CHART PLACEHOLDERS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Box: Publikasi per Kategori -->
        <div class="bg-white p-6 rounded-xl shadow-md border border-slate-100 flex flex-col min-h-[350px]">
            <h3 class="heading-font text-base font-bold text-slate-800 mb-4 pb-3 border-b border-slate-50 flex items-center gap-2">
                <!-- Pie Chart Icon -->
                <svg class="w-5 h-5 text-bps-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                </svg>
                <span>Publikasi per Kategori</span>
            </h3>
            <div class="flex-1 relative flex items-center justify-center min-h-[220px]">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Right Box: Ringkasan per Bulan -->
        <div class="bg-white p-6 rounded-xl shadow-md border border-slate-100 flex flex-col min-h-[350px]">
            <h3 class="heading-font text-base font-bold text-slate-800 mb-4 pb-3 border-b border-slate-50 flex items-center gap-2">
                <!-- Line Chart Icon -->
                <svg class="w-5 h-5 text-bps-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                </svg>
                <span>Ringkasan per Bulan</span>
            </h3>
            <div class="flex-1 relative flex items-center justify-center min-h-[220px]">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- SECTION 3: RECENT PUBLICATIONS TABLE -->
    <div class="bg-white rounded-xl shadow-md border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="heading-font text-base font-bold text-slate-800 flex items-center gap-2">
                <!-- Table Icon -->
                <svg class="w-5 h-5 text-bps-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75c.621 0 1.125.504 1.125 1.125v12.75c0 .621-.504 1.125-1.125 1.125H5.625c-.621 0-1.125-.504-1.125-1.125V5.625c0-.621.504-1.125 1.125-1.125z" />
                </svg>
                <span>Publikasi Terbaru</span>
            </h3>
            <span class="text-[11px] font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full">5 Data Terbaru</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tahun</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @if ($recentPublications->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 font-semibold italic">
                                Belum ada publikasi terunggah.
                            </td>
                        </tr>
                    @else
                        @foreach ($recentPublications as $publication)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800 line-clamp-1 leading-snug" title="{{ $publication->title }}">
                                    <a href="{{ route('publications.show', $publication) }}" class="hover:text-bps-primary hover:underline transition-colors">
                                        {{ $publication->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-slate-500 font-medium">{{ $publication->category }}</td>
                                <td class="px-6 py-4 text-slate-500 font-medium">{{ $publication->year }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center">
                                        @if ($publication->status === 'Selesai')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold select-none">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                {{ $publication->status }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-bold select-none">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                {{ $publication->status }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="/storage/{{ $publication->pdf_path }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-bps-primary hover:text-blue-800 font-bold bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-xl transition-all" title="Lihat PDF">
                                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Lihat</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari Controller
        const categoryData = @json($categoryData);
        const monthlyData = @json($monthlyData);

        // 1. Chart Kategori (Doughnut Chart)
        const ctxCategory = document.getElementById('categoryChart').getContext('2d');
        const categories = Object.keys(categoryData);
        const categoryCounts = Object.values(categoryData);
        
        // Periksa jika seluruh data kosong
        const isCategoryEmpty = categoryCounts.every(val => val === 0);

        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: categories,
                datasets: [{
                    data: isCategoryEmpty ? [1, 1, 1, 1, 1, 1, 1, 1] : categoryCounts,
                    backgroundColor: [
                        '#0266b3', // BPS Blue
                        '#ff9e1b', // BPS Orange
                        '#43b02a', // BPS Green
                        '#e11d48', // Rose
                        '#4910a3', // Purple
                        '#0f2a4a', // BPS Navy
                        '#06b6d4', // Cyan
                        '#84cc16'  // Lime
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 11,
                                family: 'Inter',
                                weight: '600'
                            },
                            color: '#1e293b',
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (isCategoryEmpty) {
                                    return ' ' + context.label + ': 0 publikasi';
                                }
                                return ' ' + context.label + ': ' + context.raw + ' publikasi';
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // 2. Chart Ringkasan Bulanan (Bar Chart)
        const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
        const months = Object.keys(monthlyData);
        const monthlyCounts = Object.values(monthlyData);

        new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Jumlah Ringkasan',
                    data: monthlyCounts,
                    backgroundColor: 'rgba(67, 176, 42, 0.15)',
                    borderColor: '#43b02a', // BPS Green
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 16
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 10,
                                family: 'Inter',
                                weight: '600'
                            },
                            color: '#475569'
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 10,
                                family: 'Inter',
                                weight: '600'
                            },
                            color: '#475569'
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endsection
