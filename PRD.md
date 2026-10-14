# PRODUCT REQUIREMENT DOCUMENT (PRD)

## PROJEK: RINGKAS - Sistem Ringkasan & Manajemen Publikasi Statistik BPS

| Atribut | Detail |
| :--- | :--- |
| **Nama Aplikasi** | **RINGKAS** (Sistem Ringkasan & Manajemen Publikasi Statistik BPS) |
| **Status Dokumen** | Rilis / Final |
| **Pemilik Produk** | Badan Pusat Statistik (BPS) |
| **Target Pengguna** | Administrator BPS, Staf Analis, Masyarakat Umum (User Publik) |
| **Bahasa Utama** | Bahasa Indonesia |

---

## 1. Pendahuluan

### 1.1 Latar Belakang
Badan Pusat Statistik (BPS) menerbitkan berbagai dokumen publikasi statistik secara berkala (bulanan, tahunan, maupun hasil sensus sektoral) dalam format PDF. Sering kali, dokumen-dokumen tersebut memiliki ukuran yang besar dan jumlah halaman yang banyak, sehingga menyulitkan pembaca umum maupun analis kebijakan untuk mendapatkan intisari informasi secara cepat.

Aplikasi **RINGKAS** dikembangkan sebagai solusi untuk mendokumentasikan, mengelola, serta meringkas dokumen publikasi statistik BPS secara otomatis menggunakan teknologi kecerdasan buatan (AI) berbasis LLM (Large Language Model) Google Gemini.

### 1.2 Tujuan Produk
* **Otomatisasi Ekstraksi**: Mengekstrak teks dari file PDF secara efisien melalui metode server-side maupun client-side fallback.
* **Analisis Data Terstruktur**: Menyajikan analisis 9 bagian penting dari publikasi statistik secara otomatis guna memberikan informasi ringkas tanpa membaca seluruh halaman.
* **Manajemen Publikasi**: Menyediakan portal terpusat bagi administrator untuk mengunggah, memperbarui, serta memantau data publikasi.
* **Akses Publik yang Mudah**: Menyediakan antarmuka pencarian dan eksplorasi ringkasan publikasi yang cepat dan ramah pengguna umum.

---

## 2. Arsitektur Sistem & Alur Kerja Utama

### 2.1 Alur Kerja Ekstraksi PDF Dual-Mode (Hybrid Extraction)
Untuk menangani dokumen PDF yang terproteksi (encrypted), dipindai (scanned), atau memiliki batasan memori parsing pada server, aplikasi menerapkan alur dual-mode:

```mermaid
graph TD
    A[Admin Mengunggah PDF] --> B{Server Parsing: Smalot PdfParser}
    B -- Sukses (Text > 10 char) --> C[Simpan Teks & Kirim ke AI Summarizer]
    B -- Gagal / Terkunci (Text < 10 char) --> D[Ubah Status ke Pending]
    D --> E[Render Halaman Detail / Show]
    E --> F[Client-side Fallback: PDF.js di Browser]
    F --> G[Ekstrak Teks Halaman 1-8 di Browser]
    G --> H[POST Teks ke /publications/{id}/save-extracted-text]
    H --> C
```

1. **Server-Side Extraction (Primer)**: Menggunakan library `Smalot\PdfParser` untuk mengekstrak seluruh teks dan jumlah halaman langsung pada saat berkas diunggah.
2. **Client-Side Fallback (Sekunder)**: Apabila teks hasil ekstraksi server kurang dari 10 karakter (indikasi file terkunci atau gagal parse), sistem mengubah status menjadi *Pending*. Ketika admin membuka halaman detail, script berbasis `PDF.js` di browser akan memproses file secara lokal, mengekstrak teks dari 8 halaman pertama secara aman, kemudian mengirimkannya kembali ke server menggunakan API POST untuk diproses oleh AI Summarizer.

### 2.2 Integrasi AI Summarizer & Fallback Heuristik
Proses perumusan analisis AI berjalan secara otomatis setelah teks berhasil diekstrak melalui salah satu metode di atas:

* **Mode Utama (Gemini API)**: Mengirimkan instruksi dan teks (maksimal 40.000 karakter pertama) ke **Google Gemini API** (secara default menggunakan model `gemini-1.5-flash` atau model terbaru yang dikonfigurasi melalui `.env`). Respons yang diminta diformat dalam skema JSON terstruktur.
* **Mode Fallback Lokal (Heuristic Engine)**: Jika API Key Gemini tidak disetel atau terjadi kegagalan jaringan, sistem secara otomatis beralih ke mesin heuristik lokal (`summarizeLocally`). Mesin ini menggunakan templat teks berdasarkan kategori publikasi BPS dan ekspresi reguler (Regex) untuk mendeteksi wilayah administratif, indikator angka, tren, dan kesimpulan secara dinamis.

---

## 3. Persyaratan Fungsional

### 3.1 Portal Otentikasi & Manajemen Akun
Sistem mendukung dua peran pengguna utama yang diatur dalam Middleware:

1. **Role: Admin (`role:admin`)**
   * Mengakses Dashboard Administrator.
   * Melakukan pengelolaan CRUD dokumen publikasi.
   * Memicu regenerasi analisis AI secara manual.
   * Memantau dan membersihkan log aktivitas/notifikasi sistem.
   * Mengelola akun pengguna (CRUD Users).
   * Memperbarui pengaturan akun admin pribadi (Nama, Username, Password).

2. **Role: User Umum (`role:user`)**
   * Mendaftar mandiri melalui halaman registrasi publik.
   * Mengakses beranda ringkasan publikasi.
   * Melakukan pencarian publikasi berdasarkan judul, kategori, tahun rilis, dan wilayah.
   * Membaca analisis 9 bagian publikasi secara interaktif.
   * Mengunduh dokumen fisik PDF.
   * Memperbarui informasi profil pengguna.

---

### 3.2 Pengelolaan Publikasi (Sisi Admin)
* **Unggah Dokumen**: Mengunggah berkas PDF (batas maksimal ukuran default 25 MB).
* **Metadata Input**: Admin menginput Judul, Kategori Publikasi, dan Tanggal Rilis. Kategori terbatas pada:
  * *Publikasi Umum*
  * *Statistik Sosial*
  * *Statistik Ekonomi*
  * *Statistik Pertanian*
  * *Statistik Industri*
  * *Statistik Distribusi*
  * *Statistik Lingkungan*
  * *Sensus & Survei*
* **Kalkulasi Otomatis**: Menghitung kapasitas file dan estimasi jumlah halaman pada saat upload secara dinamis.
* **Regenerasi AI**: Admin dapat memicu tombol "Generate Ulang AI" di halaman detail jika ingin memproses kembali dokumen menggunakan prompt LLM terbaru.

---

### 3.3 Hasil Analisis AI Terstruktur (9 Bagian Utama)
Halaman detail publikasi menyajikan 9 bagian analisis terstruktur yang diekstrak oleh AI:

1. **Ringkasan Eksekutif (Executive Summary)**: Ditulis dalam 3 paragraf mendalam dan formal (Paragraf 1: Gambaran umum; Paragraf 2: Temuan data/indikator menonjol; Paragraf 3: Relevansi/manfaat bagi pembuat kebijakan).
2. **Informasi Publikasi (Publication Information)**: Kartu informasi yang merangkum Judul, Tahun Rilis, Wilayah Cakupan, Kategori, Jumlah Halaman, Ukuran Berkas, dan Tanggal Diunggah.
3. **Topik Utama (Main Topics)**: Daftar topik sektoral utama yang dibahas di dalam dokumen.
4. **Kata Kunci (Keywords)**: Tag/kata kunci penting untuk optimasi pencarian indeks dokumen.
5. **Poin-Poin Penting (Key Points)**: Daftar butir (bullet-points) temuan kunci yang memuat angka statistik krusial.
6. **Indikator Statistik (Statistical Indicators)**: Tabel berisikan nama indikator (contoh: IPM, Laju Inflasi), nilai angka, dan satuan unit (%, jiwa, rupiah, dll).
7. **Tren & Arah Perkembangan (Trends & Directions)**: Analisis tren visual berupa indikator, arah tren (Meningkat/Menurun/Stabil), disertai ikon representatif (📈 / 📉 / ➖).
8. **Lokasi Pembahasan (Discussion Locations / Page Mapping)**: Pemetaan otomatis yang mencocokkan topik bahasan utama dengan nomor halaman kemunculannya pada PDF untuk navigasi cepat.
9. **Kesimpulan Eksekutif (Conclusion)**: Satu paragraf konklusi akhir mengenai implikasi kebijakan serta rekomendasi perencanaan daerah ke depan.

---

### 3.4 Pusat Notifikasi & Log Aktivitas
* Sistem mencatat secara otomatis aktivitas krusial ke dalam tabel `notifications`, antara lain:
  * Pendaftaran pengguna baru.
  * Berhasil mengunggah dokumen (Status: Pending).
  * Berhasil melakukan ekstraksi teks & analisis AI.
  * Pembaruan data publikasi.
  * Penghapusan dokumen publikasi.
* Halaman Aktivitas Sistem menyajikan log ini secara kronologis dengan paginasi.
* Administrator dapat menandai semua notifikasi sebagai telah dibaca (`read-all`) atau menghapus riwayat log aktivitas secara permanen (`truncate`).

---

## 4. Arsitektur Data (Skema Database)

### 4.1 Tabel: `users`
Menyimpan data kredensial akun pengguna sistem.
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user', -- 'admin' atau 'user'
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### 4.2 Tabel: `publications`
Menyimpan informasi publikasi yang diunggah serta status pemrosesannya.
```sql
CREATE TABLE publications (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    year INT NOT NULL,
    release_date DATE NOT NULL,
    pdf_path VARCHAR(255) NOT NULL,
    region VARCHAR(255) NULL,
    page_count INT NULL,
    file_size VARCHAR(50) NULL,
    extracted_text LONGTEXT NULL,
    status VARCHAR(50) DEFAULT 'Pending', -- 'Pending' atau 'Selesai'
    uploaded_by VARCHAR(100) DEFAULT 'Admin BPS',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### 4.3 Tabel: `publications_ai_results`
Menyimpan hasil analisis terstruktur AI yang terhubung secara relasional (`one-to-one`) ke tabel publikasi.
```sql
CREATE TABLE publications_ai_results (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    publication_id BIGINT UNIQUE NOT NULL,
    summary TEXT NOT NULL,
    publication_information JSON NOT NULL, -- Menyimpan metadata hasil ekstraksi AI
    topics JSON NOT NULL,                  -- Array dari topik
    keywords JSON NOT NULL,                -- Array dari kata kunci
    key_points JSON NOT NULL,              -- Array dari poin penting
    indicators JSON NOT NULL,              -- Objek list nama, value, unit
    trends JSON NOT NULL,                  -- Objek list indicator, tren, icon
    discussion_locations JSON NOT NULL,    -- Objek list topic dan halaman
    conclusion TEXT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (publication_id) REFERENCES publications(id) ON DELETE CASCADE
);
```

### 4.4 Tabel: `notifications`
Menyimpan catatan riwayat aktivitas sistem untuk dipantau admin.
```sql
CREATE TABLE notifications (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) DEFAULT 'info', -- 'upload', 'delete', 'summary', 'success', dll
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

## 5. Persyaratan Non-Fungsional

### 5.1 Performa & Penanganan File Besar
* **Pencegahan Connection Timeout**: File PDF berukuran besar dapat memicu timeout pemrosesan php-fpm. Oleh karena itu, server dibatasi hanya melakukan parsing parsial (maksimal 8 halaman pertama untuk file berukuran > 5 MB) pada backend Laravel, sedangkan parser browser (PDF.js) digunakan sebagai fallback paralel.
* **Timeout Koneksi API**: Integrasi API HTTP ke Google Gemini menggunakan limit timeout 45 detik untuk menghindari bottleneck thread.

### 5.2 Keamanan Data & Proteksi Hak Cipta
* **Akses Middleware**: Seluruh rute administratif dilindungi oleh middleware `role:admin`.
* **Penyimpanan Berkas**: File PDF disimpan di direktori internal `/storage/app/public/publications` yang hanya diekspos melalui symlink publik terenkripsi.

### 5.3 Estetika & Responsivitas Antarmuka
* **Premium Design System**: Desain antarmuka menggunakan palet warna korporat formal BPS (Navy Blue, Light Blue, Soft Orange) yang modern dengan perpaduan efek glassmorphism dan micro-animations.
* **Ikonografi Dinamis**: Menampilkan visualisasi tren statistik menggunakan simbol emoji/warna yang memudahkan pemindaian sekilas.
* **Cover Dinamis (Mockup)**: Jika PDF.js gagal memuat sampul buku asli di halaman depan, sistem secara cerdas menampilkan visualisasi CSS fallback berupa layout buku bergaya modern yang memuat judul dan kategori secara dinamis.

---

## 6. Rencana Pengembangan Masa Depan (Future Roadmap)

1. **Ekstraksi Tabel Menggunakan Vision AI**: Dukungan untuk tidak hanya membaca teks, melainkan membaca tabel-tabel data kompleks di dalam PDF menggunakan model multimodal (seperti Gemini 2.5 Pro Vision) dan mengonversinya menjadi berkas Excel/CSV yang dapat diunduh.
2. **Visualisasi Grafik Otomatis**: Membuat visualisasi bagan (chart/graph) interaktif berbasis javascript (misalnya Chart.js) langsung dari data tabel indikator yang diekstrak oleh AI.
3. **Pencarian Semantik (Vector Search)**: Menggantikan pencarian teks SQL `LIKE` tradisional dengan pencarian semantik (Semantic Search) berbasis embedding vector sehingga pengguna dapat mengajukan pertanyaan bebas (seperti chatbot) ke dokumen publikasi.
