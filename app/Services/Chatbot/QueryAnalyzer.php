<?php

namespace App\Services\Chatbot;

/**
 * Memahami pertanyaan pengguna umum: niat, topik, wilayah, tahun,
 * singkatan, dan pertanyaan lanjutan.
 */
class QueryAnalyzer
{
    public const INTENT_GREETING = 'greeting';
    public const INTENT_THANKS = 'thanks';
    public const INTENT_HELP = 'help';
    public const INTENT_SUMMARIZE = 'summarize';
    public const INTENT_DEFINITION = 'definition';
    public const INTENT_LIST = 'list';
    public const INTENT_COMPARE = 'compare';
    public const INTENT_DOWNLOAD = 'download';
    public const INTENT_SEARCH = 'search';

    /**
     * Kata yang tidak membantu pencarian. Kata seperti "jumlah" sengaja
     * dipertahankan karena penting pada pertanyaan kependudukan.
     */
    private const STOP_WORDS = [
        'ada', 'adakah', 'apa', 'apakah', 'arti', 'aja', 'aku', 'anda', 'bagaimana',
        'bantu', 'bantuan', 'bisa', 'boleh', 'buku', 'cara', 'carikan', 'cari', 'contoh',
        'dalam', 'dan', 'dari', 'data', 'deh', 'dengan', 'di', 'dong', 'dokumen',
        'gimana', 'halo', 'hai', 'halaman', 'help', 'hi', 'hello', 'informasi', 'info',
        'ini', 'itu', 'jelasin', 'jelaskan', 'juga', 'kah', 'kalau', 'kalo', 'kami',
        'katalog', 'kasih', 'ke', 'kita', 'kok', 'lihat', 'link', 'loh', 'maksud',
        'malam', 'mana', 'masih', 'mau', 'mengenai', 'minta', 'mohon', 'nya', 'nih',
        'pada', 'pagi', 'page', 'pdf', 'penjelasan', 'pengertian', 'per', 'publikasi',
        'punya', 'ringkas', 'ringkasan', 'rangkum', 'saja', 'saya', 'secara', 'selamat',
        'semua', 'sih', 'siang', 'sore', 'sudah', 'summary', 'summarize', 'tadi',
        'tahun', 'tampilkan', 'tautan', 'terima', 'terbaru', 'thanks', 'thank', 'tingkat',
        'tolong', 'tentang', 'untuk', 'unduh', 'download', 'ya', 'yang', 'yak',
        'intisari', 'file', 'mengerti', 'tau', 'tahu',
    ];

    /**
     * Kelompok sinonim / istilah terkait. Setiap anggota kelompok dianggap
     * relevan ketika salah satu kata muncul pada pertanyaan.
     */
    private const SYNONYM_GROUPS = [
        'ipm' => ['ipm', 'indeks pembangunan manusia', 'pembangunan manusia', 'ipg', 'ipm perempuan'],
        'penduduk' => ['penduduk', 'kependudukan', 'demografi', 'populasi', 'jumlah penduduk', 'pertumbuhan penduduk'],
        'pengangguran' => ['pengangguran', 'tpt', 'ketenagakerjaan', 'angkatan kerja', 'tpak', 'bekerja'],
        'kemiskinan' => ['kemiskinan', 'miskin', 'garis kemiskinan', 'kemiskinan makro', 'penduduk miskin'],
        'inflasi' => ['inflasi', 'ihk', 'indeks harga konsumen', 'harga', 'harga konsumen'],
        'pdrb' => ['pdrb', 'pertumbuhan ekonomi', 'produk domestik regional bruto', 'ekonomi'],
        'pertanian' => ['pertanian', 'hortikultura', 'tanaman pangan', 'perkebunan', 'petani', 'produksi pangan'],
        'kesehatan' => ['kesehatan', 'stunting', 'gizi', 'bayi', 'harapan hidup', 'umur harapan hidup'],
        'pendidikan' => ['pendidikan', 'sekolah', 'hls', 'rls', 'harapan lama sekolah', 'rata rata lama sekolah', 'literasi'],
        'gender' => ['gender', 'kesetaraan gender', 'pemberdayaan perempuan', 'ipg'],
        'sensus' => ['sensus', 'survei', 'pendataan', 'responden'],
        'industri' => ['industri', 'manufaktur', 'perusahaan', 'usaha'],
        'konsumsi' => ['konsumsi', 'pengeluaran', 'belanja', 'rumah tangga'],
        'pemerintahan' => ['pemerintahan', 'apbd', 'keuangan daerah', 'desa'],
        'lingkungan' => ['lingkungan', 'iklim', 'cuaca', 'emisi'],
    ];

    /**
     * Alias wilayah → potongan teks yang dicari pada kolom region.
     * Nilai harus huruf kecil agar cocok dengan kolom yang dinormalisasi.
     */
    private const REGION_ALIASES = [
        'aceh' => 'aceh',
        'sumatera utara' => 'sumatera utara',
        'sumut' => 'sumatera utara',
        'sumatera barat' => 'sumatera barat',
        'sumbar' => 'sumatera barat',
        'kepulauan riau' => 'kepulauan riau',
        'kepri' => 'kepulauan riau',
        'sumatera selatan' => 'sumatera selatan',
        'sumsel' => 'sumatera selatan',
        'bangka belitung' => 'bangka belitung',
        'babel' => 'bangka belitung',
        'riau' => 'riau',
        'jambi' => 'jambi',
        'bengkulu' => 'bengkulu',
        'lampung' => 'lampung',
        'dki jakarta' => 'jakarta',
        'jakarta' => 'jakarta',
        'jawa barat' => 'jawa barat',
        'jabar' => 'jawa barat',
        'jawa tengah' => 'jawa tengah',
        'jateng' => 'jawa tengah',
        'di yogyakarta' => 'yogyakarta',
        'diy' => 'yogyakarta',
        'yogyakarta' => 'yogyakarta',
        'jogja' => 'yogyakarta',
        'jawa timur' => 'jawa timur',
        'jatim' => 'jawa timur',
        'banten' => 'banten',
        'bali' => 'bali',
        'nusa tenggara barat' => 'nusa tenggara barat',
        'ntb' => 'nusa tenggara barat',
        'nusa tenggara timur' => 'nusa tenggara timur',
        'ntt' => 'nusa tenggara timur',
        'kalimantan barat' => 'kalimantan barat',
        'kalbar' => 'kalimantan barat',
        'kalimantan tengah' => 'kalimantan tengah',
        'kalteng' => 'kalimantan tengah',
        'kalimantan selatan' => 'kalimantan selatan',
        'kalsel' => 'kalimantan selatan',
        'kalimantan timur' => 'kalimantan timur',
        'kaltim' => 'kalimantan timur',
        'kalimantan utara' => 'kalimantan utara',
        'kaltara' => 'kalimantan utara',
        'sulawesi utara' => 'sulawesi utara',
        'sulut' => 'sulawesi utara',
        'sulawesi tengah' => 'sulawesi tengah',
        'sulteng' => 'sulawesi tengah',
        'sulawesi selatan' => 'sulawesi selatan',
        'sulsel' => 'sulawesi selatan',
        'sulawesi tenggara' => 'sulawesi tenggara',
        'sultra' => 'sulawesi tenggara',
        'sulawesi barat' => 'sulawesi barat',
        'sulbar' => 'sulawesi barat',
        'maluku utara' => 'maluku utara',
        'malut' => 'maluku utara',
        'maluku' => 'maluku',
        'papua barat daya' => 'papua barat daya',
        'papua barat' => 'papua barat',
        'papua tengah' => 'papua tengah',
        'papua pegunungan' => 'papua pegunungan',
        'papua selatan' => 'papua selatan',
        'papua' => 'papua',
        'kota tasikmalaya' => 'tasikmalaya',
        'kabupaten tasikmalaya' => 'tasikmalaya',
        'tasikmalaya' => 'tasikmalaya',
        'tasik' => 'tasikmalaya',
        'kota bandung' => 'bandung',
        'kabupaten bandung' => 'bandung',
        'bandung' => 'bandung',
        'garut' => 'garut',
        'ciamis' => 'ciamis',
        'sumedang' => 'sumedang',
        'cirebon' => 'cirebon',
        'bogor' => 'bogor',
        'depok' => 'depok',
        'bekasi' => 'bekasi',
        'sukabumi' => 'sukabumi',
        'nasional' => 'nasional',
        'indonesia' => 'indonesia',
    ];

    /**
     * @param  array<int, array{role?: string, text?: string, content?: string}>  $history
     * @return array{
     *     raw: string,
     *     normalized: string,
     *     intent: string,
     *     is_follow_up: bool,
     *     previous_question: string,
     *     context: string,
     *     keywords: array<int, string>,
     *     year: int|null,
     *     region: string|null
     * }
     */
    public function analyze(string $message, array $history = []): array
    {
        $raw = trim($message);
        $normalized = $this->normalize($raw);
        $intent = $this->detectIntent($normalized);

        $previousQuestion = $this->previousUserMessage($history);
        $isFollowUp = $this->isFollowUp($normalized, $intent, $previousQuestion);

        $previousNormalized = $isFollowUp ? $this->normalize($previousQuestion) : '';
        $context = trim($previousNormalized . ' ' . $normalized);

        $year = $this->extractYear($normalized)
            ?? ($previousNormalized !== '' ? $this->extractYear($previousNormalized) : null);

        $region = $this->extractRegion($normalized)
            ?? ($previousNormalized !== '' ? $this->extractRegion($previousNormalized) : null);

        return [
            'raw' => $raw,
            'normalized' => $normalized,
            'intent' => $intent,
            'is_follow_up' => $isFollowUp,
            'previous_question' => $isFollowUp ? $previousQuestion : '',
            'context' => $context,
            'keywords' => $this->extractKeywords($context),
            'year' => $year,
            'region' => $region,
        ];
    }

    public function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text) ?? $text;

        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }

    private function detectIntent(string $normalized): string
    {
        if ($normalized === '') {
            return self::INTENT_GREETING;
        }

        if (preg_match('/^(halo|hai|hi|hello|selamat (pagi|siang|sore|malam)|assalamu|salam)\b/u', $normalized)) {
            return self::INTENT_GREETING;
        }

        if (preg_match('/\b(terima kasih|makasih|thanks|thank you|tq|sip|oke|ok)\b/u', $normalized)) {
            return self::INTENT_THANKS;
        }

        if (preg_match('/\b(rangkum|ringkas|ringkasan|intisari|summary|summarize)\b/u', $normalized)) {
            return self::INTENT_SUMMARIZE;
        }

        if (preg_match('/\b(bantuan|help|bisa apa|fitur|cara pakai|panduan|bingung)\b/u', $normalized)) {
            return self::INTENT_HELP;
        }

        if (preg_match('/\b(download|unduh|file pdf|ambil file|simpan pdf)\b/u', $normalized)) {
            return self::INTENT_DOWNLOAD;
        }

        if (preg_match('/\b(bandingkan|perbandingan|banding|versus|vs)\b/u', $normalized)) {
            return self::INTENT_COMPARE;
        }

        if (preg_match('/\b(apa itu|apakah itu|pengertian|definisi|arti|maksud dari|apa yang dimaksud)\b/u', $normalized)) {
            return self::INTENT_DEFINITION;
        }

        if (preg_match('/\b(publikasi|buku|katalog|daftar)\b.*\b(apa saja|apa aja|daftar|tersedia|semua|list)\b/u', $normalized)
            || preg_match('/^(publikasi|buku|katalog)\b/u', $normalized)) {
            return self::INTENT_LIST;
        }

        return self::INTENT_SEARCH;
    }

    private function isFollowUp(string $normalized, string $intent, string $previousQuestion): bool
    {
        if ($previousQuestion === '') {
            return false;
        }

        if (in_array($intent, [
            self::INTENT_GREETING,
            self::INTENT_THANKS,
            self::INTENT_HELP,
            self::INTENT_SUMMARIZE,
            self::INTENT_LIST,
            self::INTENT_DOWNLOAD,
        ], true)) {
            return false;
        }

        if (preg_match('/^(kalau|kalo|klo|yang|itu|tadi|nya|selain|terus|lalu|dan|berapa|bagaimana dengan|gimana dengan)\b/u', $normalized)) {
            return true;
        }

        $wordCount = count(preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: []);

        return $wordCount <= 4
            && ($this->extractYear($normalized) !== null || $this->extractRegion($normalized) !== null);
    }

    /**
     * @param  array<int, array{role?: string, text?: string, content?: string}>  $history
     */
    private function previousUserMessage(array $history): string
    {
        foreach (array_reverse($history) as $item) {
            $role = $item['role'] ?? 'user';

            if (!in_array($role, ['user', 'human'], true)) {
                continue;
            }

            $text = trim((string) ($item['text'] ?? $item['content'] ?? ''));

            if ($text !== '') {
                return $text;
            }
        }

        return '';
    }

    private function extractYear(string $normalized): ?int
    {
        if (preg_match('/\b(19\d{2}|20\d{2})\b/', $normalized, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function extractRegion(string $normalized): ?string
    {
        $aliases = array_keys(self::REGION_ALIASES);

        // Cek alias terpanjang lebih dulu agar "kota bandung" menang atas "bandung".
        usort($aliases, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));

        foreach ($aliases as $alias) {
            if (preg_match('/(?<![\p{L}\p{N}])' . preg_quote($alias, '/') . '(?![\p{L}\p{N}])/u', $normalized)) {
                return self::REGION_ALIASES[$alias];
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function extractKeywords(string $context): array
    {
        if ($context === '') {
            return [];
        }

        $tokens = preg_split('/\s+/', $context, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $keywords = [];

        foreach ($tokens as $token) {
            if (mb_strlen($token) < 3 || in_array($token, self::STOP_WORDS, true)) {
                continue;
            }

            if (preg_match('/^(19|20)\d{2}$/', $token)) {
                continue;
            }

            $keywords[] = $token;

            foreach (self::SYNONYM_GROUPS as $group) {
                if (in_array($token, $group, true)) {
                    foreach ($group as $synonym) {
                        // Frasa multi-kata ikut dimasukkan sebagai istilah pencarian.
                        $keywords[] = $synonym;
                    }
                }
            }
        }

        // Pertahankan kemunculan pertama, buang duplikat, batasi agar tetap ringkas.
        return array_slice(array_values(array_unique(array_filter($keywords, fn ($word) => $word !== ''))), 0, 16);
    }
}
