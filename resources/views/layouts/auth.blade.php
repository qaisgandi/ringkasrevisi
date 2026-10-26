<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>@yield('title') — RINGKAS</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="rk-auth">
    <aside class="rk-auth-aside">
        <div class="rk-auth-top"><x-brand /><span class="rk-auth-top-label">RUANG BACA STATISTIK</span></div>
        <div class="rk-auth-content">
            <p class="rk-auth-kicker"><span></span>RINGKAS / INFORMASI PUBLIK</p>
            <h2>Informasi statistik.<br>Perspektif yang<br><em>lebih utuh.</em></h2>
            <p>Temukan publikasi, baca ringkasan, dan pahami konteks data melalui RINGKAS.</p>
            <div class="rk-auth-highlights" aria-label="Fitur RINGKAS">
                <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5z"/><path d="M4 5.5v16M8 7h8M8 11h8"/></svg>Publikasi statistik</span>
                <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h6"/></svg>Ringkasan terstruktur</span>
                <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 11.5a8.5 8.5 0 0 1-12.3 7.6L3 21l1.9-5.2A8.5 8.5 0 1 1 21 11.5Z"/><path d="M8 11h.01M12 11h.01M16 11h.01"/></svg>Tanya jawab kontekstual</span>
            </div>
            <div class="rk-auth-preview" aria-hidden="true">
                <div class="rk-auth-preview-head"><span><i></i><i></i><i></i></span><small>RUANG BACA / RINGKAS</small></div>
                <div class="rk-auth-preview-body">
                    <div class="rk-auth-doc-icon"><svg viewBox="0 0 24 24"><path d="M6 3h8l5 5v13H6z"/><path d="M14 3v5h5M9 13h7M9 17h5"/></svg></div>
                    <div class="rk-auth-doc-copy"><strong>Publikasi statistik</strong><small>Dokumen sumber · PDF</small></div>
                    <span class="rk-auth-doc-status">SIAP DIBACA</span>
                </div>
                <div class="rk-auth-summary"><span>RINGKASAN</span><i></i><i></i><i></i></div>
            </div>
        </div>
        <small class="rk-auth-copyright">Sistem Ringkasan Publikasi Statistik BPS</small>
    </aside>
    <main class="rk-auth-main">
        <div class="rk-auth-form">
            <a href="{{ route('landing') }}" class="rk-auth-back"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6M9 12h12"/></svg>Kembali ke beranda</a>
            <section class="rk-auth-card">
                <div class="rk-auth-mark"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/><path d="m9 12 2 2 4-4"/></svg></div>
                <p class="rk-auth-form-kicker">AKSES TERKENDALI</p>
                <h1>@yield('title')</h1>
                <p>@yield('description')</p>
                <x-alerts />
                @yield('content')
                <div class="rk-auth-security"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3M12 14v3"/></svg>Informasi akun Anda dilindungi selama sesi masuk.</div>
            </section>
        </div>
    </main>
</body></html>
