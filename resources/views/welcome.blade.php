<!DOCTYPE html>
<html lang="id"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Jelajahi publikasi statistik BPS, baca ringkasan terstruktur, dan telusuri konteks data bersama RINGKAS.">
<title>RINGKAS — Pahami publikasi statistik</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script src="https://unpkg.com/lucide@latest"></script>

</head><body class="rk-public">
<a class="rk-skip" href="#konten">Lewati navigasi</a>
<header class="rk-nav"><div class="rk-wrap rk-nav-inner">
<x-brand />
<button type="button" class="rk-nav-toggle" data-nav-toggle aria-controls="public-nav" aria-expanded="false">Menu</button>
<nav class="rk-nav-links" id="public-nav" aria-label="Navigasi utama"><a href="#beranda">Beranda</a><a href="#cara-kerja">Cara Kerja</a><a href="#fitur">Fitur</a><a href="#contoh-hasil">Contoh Hasil</a><a href="#tentang">Tentang</a></nav>
<div class="rk-nav-actions"><a href="{{ route('login') }}">Masuk</a><a href="{{ route('register') }}" class="rk-btn">Daftar</a></div>
</div></header>
<main id="konten">
<section class="rk-hero" id="beranda"><div class="rk-wrap">
<p class="rk-kicker rk-hero-kicker"><i data-lucide="chart-no-axes-combined" aria-hidden="true"></i>Ruang baca publikasi statistik BPS</p>
<h1>Pahami data statistik,<br><em>lebih cepat dan lebih mudah.</em></h1>
<p class="rk-lead">Dari publikasi yang panjang, temukan informasi yang Anda butuhkan. RINGKAS membantu Anda membaca ringkasan terstruktur dan menelusuri kembali dokumen sumber.</p>
<div class="rk-actions"><a href="{{ route('login') }}" class="rk-btn">Jelajahi publikasi <i data-lucide="arrow-right" aria-hidden="true"></i></a><a href="#contoh-hasil" class="rk-btn rk-btn-secondary">Lihat contoh hasil</a></div>
<p class="rk-hero-note">Masuk untuk mengakses katalog, ringkasan, dan asisten tanya jawab.</p>
<div class="rk-demo" aria-label="Demonstrasi dokumen dan ringkasan">
<div class="rk-demo-toolbar"><strong>RINGKAS / Ruang baca</strong><span>Ilustrasi antarmuka · bukan data statistik resmi</span></div>
<div class="rk-demo-grid"><div class="rk-demo-source"><article class="rk-paper">
<small>DOKUMEN CONTOH / PDF</small><div class="rk-paper-rule"></div><h3>Membaca<br>statistik<br>kesejahteraan rakyat</h3><p>Contoh susunan publikasi tentang penduduk, pendidikan, dan kondisi sosial.</p><dl><div><dt>Format</dt><dd>Publikasi PDF</dd></div><div><dt>Topik</dt><dd>Statistik sosial</dd></div></dl>
</article></div><article class="rk-demo-result">
<p class="rk-kicker">Hasil yang lebih terstruktur</p><h3>Dari dokumen, menuju pemahaman.</h3><h4>Ikhtisar</h4><p>Ringkasan menyajikan pokok pembahasan publikasi agar pembaca dapat mengenali konteks sebelum menelusuri data lebih jauh.</p>
<ol><li><strong>Kenali topiknya.</strong> Lihat cakupan dan pokok pembahasan.</li><li><strong>Temukan indikatornya.</strong> Baca temuan utama dalam konteks publikasi.</li><li><strong>Periksa sumbernya.</strong> Kembali ke dokumen asli untuk memahami detail.</li></ol>
<p class="rk-source-note">Contoh struktur tampilan. Isi ringkasan sebenarnya mengikuti publikasi yang tersedia dan hasil pemrosesan AI.</p>
</article></div></div></div></section>
<section class="rk-section" id="cara-kerja"><div class="rk-wrap"><div class="rk-section-heading"><div><p class="rk-kicker rk-area-kicker"><i data-lucide="route" aria-hidden="true"></i>01 / Cara kerja</p><h2>Dari mencari referensi<br>hingga memahami isinya.</h2></div><p>Administrator menyiapkan publikasi dan memproses ringkasannya. Anda dapat menjelajahi dokumen yang tersedia melalui akun RINGKAS.</p></div>
<ol class="rk-steps">
<li><span class="rk-step-marker"><i data-lucide="log-in" aria-hidden="true"></i><span class="rk-step-number">01</span></span><h3>Masuk ke RINGKAS</h3><p>Gunakan akun Anda atau daftar untuk mengakses katalog publikasi.</p></li>
<li><span class="rk-step-marker"><i data-lucide="search" aria-hidden="true"></i><span class="rk-step-number">02</span></span><h3>Temukan publikasi</h3><p>Cari judul, tahun, wilayah, atau kategori yang sesuai dengan kebutuhan Anda.</p></li>
<li><span class="rk-step-marker"><i data-lucide="align-left" aria-hidden="true"></i><span class="rk-step-number">03</span></span><h3>Baca ringkasannya</h3><p>Pelajari ikhtisar, topik, serta indikator pada publikasi yang sudah diproses.</p></li>
<li><span class="rk-step-marker"><i data-lucide="external-link" aria-hidden="true"></i><span class="rk-step-number">04</span></span><h3>Telusuri lebih jauh</h3><p>Tanyakan isi publikasi kepada asisten AI dan periksa kembali dokumen PDF sumber.</p></li>
</ol></div></section>
<section class="rk-section" id="fitur"><div class="rk-wrap rk-features"><div><p class="rk-kicker rk-area-kicker"><i data-lucide="sparkles" aria-hidden="true"></i>02 / Fitur RINGKAS</p><h2>Lebih dekat dengan data.<br>Lebih jelas konteksnya.</h2><p class="rk-muted" style="margin-top:24px;font-size:15px;line-height:1.8">Untuk mahasiswa, peneliti, dan siapa pun yang ingin memahami publikasi statistik.</p></div><div class="rk-feature-list">
<article><i data-lucide="file-text" aria-hidden="true"></i><div><h3>Ringkasan yang terstruktur</h3><p>Ikhtisar, pokok pembahasan, kata kunci, dan kesimpulan membantu Anda menavigasi isi publikasi.</p></div></article>
<article><i data-lucide="messages-square" aria-hidden="true"></i><div><h3>Tanya jawab dalam konteks</h3><p>Asisten AI dapat menggunakan konteks katalog atau publikasi yang sedang Anda baca. Cocokkan jawaban dengan sumber sebelum menggunakannya.</p></div></article>
<article><i data-lucide="book-open" aria-hidden="true"></i><div><h3>Dokumen sumber tetap tersedia</h3><p>Buka atau unduh publikasi asli untuk memeriksa tabel, definisi, dan detail yang tidak tercakup dalam ringkasan.</p></div></article>
<article><i data-lucide="search" aria-hidden="true"></i><div><h3>Eksplorasi sesuai kebutuhan</h3><p>Jelajahi publikasi menurut kategori, mulai dari statistik sosial dan ekonomi hingga sensus dan survei.</p></div></article>
</div></div></section>
<section class="rk-section" id="kategori-publikasi"><div class="rk-wrap"><div class="rk-section-heading"><div><p class="rk-kicker rk-area-kicker"><i data-lucide="folders" aria-hidden="true"></i>Katalog publik</p><h2>Jelajahi publikasi<br>menurut kategorinya.</h2></div><p>Pilih kategori untuk melihat koleksi yang tersedia. Masuk ke portal pembaca untuk membuka ringkasan dan dokumen sumber.</p></div>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
@foreach($categories as $category)
<a href="{{ route('home', ['category' => $category['name']]) }}" class="group flex min-h-28 items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md">
<span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $category['classes'] }}"><i data-lucide="{{ $category['icon'] }}" aria-hidden="true"></i></span>
<span class="min-w-0"><strong class="block text-sm leading-snug text-slate-800">{{ $category['name'] }}</strong><small class="mt-1 block text-xs text-slate-500">{{ $category['count'] }} publikasi</small></span>
</a>
@endforeach
</div></div></section>
<section class="rk-section rk-example" id="contoh-hasil"><div class="rk-wrap"><div class="rk-section-heading"><div><p class="rk-kicker rk-area-kicker"><i data-lucide="file-check-2" aria-hidden="true"></i>03 / Contoh hasil</p><h2>Baca gambaran besarnya.<br>Tetap dekat dengan sumber.</h2></div><p>Berikut ilustrasi cara informasi ditata ulang. Contoh ini menjelaskan format, tanpa menyajikan angka atau temuan sebagai data resmi.</p></div>
<div class="rk-comparison"><div><h3><i data-lucide="file-text" aria-hidden="true"></i>Dokumen publikasi</h3><p>Publikasi statistik dapat memuat penjelasan konsep, metodologi, tabel, dan uraian per topik. Pembaca perlu memahami cakupan wilayah, periode, serta definisi indikator agar tidak keliru menafsirkan data.</p><p style="margin-top:18px">Dokumen asli menjadi rujukan untuk memeriksa angka, satuan, dan catatan yang menyertai setiap tabel.</p></div><div><h3><i data-lucide="list-checks" aria-hidden="true"></i>Susunan hasil ringkasan</h3><dl><dt>Ikhtisar</dt><dd>Gambaran isi dan cakupan publikasi.</dd><dt>Temuan utama</dt><dd>Poin penting dari hasil analisis dokumen.</dd><dt>Indikator</dt><dd>Informasi indikator yang ditemukan dalam publikasi.</dd><dt>Rujukan</dt><dd>Lokasi pembahasan apabila tersedia dalam hasil analisis.</dd></dl></div></div>
<p class="rk-form-note" style="margin-top:18px">Hasil AI dapat mengandung kekeliruan. Gunakan publikasi asli sebagai rujukan utama.</p></div></section>
<section class="rk-section" id="tentang"><div class="rk-wrap rk-about"><div><p class="rk-kicker rk-area-kicker"><i data-lucide="landmark" aria-hidden="true"></i>04 / Tentang RINGKAS</p><h2>Statistik yang lebih<br>mudah didekati.</h2></div><div><p>RINGKAS adalah sistem ringkasan publikasi statistik BPS yang membantu pembaca memahami dokumen melalui pemrosesan teks dan kecerdasan buatan.</p><p>Ringkasan berfungsi sebagai pintu masuk untuk eksplorasi. Konteks, metodologi, dan rincian dalam publikasi asli tetap menjadi bagian penting dari proses membaca data.</p><a href="https://www.bps.go.id" class="rk-link" target="_blank" rel="noopener noreferrer">Kunjungi portal resmi BPS ↗</a></div></div></section>
<section class="rk-final-cta"><div class="rk-wrap"><div><h2>Mulai dari satu publikasi.</h2><p>Temukan referensi yang relevan, lalu pelajari informasi di dalamnya.</p></div><a href="{{ route('register') }}" class="rk-btn">Buat akun RINGKAS <i data-lucide="arrow-right" aria-hidden="true"></i></a></div></section>
</main>@include('layouts.footer')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const button = document.querySelector('[data-nav-toggle]');
    const menu = document.getElementById('public-nav');
    button?.addEventListener('click', () => {
        const open = menu?.classList.toggle('is-open') ?? false;
        button.setAttribute('aria-expanded', String(open));
    });
    if (window.lucide) window.lucide.createIcons();
});
</script>
</body></html>
