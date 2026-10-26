@extends('layouts.admin')
@section('title', 'Semua Ringkasan — RINGKAS')
@section('content')
@php
    $summaryCount = $totalSelesaiCount ?? $publications->count();
    $pendingCount = $totalPendingCount ?? 0;
    $categories = $publications->pluck('category')->filter()->unique()->sort()->values();
@endphp
<div class="rk-page-heading"><div><p class="rk-kicker">Hasil pemrosesan</p><h1>Semua Ringkasan</h1><p>Jelajahi ringkasan yang tersedia, lalu buka publikasi sumber untuk membaca hasil lengkap.</p></div><a class="rk-btn rk-btn-secondary" href="{{ route('publications.index') }}">Kelola publikasi</a></div>
<dl class="rk-metrics"><div><dt>Ringkasan tersedia</dt><dd>{{ $summaryCount }}</dd></div><div><dt>Publikasi belum selesai</dt><dd>{{ $pendingCount }}</dd></div><div><dt>Kategori dengan ringkasan</dt><dd>{{ $categories->count() }}</dd></div></dl>
<div class="rk-panel">
<form action="{{ route('admin.summaries') }}" method="GET" class="rk-toolbar" role="search">
<div class="rk-field"><label for="summary-search">Cari publikasi</label><input id="summary-search" name="search" value="{{ request('search') }}" placeholder="Judul publikasi atau kategori"></div>
<div class="rk-field"><label for="summary-category">Kategori sumber</label><select id="summary-category" name="category"><option value="">Semua kategori</option>@foreach($categories as $category)<option @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select></div>
<button class="rk-btn" type="submit">Terapkan</button>@if(request('search') || request('category'))<a class="rk-link" href="{{ route('admin.summaries') }}">Hapus filter</a>@endif
</form>
@if($publications->isEmpty())
<x-empty-state :title="request('search') || request('category') ? 'Tidak ada ringkasan yang sesuai' : 'Belum ada ringkasan tersedia'" :description="request('search') || request('category') ? 'Coba kata pencarian lain atau hapus filter.' : 'Unggah publikasi, kemudian jalankan ekstraksi dari daftar publikasi. Hasil analisis yang selesai akan muncul di sini.'"><a href="{{ route('publications.index') }}" class="rk-btn rk-btn-secondary">Lihat publikasi</a></x-empty-state>
@else
<div class="rk-table-wrap"><table class="rk-table"><caption class="sr-only">Hasil ringkasan dan publikasi sumber</caption><thead><tr><th scope="col">Publikasi sumber / ikhtisar</th><th scope="col">Kategori</th><th scope="col">Hasil diperbarui</th><th scope="col">Status</th><th scope="col">Tindakan</th></tr></thead><tbody>
@foreach($publications as $publication)<tr><td><a class="rk-link font-semibold" href="{{ route('publications.show', $publication) }}">{{ $publication->title }}</a><small>{{ \Illuminate\Support\Str::limit(strip_tags($publication->aiResult?->summary ?? $publication->summary ?? ''), 180) }}</small></td><td>{{ $publication->category }}<small>{{ $publication->year }}</small></td><td>{{ $publication->updated_at?->format('d M Y') ?? '—' }}</td><td><span class="rk-status">Tersedia</span></td><td><a class="rk-link" href="{{ route('publications.show', $publication) }}">Baca ringkasan</a></td></tr>@endforeach
</tbody></table></div>
@endif
</div>
@endsection
