@php
    $editing = isset($publication);
    $categories = ['Publikasi Umum','Statistik Sosial','Statistik Ekonomi','Statistik Pertanian','Statistik Industri','Statistik Distribusi','Statistik Lingkungan','Sensus & Survei'];
@endphp
<div class="rk-page-heading"><div><a href="{{ route('publications.index') }}" class="rk-link text-sm">← Daftar publikasi</a><h1 class="mt-4">{{ $editing ? 'Edit Publikasi' : 'Unggah Publikasi' }}</h1><p>{{ $editing ? 'Perbarui metadata atau ganti dokumen sumber publikasi.' : 'Tambahkan dokumen sumber sebelum memproses ringkasan.' }}</p></div></div>
<x-alerts />
<div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_260px] gap-8">
<form action="{{ $editing ? route('publications.update', $publication) : route('publications.store') }}" method="POST" enctype="multipart/form-data" class="rk-panel rk-form p-6 md:p-8" data-publication-upload data-submit-state @if(config('services.vercel_blob.enabled') || config('services.vercel_blob.serverless')) data-blob-upload-url="{{ url('/api/blob-upload') }}" data-upload-token-url="{{ route('publications.upload-token') }}" @endif>
@csrf @if($editing) @method('PUT') @endif
<input type="hidden" name="pdf_blob_url" value="{{ old('pdf_blob_url') }}">
<input type="hidden" name="uploaded_file_size" value="{{ old('uploaded_file_size') }}">
<div class="rk-field"><label for="title">Judul publikasi</label><input id="title" name="title" value="{{ old('title', $publication->title ?? '') }}" required maxlength="255" placeholder="Judul lengkap sesuai dokumen sumber">@error('title')<p class="rk-field-error">{{ $message }}</p>@enderror</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
<div class="rk-field"><label for="category">Kategori</label><select id="category" name="category" required><option value="">Pilih kategori</option>@foreach($categories as $category)<option @selected(old('category', $publication->category ?? '') === $category)>{{ $category }}</option>@endforeach</select></div>
<div class="rk-field"><label for="release_date">Tanggal rilis publikasi</label><input id="release_date" name="release_date" type="date" value="{{ old('release_date', isset($publication) ? $publication->release_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" required></div>
</div>
<div class="rk-field"><label for="pdf">{{ $editing ? 'Ganti dokumen PDF (opsional)' : 'Dokumen PDF' }}</label>
<div class="rk-upload" data-drop-zone><i data-lucide="upload" aria-hidden="true"></i><strong>Pilih atau seret dokumen PDF</strong><p>Format PDF · maksimal 25 MB</p><input type="file" id="pdf" name="pdf" accept="application/pdf,.pdf" @required(!$editing) aria-describedby="file-feedback"><p id="file-feedback" role="status" aria-live="polite">Belum ada berkas dipilih.</p></div>
<p class="rk-field-error" data-file-error role="alert" hidden></p>
@if($editing)<p class="rk-form-note">Dokumen saat ini: {{ basename($publication->pdf_path) }}. Mengganti PDF akan menghapus hasil analisis lama dan mengembalikan status ke Pending. Ringkasan perlu diproses ulang.</p>@endif
@error('pdf')<p class="rk-field-error">{{ $message }}</p>@enderror
</div>
<div data-upload-progress hidden role="status"><progress class="w-full" max="100" aria-label="Unggahan sedang diproses"></progress><p class="rk-form-note" data-upload-status>Menyiapkan dokumen…</p></div>
<div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-6"><a href="{{ route('publications.index') }}" class="rk-btn rk-btn-secondary">Batal</a><button type="submit" class="rk-btn">{{ $editing ? 'Simpan perubahan' : 'Unggah publikasi' }}</button></div>
</form>
<aside class="text-sm text-slate-600 space-y-5"><h2 class="font-semibold text-bps-navy text-base">Dokumen yang siap dibaca</h2><p>Gunakan judul dan tanggal rilis sesuai publikasi asli agar referensi mudah ditemukan.</p><p>PDF dengan teks yang dapat dipilih lebih mudah diekstrak daripada dokumen hasil pemindaian.</p><div class="border-t border-slate-200 pt-5"><h3 class="font-semibold text-bps-navy mb-2">Setelah diunggah</h3><p>Publikasi disimpan dengan status Pending. Jalankan ekstraksi dari daftar publikasi untuk menghasilkan ringkasan.</p></div></aside>
</div>
