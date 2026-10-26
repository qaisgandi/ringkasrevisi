<?php

namespace App\Services;

use App\Models\Publication;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiChatService
{
    /**
     * Kirim pesan ke Gemini API dengan konteks katalog atau konteks publikasi spesifik.
     *
     * @param string $userMessage Pesan dari pengguna
     * @param array $history Riwayat percakapan sebelumnya [['role' => 'user'|'model', 'text' => '...']]
     * @param int|null $publicationId ID publikasi jika dalam mode in-document
     * @return array ['status' => bool, 'reply' => string, 'mode' => string, 'publication' => array|null]
     */
    public function chat(string $userMessage, array $history = [], ?int $publicationId = null): array
    {
        $publication = null;
        $isDocumentMode = false;

        if ($publicationId) {
            $publication = Publication::with('aiResult')->find($publicationId);
            if ($publication) {
                $isDocumentMode = true;
            }
        }

        $systemInstruction = $isDocumentMode
            ? $this->buildDocumentSystemInstruction($publication)
            : $this->buildUniversalSystemInstruction();

        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-1.5-flash');

        if (empty($apiKey)) {
            return [
                'status' => true,
                'reply' => $this->generateFallbackReply($userMessage, $publication, $isDocumentMode),
                'mode' => $isDocumentMode ? 'document' : 'universal',
                'publication' => $publication ? ['id' => $publication->id, 'title' => $publication->title] : null,
            ];
        }

        try {
            $contents = $this->formatHistoryAndMessage($history, $userMessage);
            $replyText = $this->callGeminiApi($apiKey, $model, $systemInstruction, $contents);

            return [
                'status' => true,
                'reply' => $replyText,
                'mode' => $isDocumentMode ? 'document' : 'universal',
                'publication' => $publication ? [
                    'id' => $publication->id,
                    'title' => $publication->title,
                    'category' => $publication->category,
                    'year' => $publication->year,
                ] : null,
            ];
        } catch (\Exception $e) {
            Log::warning('Gemini chat request failed.', ['exception_type' => get_class($e)]);

            // Fallback response jika terjadi kendala jaringan/kuota API
            $fallbackReply = $this->generateFallbackReply($userMessage, $publication, $isDocumentMode);

            return [
                'status' => false,
                'reply' => $fallbackReply,
                'mode' => $isDocumentMode ? 'document' : 'universal',
                'publication' => $publication ? ['id' => $publication->id, 'title' => $publication->title] : null,
                'error' => 'Layanan AI sedang tidak tersedia.',
            ];
        }
    }

    /**
     * Susun System Instruction untuk Mode Universal (Seluruh Katalog Publikasi).
     */
    private function buildUniversalSystemInstruction(): string
    {
        $publications = Publication::with('aiResult')->latest()->get();

        $catalogSummary = [];
        foreach ($publications as $pub) {
            $ai = $pub->aiResult;
            $indicators = $ai ? ($ai->indicators ?? []) : ($pub->indicators ?? []);
            $topics = $ai ? ($ai->topics ?? []) : ($pub->topics ?? []);
            $summary = $ai ? ($ai->summary ?? '') : ($pub->summary ?? '');
            $conclusion = $ai ? ($ai->conclusion ?? '') : ($pub->conclusion ?? '');

            // Format indikator ringkas
            $indicatorStrings = [];
            if (is_array($indicators)) {
                foreach (array_slice($indicators, 0, 8) as $ind) {
                    if (is_array($ind) && isset($ind['name'])) {
                        $val = ($ind['value'] ?? '-') . ' ' . ($ind['unit'] ?? '');
                        $indicatorStrings[] = "{$ind['name']}: {$val}";
                    }
                }
            }

            $catalogSummary[] = sprintf(
                "- [ID: %d] \"%s\"\n  • Kategori: %s | Tahun: %s | Wilayah: %s\n  • Topik: %s\n  • Indikator Utama: %s\n  • Ringkasan Inti: %s\n  • Kesimpulan: %s\n  • Tautan: /publications/%d",
                $pub->id,
                $pub->title,
                $pub->category ?? 'Umum',
                $pub->year ?? '-',
                $pub->region ?? 'Nasional',
                is_array($topics) ? implode(', ', array_slice($topics, 0, 5)) : '-',
                !empty($indicatorStrings) ? implode('; ', $indicatorStrings) : 'Tidak ada indikator spesifik tercatat',
                !empty($summary) ? mb_substr(strip_tags($summary), 0, 250) . '...' : 'Ringkasan belum tersedia',
                !empty($conclusion) ? mb_substr(strip_tags($conclusion), 0, 180) . '...' : '-',
                $pub->id
            );
        }

        $catalogText = !empty($catalogSummary)
            ? implode("\n\n", $catalogSummary)
            : "Saat ini belum ada publikasi statistik yang diunggah ke sistem.";

        return "Anda adalah Asisten Virtual Cerdas BPS (Badan Pusat Statistik) Republik Indonesia pada portal aplikasi 'RINGKAS'.
Peran Anda adalah menjadi konsultan dan pustakawan data statistik yang ramah, profesional, solutif, dan berbasis data.

KONTEKS KATALOG PUBLIKASI YANG TERSEDIA DI SISTEM:
=====================================================
{$catalogText}
=====================================================

PANDUAN MENJAWAB:
1. PENCARIAN PUBLIKASI & DATA: Jika pengguna bertanya tentang data tertentu (misalnya: 'Ada di publikasi mana tingkat IPM Kota X?', 'Di mana data inflasi?', 'Publikasi apa yang membahas kemiskinan?'):
   - Cari publikasi yang relevan dari katalog di atas.
   - Sebutkan judul publikasinya dengan jelas.
   - Sampaikan angka/indikator penting jika tersedia di katalog di atas.
   - Selalu berikan tautan langsung yang dapat diklik ke publikasi tersebut dengan format Markdown link: `[Buka Publikasi: Judul Publikasi](/publications/ID)`.
2. ANALISIS & PERBANDINGAN: Jika pengguna meminta perbandingan antar daerah atau tahun, buatkan ringkasan perbandingan atau tabel sederhana berdasarkan data yang ada di katalog.
3. EDUKASI STATISTIK: Jika pengguna menanyakan definisi istilah statistik (seperti apa itu IPM, Gini Ratio, Inflasi YoY, Angka Partisipasi Murni, dll.), jelaskan dengan bahasa yang mudah dimengerti sesuai standar Badan Pusat Statistik.
4. JIKA DATA TIDAK DITEMUKAN: Jika topik/wilayah yang dicari belum ada di katalog publikasi saat ini, jelaskan dengan sopan bahwa publikasi tersebut belum tersedia di portal RINGKAS, dan berikan saran publikasi atau kategori terdekat yang ada.
5. FORMAT & GAYA BAHASA:
   - Gunakan Bahasa Indonesia yang sopan, ramah, dan profesional.
   - Gunakan format Markdown yang menarik (gunakan bullet point, teks tebal (**teks**), dan emoji relevan seperti 📊, 📘, 📈, 📍).
   - Jangan pernah mengarang data statistik yang bertentangan dengan katalog di atas.";
    }

    /**
     * Susun System Instruction untuk Mode In-Document (1 Publikasi Spesifik).
     */
    private function buildDocumentSystemInstruction(Publication $publication): string
    {
        $ai = $publication->aiResult;
        $title = $publication->title;
        $category = $publication->category ?? 'Umum';
        $year = $publication->year ?? '-';
        $region = $publication->region ?? 'Nasional';
        $summary = $ai ? ($ai->summary ?? '') : ($publication->summary ?? '');
        $indicators = $ai ? ($ai->indicators ?? []) : ($publication->indicators ?? []);
        $keyPoints = $ai ? ($ai->key_points ?? []) : ($publication->key_points ?? []);
        $trends = $ai ? ($ai->trends ?? []) : ($publication->trends ?? []);
        $conclusion = $ai ? ($ai->conclusion ?? '') : ($publication->conclusion ?? '');

        // Potong extracted text jika terlalu panjang agar tetap optimal
        $extractedText = $publication->extracted_text ?? '';
        $textExcerpt = !empty($extractedText) ? mb_substr($extractedText, 0, 30000) : 'Teks lengkap belum diekstraksi.';

        $indicatorsJson = json_encode($indicators, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $keyPointsJson = json_encode($keyPoints, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $trendsJson = json_encode($trends, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return "Anda adalah Asisten Cerdas BPS (Badan Pusat Statistik) Republik Indonesia pada portal 'RINGKAS'.
Saat ini Anda sedang bertindak sebagai pendamping membaca untuk publikasi berikut:

INFORMASI DOKUMEN YANG SEDANG DIBUKA:
=====================================================
- Judul: \"{$title}\"
- Kategori: {$category}
- Tahun: {$year}
- Wilayah: {$region}
- URL Dokumen: /publications/{$publication->id}

RINGKASAN EKSEKUTIF DOKUMEN:
{$summary}

INDIKATOR STATISTIK:
{$indicatorsJson}

TEMUAN KUNCI:
{$keyPointsJson}

TREN DATA:
{$trendsJson}

KESIMPULAN:
{$conclusion}

TEKS DOKUMEN HASIL EKSTRAKSI (Kutipan):
---
{$textExcerpt}
---
=====================================================

PANDUAN MENJAWAB:
1. Jawab pertanyaan pengguna secara akurat, mendalam, dan relevan khusus mengenai dokumen publikasi \"{$title}\" ini.
2. Jika pengguna meminta penjelasan mengenai bab, tabel, indikator, tren, atau kesimpulan, jelaskan secara terstruktur.
3. Gunakan bahasa Indonesia yang santun, profesional, dan mudah dipahami.
4. Gunakan pemformatan Markdown (teks tebal, daftar poin, dan tabel jika perlu) agar jawaban enak dibaca.";
    }

    /**
     * Format riwayat percakapan menjadi struktur payload Gemini API.
     */
    private function formatHistoryAndMessage(array $history, string $userMessage): array
    {
        $contents = [];

        // Ambil maksimal 8 pesan riwayat terakhir agar token efisien
        $recentHistory = array_slice($history, -8);

        foreach ($recentHistory as $item) {
            $role = ($item['role'] ?? '') === 'model' || ($item['role'] ?? '') === 'assistant' ? 'model' : 'user';
            $text = trim($item['text'] ?? $item['content'] ?? '');

            if (!empty($text)) {
                $contents[] = [
                    'role' => $role,
                    'parts' => [
                        ['text' => $text]
                    ]
                ];
            }
        }

        // Tambahkan pesan user terkini
        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage]
            ]
        ];

        return $contents;
    }

    /**
     * Lakukan HTTP POST ke Google Gemini API endpoint v1beta.
     */
    private function callGeminiApi(string $apiKey, string $model, string $systemInstruction, array $contents): string
    {
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $payload = [
            'system_instruction' => [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 2048,
            ]
        ];

        $response = Http::withHeaders([
            'x-goog-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(35)->post($endpoint, $payload);

        if ($response->failed()) {
            // Jika model belum mendukung system_instruction terpisah, fallback gabungkan system instruction ke turn user pertama
            if ($response->status() === 400 && str_contains($response->body(), 'system_instruction')) {
                return $this->callGeminiApiLegacy($apiKey, $model, $systemInstruction, $contents);
            }
            throw new \Exception('Gemini API request error (' . $response->status() . '): ' . $response->body());
        }

        $responseData = $response->json();
        $replyText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (empty($replyText)) {
            throw new \Exception('Gemini API returned an empty response.');
        }

        return $replyText;
    }

    /**
     * Fallback format jika endpoint/model versi tertentu memerlukan system prompt di turn pertama.
     */
    private function callGeminiApiLegacy(string $apiKey, string $model, string $systemInstruction, array $contents): string
    {
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        if (!empty($contents) && isset($contents[0]['parts'][0]['text'])) {
            $contents[0]['parts'][0]['text'] = "[INSTRUKSI SISTEM UTAMA]:\n" . $systemInstruction . "\n\n[PESAN PENGGUNA]:\n" . $contents[0]['parts'][0]['text'];
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 2048,
            ]
        ];

        $response = Http::withHeaders([
            'x-goog-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(35)->post($endpoint, $payload);

        if ($response->failed()) {
            throw new \Exception('Gemini Legacy API error (' . $response->status() . '): ' . $response->body());
        }

        $responseData = $response->json();
        return $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, terjadi kendala saat memproses jawaban AI.';
    }

    /**
     * Respon fallback lokal pintar jika Gemini API sedang offline / terjadi timeout.
     */
    private function generateFallbackReply(string $userMessage, ?Publication $publication, bool $isDocumentMode): string
    {
        if ($isDocumentMode && $publication) {
            $ai = $publication->aiResult;
            $summary = $ai ? ($ai->summary ?? '') : ($publication->summary ?? '');
            $conclusion = $ai ? ($ai->conclusion ?? '') : ($publication->conclusion ?? '');

            return "💡 *Catatan: Asisten AI sedang beroperasi dalam mode hemat offline.*\n\nBerikut ringkasan mengenai publikasi **{$publication->title}**:\n\n" .
                ($summary ?: 'Ringkasan belum tersedia untuk publikasi ini.') . "\n\n" .
                "**Kesimpulan:**\n" . ($conclusion ?: 'Belum ada kesimpulan tercatat.');
        }

        // Mode universal offline search
        $keyword = mb_strtolower($userMessage);
        if (preg_match('/\b(halo|hai|hello|hi|selamat pagi|selamat siang|selamat sore|selamat malam)\b/u', $keyword)) {
            return 'Halo! Saya bisa membantu mencari publikasi berdasarkan topik, kategori, tahun, atau wilayah. Coba tanyakan, misalnya: “Cari publikasi tentang kemiskinan”.';
        }

        if (preg_match('/\b(bantuan|help|bisa apa|fitur)\b/u', $keyword)) {
            return 'Saya dapat mencari publikasi yang tersimpan di katalog berdasarkan judul, kategori, tahun, dan wilayah. Pada halaman publikasi, saya juga dapat menampilkan ringkasan yang sudah tersedia.';
        }

        $definitions = [
            'ipm' => 'IPM (Indeks Pembangunan Manusia) merangkum capaian pembangunan manusia melalui dimensi kesehatan, pendidikan, dan standar hidup. Periksa publikasi sumber untuk definisi dan metode penghitungan yang digunakan.',
            'gini' => 'Rasio Gini mengukur ketimpangan distribusi pengeluaran atau pendapatan. Nilai yang lebih tinggi menunjukkan ketimpangan yang lebih besar; perhatikan cakupan dan metode dalam publikasi sumber.',
            'inflasi' => 'Inflasi menggambarkan perubahan tingkat harga barang dan jasa dari waktu ke waktu. Periode pembanding dan cakupan komoditas perlu diperiksa pada publikasi sumber.',
            'pdrb' => 'PDRB (Produk Domestik Regional Bruto) menggambarkan nilai tambah barang dan jasa yang dihasilkan di suatu wilayah dalam periode tertentu. Perhatikan apakah data memakai harga berlaku atau harga konstan.',
            'tpt' => 'TPT (Tingkat Pengangguran Terbuka) adalah persentase penganggur terhadap angkatan kerja. Definisi operasional dan periode rujukan dapat dilihat pada publikasi sumber.',
        ];
        foreach ($definitions as $term => $definition) {
            if (str_contains($keyword, $term)) {
                $matches = $this->findCatalogMatches($keyword);
                return $definition . ($matches->isNotEmpty() ? "\n\n" . $this->formatCatalogMatches($matches) : '');
            }
        }

        $matches = $this->findCatalogMatches($keyword);

        if ($matches->isNotEmpty()) {
            return $this->formatCatalogMatches($matches);
        }

        return 'Saya sedang berjalan dalam mode katalog. Saya belum menemukan publikasi yang cocok. Coba gunakan kata kunci yang lebih singkat, seperti “kemiskinan”, “pertanian”, nama wilayah, atau tahun.';
    }

    private function findCatalogMatches(string $keyword)
    {
        $stopWords = ['ada', 'apa', 'di', 'dari', 'dan', 'data', 'mana', 'untuk', 'tentang', 'yang', 'dengan', 'bisa', 'saya', 'cari', 'publikasi', 'terbaru', 'tingkat', 'angka', 'berapa', 'tolong'];
        $terms = collect(preg_split('/[^\pL\pN]+/u', $keyword) ?: [])
            ->map(fn ($term) => trim($term))
            ->filter(fn ($term) => mb_strlen($term) >= 3 && !in_array($term, $stopWords, true))
            ->unique()
            ->take(5);

        if ($terms->isEmpty()) {
            return collect();
        }

        return Publication::query()
            ->where(function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->orWhere('title', 'like', "%{$term}%")
                        ->orWhere('category', 'like', "%{$term}%")
                        ->orWhere('region', 'like', "%{$term}%")
                        ->orWhere('year', 'like', "%{$term}%");
                }
            })
            ->latest()
            ->limit(3)
            ->get();
    }

    private function formatCatalogMatches($matches): string
    {
        $reply = "Berikut publikasi yang mungkin relevan. Pastikan kembali detailnya pada dokumen sumber:\n\n";
        foreach ($matches as $match) {
            $reply .= "📘 **{$match->title}** ({$match->year})\n";
            $reply .= "• Kategori: {$match->category} | Wilayah: " . ($match->region ?: 'Nasional') . "\n";
            $reply .= "👉 [Buka Publikasi](/publications/{$match->id})\n\n";
        }

        return $reply;
    }
}
