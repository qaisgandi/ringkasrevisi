# Penguatan keamanan RINGKAS

Diterapkan pada 13 Oktober 2026 di `D:\capstone1\RINGKAS-main`. Ini merupakan penguatan aplikasi lokal dan konfigurasi deployment; tidak ada deployment ke hosting publik pada pekerjaan ini.

## Perbaikan yang diterapkan

| Temuan | Perbaikan |
|---|---|
| Endpoint baca semua notifikasi tidak memiliki pembatasan peran | Sekarang wajib admin; tamu mendapat 401 dan pengguna biasa mendapat 403 untuk JSON |
| Tidak ada pembatasan login/AI | Login 5/menit per kombinasi username/IP dan 30/menit per IP, dibagi antarportal; registrasi 5/jam per IP; chat 10/menit dan 100/jam per akun; pemrosesan 3/menit per admin; pembaruan profil/password 5/menit |
| Seluruh proxy dipercaya | Default tidak mempercayai forwarded headers; `TRUSTED_PROXIES` menerima daftar alamat proxy resmi dari operator |
| Password pengguna dapat diubah tanpa password lama | Wajib password saat ini; celah melalui form edit akun admin sendiri juga ditutup |
| Aturan password baru hanya 6–8 karakter | Password baru minimal 12, maksimal 128 karakter; password lama tetap dapat dipakai login |
| Sesi lama tetap dapat bertahan setelah password berubah | Middleware pemeriksaan hash sesi, rotasi sesi dan remember token; password sensitif tidak dimasukkan ke old input |
| PDF disimpan pada disk publik | Unggahan baru memakai disk privat dan nama UUID dengan ekstensi tetap `.pdf`; baca/unduh melalui route yang memerlukan login |
| Ekstensi nama file berasal dari pengguna | Validasi MIME, ekstensi PDF, dan ukuran 25 MB pada unggah maupun penggantian; ukuran/jumlah halaman saat unggah dihitung server |
| Request riwayat chat dan hasil ekstraksi tidak dibatasi memadai | Batas jumlah/ukuran riwayat, struktur pesan, jumlah halaman, teks gabungan, body request, dan memori decompression parser |
| PDF.js 2.16.105 memiliki advisory eksekusi JavaScript | Diganti PDF.js 6.4.299 yang dibundel lokal, termasuk worker; eval dan scripting dimatikan; aset PDF hanya dimuat pada halaman terkait |
| HTML hasil formatter chatbot membutuhkan pertahanan tambahan | DOMPurify menyaring hasil sebelum masuk DOM; interpolasi username menggunakan encoding JavaScript aman |
| Gemini menonaktifkan verifikasi TLS, key dalam URL | Verifikasi TLS aktif; API key dipindahkan ke header; error layanan tidak mencetak detail sensitif ke pengguna/log aplikasi |
| Debug aktif di konfigurasi produksi dan entrypoint Vercel | Debug produksi dimatikan, error bootstrap tidak ditampilkan, cookie produksi Secure/HttpOnly; cache Vercel memakai database terlebih dahulu dan fallback ke cache lokal saat database cache gagal |
| Belum ada kebijakan header browser | CSP, nosniff, pembatasan framing, referrer policy, permissions policy, no-store untuk respons akun; HSTS pada HTTPS |
| Lookup wilayah mengirim IP ke endpoint HTTP | Dinonaktifkan secara default; wilayah publik menjadi “Tidak diketahui” selama integrasi lama ini nonaktif |
| Dependensi memiliki advisory keamanan | Paket npm terdampak diperbarui; Laravel, Guzzle, CommonMark, Flysystem dan dependensi terkait diperbarui |

Akses admin tetap mengikuti peran akun. Registrasi tetap memaksa role user meski payload menyertakan role admin. CSRF bawaan Laravel dipertahankan dan diuji dengan middleware CSRF yang benar-benar aktif dalam tes.

Pengujian juga menemukan bug lama pada notifikasi hasil ekstraksi: kolom `type` tidak diisi. Kolom ini sekarang diisi `summary` agar alur penyimpanan hasil tidak gagal.

## Penyimpanan PDF

Route baru `/publications/{publication}/file` melayani PDF privat setelah autentikasi; URL preview, thumbnail, dan tombol baca telah disesuaikan. Route download tetap tersedia. Respons file memeriksa pola path dan mempunyai MIME PDF, nosniff, no-store, serta CSP sandbox. Aturan Apache menolak akses langsung `/storage/publications/`.

Perintah migrasi disediakan untuk deployment yang memiliki file lama:

```powershell
& D:\PHP\php.exe artisan security:privatize-pdfs
& D:\PHP\php.exe artisan security:privatize-pdfs --apply
```

Mode default hanya memeriksa. `--apply` menyalin ke disk privat, memverifikasi SHA-256, lalu menghapus salinan publik. Path di database tidak berubah. Bila hash berbeda, sumber dipertahankan dan command gagal. Nama tidak sesuai pola memerlukan pemeriksaan manual dan menghasilkan exit gagal. Di workspace ini command telah dijalankan dan menemukan **0 file PDF publik**; tidak ada dokumen pengguna yang dipindahkan atau dihapus.

## Versi dependensi utama sesudah pembaruan

- Laravel: 13.35.0 (sebelumnya 13.21.1).
- Guzzle: 7.15.5 (sebelumnya 7.15.1); tetap pada major 7.
- CommonMark: 2.10.3 (sebelumnya 2.8.3).
- Flysystem: 3.36.0 (sebelumnya 3.35.2).
- PDF.js: 6.4.299; DOMPurify dibundel lokal melalui npm.

## Hasil verifikasi

- **34 tes lulus, 200 assertion**, termasuk 20 tes keamanan baru dan 14 tes sebelumnya. Database tes SQLite in-memory; pengujian tidak membuat akun operasional.
- Tes mencakup penolakan mutasi admin, endpoint notifikasi, pemalsuan forwarded IP, pembatasan login/chat, role registrasi, password lama, invalidasi sesi usang, kebocoran old input, akses PDF, traversal path, ekstensi PHP/isi PDF palsu/ukuran berlebihan, body request, payload ekstraksi yang sah, CSRF, header keamanan, penggunaan header API key dan verifikasi TLS, serta migrasi file.
- `npm run build`: berhasil. Modul PDF dipisah; aplikasi utama sekitar 372 KB dan modul PDF sekitar 431 KB sebelum gzip. Ada pemberitahuan nonfatal tentang paket font fallback opsional `fontaine` yang tidak terpasang.
- `php artisan view:cache`: berhasil.
- PHP lint: **39 file, 0 error**.
- `npm audit`: **0 advisory** yang ditemukan.
- `composer audit --locked`: **0 advisory**, tidak ada paket abandoned.
- Browser smoke test memakai PDF sintetis satu halaman: render berhasil dengan worker lokal; payload event handler dan URL JavaScript dihapus oleh sanitasi. Lihat `browser-smoke.jpg`.
- Halaman registrasi aktual menampilkan minimal 12 karakter; tombol tampil/sembunyikan password berhasil diperiksa.
- Koneksi HTTPS Guzzle ke endpoint publik Google berhasil dengan verifikasi sertifikat aktif. Respons root endpoint adalah HTTP 404 yang diharapkan; **tidak mengirim API key**. Respons Gemini nyata belum diuji, sedangkan tes integrasi menggunakan mock HTTP.

## Konfigurasi saat deployment

1. Gunakan HTTPS dan `APP_ENV=production`, `APP_DEBUG=false`, serta APP_URL yang tepat. Cookie Secure aktif pada produksi. Jangan gunakan HTTP biasa untuk sesi produksi.
2. Pastikan tabel `cache` dari migrasi Laravel tersedia di database produksi agar rate limit login tetap konsisten di semua instance. Vercel memakai cache database terlebih dahulu dan beralih ke `array` jika database cache tidak tersedia; fallback ini mencegah error login, tetapi pembatasan tidak lagi dibagi antar-instance sampai cache bersama pulih.
3. Isi `TRUSTED_PROXIES` dengan IP/CIDR proxy yang benar. Jangan menggunakan `*` pada server yang dapat diakses langsung dari internet.
4. Jalankan migrasi file lama di lingkungan tujuan. Untuk Nginx/CDN tambahkan aturan penolakan `/storage/publications/` dan hapus cache salinan publik lama; aturan `.htaccess` hanya berlaku untuk Apache.
5. Lakukan build sebelum menjalankan versi baru karena manifest sekarang mencakup `resources/js/pdf.js`.
6. Jangan aktifkan kembali lookup geolokasi HTTP untuk produksi; gunakan integrasi HTTPS yang sesuai bila statistik wilayah diperlukan.

## Batas perlindungan

CSP masih mengizinkan inline script/style karena Blade lama menggunakan handler inline. Ini memperkecil sumber script yang boleh dimuat tetapi belum merupakan CSP ketat berbasis nonce. DOMPurify, escaping, dan validasi tetap diperlukan.

Validasi PDF dan pembaruan parser bukan pemindaian antivirus maupun isolasi proses. Untuk menerima dokumen dari pihak tak tepercaya dalam skala besar, pemindaian malware dan worker terisolasi adalah pekerjaan lanjutan. Hasil ini juga bukan sertifikasi “100% aman” atau pentest terhadap hosting publik.

Aturan server/CDN, sertifikat HTTPS, dan kredensial deployment perlu disesuaikan di lingkungan hosting. Tidak ada API key yang diganti, dipublikasikan, atau dicantumkan dalam laporan.

## Menjalankan dan memeriksa ulang

```powershell
Set-Location D:\capstone1\RINGKAS-main
npm run build
& D:\PHP\php.exe artisan test
& D:\PHP\php.exe artisan view:cache
npm audit
& D:\PHP\php.exe -d extension=curl storage/app/security-tools/composer.phar audit --locked
& D:\PHP\php.exe artisan serve --host=127.0.0.1 --port=8000
```

Server aplikasi lokal yang sudah berjalan dapat digunakan di http://127.0.0.1:8000. Composer PHAR untuk audit diunduh dari sumber resmi dan checksum SHA-256 diverifikasi; tidak dipasang secara global. Backup sumber sebelum penguatan berada di `D:\capstone1\security-backup-20261013`. Pengaturan `.env` lokal yang berubah adalah `APP_DEBUG=false`; backup tidak menyalin rahasia `.env`.

## Sumber teknis

- [Laravel rate limiting](https://laravel.com/framework/docs/13.x/routing#rate-limiting).
- [Advisory PDF.js 2024](https://github.com/mozilla/pdf.js/security/advisories/GHSA-wgrm-67xf-hhpq).
- [Advisory PDF.js 2026](https://github.com/mozilla/pdf.js/security/advisories/GHSA-hq66-cqwq-w95j).
- [Dokumentasi API key Gemini](https://ai.google.dev/gemini-api/docs/api-key).
- [Distribusi Composer resmi](https://getcomposer.org/download/).

## File sumber yang berubah/ditambahkan

- `app/Http/Controllers/ChatbotController.php`
- `app/Http/Controllers/LoginController.php`
- `app/Http/Controllers/PublicationController.php`
- `app/Http/Controllers/PublicationFileController.php`
- `app/Http/Controllers/UserAuthController.php`
- `app/Http/Controllers/UserController.php`
- `app/Http/Middleware/RoleMiddleware.php`
- `app/Http/Middleware/SecurityHeaders.php`
- `app/Http/Requests/StorePublicationRequest.php`
- `app/Providers/AppServiceProvider.php`
- `app/Services/AiSummarizerService.php`
- `app/Services/GeminiChatService.php`
- `app/Services/IpGeolocationService.php`
- `app/Services/PdfExtractionService.php`
- `bootstrap/app.php`
- `config/filesystems.php`
- `config/security.php`
- `config/services.php`
- `config/session.php`
- `routes/console.php`
- `routes/web.php`
- `resources/js/app.js`
- `resources/js/pdf.js`
- `resources/views/account/settings.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/publications/index.blade.php`
- `resources/views/admin/publications/show.blade.php`
- `resources/views/admin/users/create.blade.php`
- `resources/views/admin/users/edit.blade.php`
- `resources/views/components/chatbot.blade.php`
- `resources/views/user/home.blade.php`
- `resources/views/user/profile.blade.php`
- `resources/views/user/register.blade.php`
- `resources/views/user/show_publication.blade.php`
- `tests/Feature/SecurityTest.php`
- `api/index.php`
- `public/index.php`, `public/.htaccess`: error bootstrap dan aturan akses file.
- `package.json`, `package-lock.json`, `composer.lock`: dependensi patched.
- `vite.config.js`: entrypoint PDF terpisah.
- `vercel.json`, `.env.example`, `.env`: default deployment dan debug.
