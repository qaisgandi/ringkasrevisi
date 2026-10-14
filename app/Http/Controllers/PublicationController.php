<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Notification;
use App\Http\Requests\StorePublicationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
    /**
     * Menampilkan daftar publikasi.
     * Mengamankan rute secara manual berbasis session.
     */
    public function index(Request $request)
    {

        $query = Publication::query();

        // Filter berdasarkan status jika ditentukan
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter berdasarkan kategori jika ditentukan
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        // Mengambil seluruh data publikasi, diurutkan dari yang terbaru diunggah
        $publications = $query->latest()->get();

        // Hitung statistik untuk dasbor ringkasan
        $totalPublications = Publication::count();
        $totalSelesai = Publication::where('status', 'Selesai')->count();
        $totalPending = Publication::where('status', '!=', 'Selesai')->count();
        $totalCategories = Publication::distinct('category')->count('category');

        $activeMenu = 'publications';
        $viewName = 'admin.publications.index';
        if ($request->input('status') === 'Selesai' && $request->routeIs('admin.summaries')) {
            $viewName = 'admin.summaries.index';
        }

        return view($viewName, [
            'publications' => $publications,
            'activeMenu' => $activeMenu,
            'totalPublicationsCount' => $totalPublications,
            'totalSelesaiCount' => $totalSelesai,
            'totalPendingCount' => $totalPending,
            'totalCategoriesCount' => $totalCategories,
        ]);
    }

    /**
     * Menampilkan form upload publikasi baru.
     */
    public function create()
    {
        return view('admin.publications.create', [
            'activeMenu' => 'publications'
        ]);
    }

    /**
     * Memproses penyimpanan berkas PDF publikasi dan metadatanya.
     */
    public function store(StorePublicationRequest $request)
    {
        @set_time_limit(180);
        try {
            // Mendapatkan berkas PDF
            $file = $request->file('pdf');
            if (!$file) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['pdf' => 'Gagal mengunggah berkas. Pastikan berkas PDF Anda valid dan ukuran tidak melebihi 25 MB.']);
            }
            
            // Membuat nama file yang unik dengan timestamp
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Menyimpan file ke storage/app/public/publications
            $pdfPath = $file->storeAs('publications', $filename, 'public');

            // Ekstraksi jumlah halaman dan ukuran file saat diupload
            $absolutePath = storage_path('app/public/' . $pdfPath);
            
            $fileBytes = @filesize($absolutePath) ?: 0;
            if ($fileBytes >= 1048576) {
                $calculatedSize = number_format($fileBytes / 1048576, 2) . ' MB';
            } elseif ($fileBytes >= 1024) {
                $calculatedSize = number_format($fileBytes / 1024, 2) . ' KB';
            } else {
                $calculatedSize = $fileBytes . ' B';
            }

            $fileSize = $request->input('file_size', $calculatedSize);
            $pageCount = $request->input('page_count');
            if (empty($pageCount) || !is_numeric($pageCount)) {
                try {
                    $extracted = app(\App\Services\PdfExtractionService::class)->extract($absolutePath);
                    $pageCount = $extracted['page_count'] ?? 1;
                } catch (\Exception $ex) {
                    $pageCount = 1;
                }
            }

            $releaseDate = $request->input('release_date');
            $year = date('Y', strtotime($releaseDate));

            // Menyimpan metadata ke database dengan status Pending (tidak melakukan ekstraksi otomatis)
            $publication = Publication::create([
                'title' => $request->input('title'),
                'category' => $request->input('category'),
                'year' => $year,
                'release_date' => $releaseDate,
                'pdf_path' => $pdfPath,
                'region' => null,
                'page_count' => $pageCount,
                'file_size' => $fileSize,
                'extracted_text' => null,
                'summary' => null,
                'topics' => null,
                'keywords' => null,
                'key_points' => null,
                'indicators' => null,
                'trends' => null,
                'page_locations' => null,
                'conclusion' => null,
                'status' => 'Pending',
                'uploaded_by' => 'Admin BPS', // Disimpan sebagai 'Admin BPS' karena session auth
            ]);

            // Catat Notifikasi
            Notification::create([
                'title' => 'Dokumen Diunggah',
                'message' => 'Berkas "' . $publication->title . '" berhasil diunggah (Status: Pending).',
                'type' => 'upload',
            ]);

            // Redirect kembali ke daftar publikasi dengan pesan sukses
            return redirect()
                ->route('publications.index')
                ->with('success', 'Publikasi berhasil diunggah. Silakan klik Ekstraksi untuk memproses.');
                
        } catch (\Exception $e) {
            // Hapus file jika terlanjur tersimpan di storage
            if (isset($pdfPath) && Storage::disk('public')->exists($pdfPath)) {
                Storage::disk('public')->delete($pdfPath);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['pdf' => 'Terjadi kesalahan sistem saat memproses berkas: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghapus publikasi dan berkas PDF terkait dari penyimpanan.
     */
    public function destroy(Publication $publication)
    {

        // Menghapus berkas fisik PDF dari storage jika ada
        if (Storage::disk('public')->exists($publication->pdf_path)) {
            Storage::disk('public')->delete($publication->pdf_path);
        }

        // Simpan judul untuk notifikasi
        $title = $publication->title;

        // Menghapus data publikasi dari database
        $publication->delete();

        // Catat Notifikasi
        Notification::create([
            'title' => 'Dokumen Dihapus',
            'message' => 'Berkas "' . $title . '" berhasil dihapus dari sistem.',
            'type' => 'delete',
        ]);

        return redirect()
            ->route('publications.index')
            ->with('success', 'Publikasi berhasil dihapus.');
    }

    /**
     * Memproses ekstraksi / ringkasan berkas PDF secara simulasi.
     */
    public function extract(Publication $publication)
    {
        @set_time_limit(180);
        
        $text = $publication->extracted_text;
        $pages = [];
        $pageCount = $publication->page_count;
        $fileSize = $publication->file_size;

        if (empty($text)) {
            // Jalankan ekstraksi secara manual dari file PDF
            $absolutePath = storage_path('app/public/' . $publication->pdf_path);
            $extracted = app(\App\Services\PdfExtractionService::class)->extract($absolutePath);
            $text = $extracted['text'];
            $pages = $extracted['pages'];
            $pageCount = $extracted['page_count'];
            $fileSize = $extracted['file_size'];
        }
        
        $aiResult = app(\App\Services\AiSummarizerService::class)->summarize(
            $publication->title,
            $publication->category,
            $publication->year,
            $text,
            $pages,
            $pageCount,
            $fileSize,
            $publication->created_at
        );

        // Ubah status publikasi menjadi selesai dan simpan metadata dasar
        $publication->update([
            'region' => $aiResult['publication_information']['region'] ?? $publication->region,
            'page_count' => $pageCount,
            'file_size' => $fileSize,
            'extracted_text' => $text,
            'status' => 'Selesai'
        ]);

        // Simpan hasil AI ke tabel publications_ai_results
        $publication->aiResult()->updateOrCreate(
            ['publication_id' => $publication->id],
            [
                'summary' => $aiResult['summary'],
                'publication_information' => $aiResult['publication_information'],
                'topics' => $aiResult['topics'],
                'keywords' => $aiResult['keywords'],
                'key_points' => $aiResult['key_points'],
                'indicators' => $aiResult['indicators'],
                'trends' => $aiResult['trends'],
                'discussion_locations' => $aiResult['discussion_locations'],
                'conclusion' => $aiResult['conclusion'],
            ]
        );

        // Catat Notifikasi
        Notification::create([
            'title' => 'Ekstraksi Sukses',
            'message' => 'Ekstraksi ringkasan PDF untuk "' . $publication->title . '" berhasil dilakukan.',
            'type' => 'summary',
        ]);

        return redirect()
            ->route('publications.show', $publication)
            ->with('success', 'Ekstraksi ringkasan dan analisis AI berhasil dilakukan.');
    }

    /**
     * Menampilkan detail hasil analisis AI untuk publikasi.
     */
    public function show(Publication $publication)
    {
        return view('admin.publications.show', [
            'publication' => $publication,
            'activeMenu' => 'publications'
        ]);
    }

    /**
     * Menampilkan form edit publikasi.
     */
    public function edit(Publication $publication)
    {
        return view('admin.publications.edit', [
            'publication' => $publication,
            'activeMenu' => 'publications'
        ]);
    }

    /**
     * Memproses pembaruan publikasi.
     */
    public function update(Request $request, Publication $publication)
    {
        @set_time_limit(180);

        // Validasi input
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:Publikasi Umum,Statistik Sosial,Statistik Ekonomi,Statistik Pertanian,Statistik Industri,Statistik Distribusi,Statistik Lingkungan,Sensus & Survei',
            'release_date' => 'required|date',
            'pdf' => 'nullable|file|mimes:pdf|max:25600', // Opsional, 25MB max
        ], [
            'title.required' => 'Judul publikasi wajib diisi.',
            'category.required' => 'Kategori publikasi wajib diisi.',
            'category.in' => 'Kategori yang dipilih tidak valid.',
            'release_date.required' => 'Tanggal rilis publikasi wajib diisi.',
            'release_date.date' => 'Format tanggal rilis tidak valid.',
            'pdf.mimes' => 'Format berkas harus berupa dokumen .pdf.',
            'pdf.max' => 'Ukuran berkas PDF maksimal 25 MB.',
        ]);

        $releaseDate = $request->input('release_date');
        $year = date('Y', strtotime($releaseDate));

        // Siapkan data untuk diupdate
        $data = [
            'title' => $request->input('title'),
            'category' => $request->input('category'),
            'year' => $year,
            'release_date' => $releaseDate,
        ];

        // Jika ada file PDF baru yang diunggah
        if ($request->hasFile('pdf')) {
            // Hapus file lama jika ada
            if (Storage::disk('public')->exists($publication->pdf_path)) {
                Storage::disk('public')->delete($publication->pdf_path);
            }

            // Simpan file baru
            $file = $request->file('pdf');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $data['pdf_path'] = $file->storeAs('publications', $filename, 'public');

            // Ekstraksi jumlah halaman dan ukuran file saat diupload
            $absolutePath = storage_path('app/public/' . $data['pdf_path']);
            
            $fileBytes = @filesize($absolutePath) ?: 0;
            if ($fileBytes >= 1048576) {
                $calculatedSize = number_format($fileBytes / 1048576, 2) . ' MB';
            } elseif ($fileBytes >= 1024) {
                $calculatedSize = number_format($fileBytes / 1024, 2) . ' KB';
            } else {
                $calculatedSize = $fileBytes . ' B';
            }

            $fileSize = $request->input('file_size', $calculatedSize);
            $pageCount = $request->input('page_count');
            if (empty($pageCount) || !is_numeric($pageCount)) {
                try {
                    $extracted = app(\App\Services\PdfExtractionService::class)->extract($absolutePath);
                    $pageCount = $extracted['page_count'] ?? 1;
                } catch (\Exception $ex) {
                    $pageCount = 1;
                }
            }

            // Reset status ke Pending karena berkas baru diunggah
            $data['region'] = null;
            $data['page_count'] = $pageCount;
            $data['file_size'] = $fileSize;
            $data['extracted_text'] = null;
            $data['summary'] = null;
            $data['topics'] = null;
            $data['keywords'] = null;
            $data['key_points'] = null;
            $data['indicators'] = null;
            $data['trends'] = null;
            $data['page_locations'] = null;
            $data['conclusion'] = null;
            $data['status'] = 'Pending';
            
            // Hapus hasil AI lama jika berkas baru diunggah
            $publication->aiResult()->delete();
        } else {
            // Jika tidak ada berkas baru diunggah, perbarui metadata di publication_information jika hasil AI sudah ada
            if ($publication->aiResult) {
                $pubInfo = $publication->aiResult->publication_information ?? [];
                if (is_array($pubInfo)) {
                    $pubInfo['title'] = $data['title'];
                    $pubInfo['category'] = $data['category'];
                    $pubInfo['year'] = $data['year'];
                    $publication->aiResult->update([
                        'publication_information' => $pubInfo,
                    ]);
                }
            }
        }

        // Simpan perubahan ke database
        $publication->update($data);

        // Catat Notifikasi
        Notification::create([
            'title' => 'Dokumen Diperbarui',
            'message' => 'Berkas "' . $publication->title . '" berhasil diperbarui.',
            'type' => 'upload',
        ]);

        return redirect()
            ->route('publications.index')
            ->with('success', 'Publikasi berhasil diperbarui.');
    }

    /**
     * Menampilkan log aktivitas sistem (Notifikasi).
     */
    public function activities(Request $request)
    {

        $query = \App\Models\Notification::query();

        // Filter berdasarkan tipe aktivitas jika ditentukan
        if ($request->filled('type') && $request->input('type') !== 'all') {
            $query->where('type', $request->input('type'));
        }

        // Pencarian jika ada
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('message', 'like', '%' . $search . '%');
            });
        }

        // Ambil notifikasi diurutkan dari yang terbaru (paginasi 15 item)
        $activities = $query->latest()->paginate(15);

        return view('activities.index', [
            'activities' => $activities,
            'activeMenu' => 'activities'
        ]);
    }

    /**
     * Menghapus seluruh riwayat aktivitas sistem.
     */
    public function clearActivities()
    {
        \App\Models\Notification::truncate();

        return redirect()
            ->route('activities.index')
            ->with('success', 'Seluruh riwayat aktivitas sistem berhasil dihapus.');
    }

    /**
     * Menyimpan hasil ekstraksi teks PDF dari client-side dan merumuskan analisis AI.
     */
    public function saveExtractedText(Request $request, Publication $publication)
    {
        @set_time_limit(180);

        $request->validate([
            'text' => 'required|string',
            'pages' => 'required|array',
            'page_count' => 'required|integer',
            'file_size' => 'required|string'
        ]);

        $text = $request->input('text');
        $pages = $request->input('pages');
        $pageCount = $request->input('page_count');
        $fileSize = $request->input('file_size');

        $aiResult = app(\App\Services\AiSummarizerService::class)->summarize(
            $publication->title,
            $publication->category,
            $publication->year,
            $text,
            $pages,
            $pageCount,
            $fileSize,
            $publication->created_at
        );

        $publication->update([
            'region' => $aiResult['publication_information']['region'] ?? $publication->region,
            'page_count' => $pageCount,
            'file_size' => $fileSize,
            'extracted_text' => $text,
            'status' => 'Selesai'
        ]);

        if ($publication->aiResult) {
            $publication->aiResult->update([
                'summary' => $aiResult['summary'],
                'publication_information' => $aiResult['publication_information'],
                'topics' => $aiResult['topics'],
                'keywords' => $aiResult['keywords'],
                'key_points' => $aiResult['key_points'],
                'indicators' => $aiResult['indicators'],
                'trends' => $aiResult['trends'],
                'discussion_locations' => $aiResult['discussion_locations'],
                'conclusion' => $aiResult['conclusion'],
            ]);
        } else {
            $publication->aiResult()->create([
                'summary' => $aiResult['summary'],
                'publication_information' => $aiResult['publication_information'],
                'topics' => $aiResult['topics'],
                'keywords' => $aiResult['keywords'],
                'key_points' => $aiResult['key_points'],
                'indicators' => $aiResult['indicators'],
                'trends' => $aiResult['trends'],
                'discussion_locations' => $aiResult['discussion_locations'],
                'conclusion' => $aiResult['conclusion'],
            ]);
        }

        // Catat Notifikasi Aktivitas
        \App\Models\Notification::create([
            'title' => 'Ekstraksi AI Berhasil',
            'message' => 'Analisis AI untuk publikasi "' . $publication->title . '" berhasil diselesaikan secara otomatis.',
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Teks berhasil diekstrak dan dianalisis oleh AI.'
        ]);
    }

    /**
     * Menampilkan daftar publikasi yang sudah selesai diekstrak (Ringkasan).
     */
    public function summaries(Request $request)
    {
        $request->merge(['status' => 'Selesai']);
        return $this->index($request);
    }
}

