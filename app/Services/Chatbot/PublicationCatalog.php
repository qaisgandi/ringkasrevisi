<?php

namespace App\Services\Chatbot;

use App\Models\Publication;
use Illuminate\Support\Collection;

/**
 * Mengambil, mencari, dan merangkum katalog publikasi untuk kebutuhan chatbot.
 *
 * Pencarian dilakukan di PHP agar konsisten antara SQLite (lokal) dan
 * PostgreSQL (produksi), serta dapat menjangkau topik, kata kunci,
 * ringkasan, indikator, dan kesimpulan — bukan hanya judul.
 */
class PublicationCatalog
{
    private const WEIGHTS = [
        'title' => 8,
        'topics' => 6,
        'keywords' => 5,
        'region' => 5,
        'category' => 4,
        'summary' => 3,
        'indicators' => 3,
        'conclusion' => 2,
    ];

    private const INDEX_LIMIT = 150;

    private const DOSSIER_LIMIT = 6;

    private const DOSSIER_MAX_CHARS = 16000;

    private ?Collection $publications = null;

    /**
     * @return Collection<int, Publication>
     */
    public function publications(): Collection
    {
        if ($this->publications !== null) {
            return $this->publications;
        }

        return $this->publications = Publication::query()
            ->summarized()
            ->select([
                'id', 'title', 'category', 'year', 'region',
                'summary', 'topics', 'keywords', 'key_points',
                'indicators', 'trends', 'conclusion',
            ])
            ->with(['aiResult' => function ($query) {
                $query->select([
                    'id', 'publication_id', 'summary', 'topics', 'keywords',
                    'key_points', 'indicators', 'trends', 'conclusion', 'discussion_locations',
                ]);
            }])
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @return array<int, int>
     */
    public function publicationIds(): array
    {
        return $this->publications()->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    /**
     * Cari publikasi paling relevan berdasarkan hasil analisis pertanyaan.
     *
     * @param  array{keywords?: array<int, string>, region?: string|null, year?: int|null}  $analysis
     * @return Collection<int, Publication>
     */
    public function search(array $analysis, int $limit = self::DOSSIER_LIMIT): Collection
    {
        $terms = array_values(array_filter((array) ($analysis['keywords'] ?? [])));
        $region = $analysis['region'] ?? null;
        $year = $analysis['year'] ?? null;

        if (empty($terms) && !$region && !$year) {
            return collect();
        }

        $publications = $this->publications();
        $scored = $this->score($publications, $terms, $region, $year);

        // Jika tidak ada yang cocok, coba toleransi salah ketik ringan.
        if (empty($scored) && !empty($terms)) {
            $corrected = $this->correctTerms($terms, $publications);

            if (!empty($corrected)) {
                $scored = $this->score($publications, $corrected, $region, $year);
            }
        }

        usort($scored, function (array $a, array $b): int {
            return [$b['score'], (int) $b['publication']->year, (int) $b['publication']->id]
                <=> [$a['score'], (int) $a['publication']->year, (int) $a['publication']->id];
        });

        return collect($scored)
            ->take($limit)
            ->pluck('publication')
            ->values();
    }

    /**
     * Indeks katalog ringkas: seluruh publikasi tanpa ringkasan panjang.
     */
    public function indexText(?Collection $publications = null, int $limit = self::INDEX_LIMIT): string
    {
        $publications ??= $this->publications();

        if ($publications->isEmpty()) {
            return 'Belum ada publikasi statistik yang diringkas pada sistem.';
        }

        $lines = [];

        foreach ($publications->take($limit) as $publication) {
            $topics = $this->valueList($publication->aiResult?->topics ?? $publication->topics);
            $line = sprintf(
                '- [ID: %d] "%s" | Kategori: %s | Tahun: %s | Wilayah: %s',
                $publication->id,
                $publication->title,
                $publication->category ?: 'Umum',
                $publication->year ?: '-',
                $publication->region ?: 'Nasional'
            );

            if (!empty($topics)) {
                $line .= ' | Topik: ' . implode(', ', array_slice($topics, 0, 4));
            }

            $lines[] = $line;
        }

        if ($publications->count() > $limit) {
            $lines[] = '- ... dan ' . ($publications->count() - $limit) . ' publikasi lain di katalog.';
        }

        return implode("\n", $lines);
    }

    /**
     * Detail publikasi yang paling relevan untuk dikirim ke model AI.
     */
    public function dossierText(Collection $matches): string
    {
        if ($matches->isEmpty()) {
            return 'Tidak ada publikasi yang cocok secara langsung dengan pertanyaan pengguna.';
        }

        $blocks = [];
        $total = 0;

        foreach ($matches as $publication) {
            $ai = $publication->aiResult;
            $summary = $this->clip(strip_tags((string) ($ai?->summary ?? $publication->summary ?? '')), 900);
            $conclusion = $this->clip(strip_tags((string) ($ai?->conclusion ?? $publication->conclusion ?? '')), 400);
            $keyPoints = $this->valueList($ai?->key_points ?? $publication->key_points);
            $indicators = $this->indicatorList($ai?->indicators ?? $publication->indicators);
            $locations = $this->locationList($ai?->discussion_locations);

            $block = sprintf(
                "### [ID: %d] \"%s\"\nKategori: %s | Tahun: %s | Wilayah: %s\n",
                $publication->id,
                $publication->title,
                $publication->category ?: 'Umum',
                $publication->year ?: '-',
                $publication->region ?: 'Nasional'
            );

            if ($summary !== '') {
                $block .= "Ringkasan: {$summary}\n";
            }

            if (!empty($keyPoints)) {
                $block .= "Poin penting:\n";
                foreach (array_slice($keyPoints, 0, 5) as $point) {
                    $block .= '- ' . $this->clip(strip_tags($point), 220) . "\n";
                }
            }

            if (!empty($indicators)) {
                $block .= 'Indikator: ' . implode('; ', array_slice($indicators, 0, 8)) . "\n";
            }

            if ($conclusion !== '') {
                $block .= "Kesimpulan: {$conclusion}\n";
            }

            if (!empty($locations)) {
                $block .= 'Lokasi pembahasan: ' . implode(', ', array_slice($locations, 0, 6)) . "\n";
            }

            if ($total + mb_strlen($block) > self::DOSSIER_MAX_CHARS) {
                break;
            }

            $blocks[] = $block;
            $total += mb_strlen($block);
        }

        return implode("\n", $blocks);
    }

    /**
     * Ringkasan isi katalog untuk saran pertanyaan dinamis.
     *
     * @return array{total: int, categories: array<int, string>, regions: array<int, string>, years: array<int, int>}
     */
    public function stats(): array
    {
        $publications = $this->publications();

        return [
            'total' => $publications->count(),
            'categories' => $publications->pluck('category')->filter()->unique()->values()->all(),
            'regions' => $publications->pluck('region')->filter()->unique()->values()->all(),
            'years' => $publications->pluck('year')->filter()->unique()->sortDesc()->values()->all(),
        ];
    }

    /**
     * Saran pertanyaan yang relevan dengan isi katalog saat ini.
     *
     * @return array<int, string>
     */
    public function suggestions(): array
    {
        $publications = $this->publications();
        $stats = $this->stats();
        $suggestions = [];

        $containsTerm = function (string $term) use ($publications): bool {
            $term = mb_strtolower($term);

            foreach ($publications as $publication) {
                $haystack = mb_strtolower(implode(' ', [
                    (string) $publication->title,
                    $this->flatten($publication->aiResult?->topics ?? $publication->topics),
                    $this->flatten($publication->aiResult?->keywords ?? $publication->keywords),
                    strip_tags((string) ($publication->aiResult?->summary ?? $publication->summary ?? '')),
                ]));

                if (str_contains($haystack, $term)) {
                    return true;
                }
            }

            return false;
        };

        if ($containsTerm('kemiskinan')) {
            $suggestions[] = 'Ada data kemiskinan terbaru?';
        }

        if ($containsTerm('ipm') || $containsTerm('pembangunan manusia')) {
            $suggestions[] = 'Apa itu IPM dan bagaimana cara membacanya?';
        }

        $region = collect($stats['regions'])
            ->reject(fn ($value) => in_array(mb_strtolower((string) $value), ['umum', 'nasional', 'nasional (indonesia)'], true))
            ->first();

        if ($region) {
            $suggestions[] = "Publikasi apa yang tersedia untuk {$region}?";
        }

        if (!empty($stats['years'])) {
            $suggestions[] = 'Tampilkan publikasi tahun ' . $stats['years'][0];
        }

        $suggestions[] = 'Apa itu Gini Ratio?';

        return array_slice(array_values(array_unique($suggestions)), 0, 4);
    }

    /**
     * Alasan singkat mengapa sebuah publikasi dianggap relevan.
     *
     * @param  array{keywords?: array<int, string>, region?: string|null, year?: int|null}  $analysis
     */
    public function matchReason(Publication $publication, array $analysis): string
    {
        $fields = $this->fields($publication);
        $labels = [
            'title' => 'judul',
            'topics' => 'topik',
            'keywords' => 'kata kunci',
            'category' => 'kategori',
            'summary' => 'ringkasan',
            'indicators' => 'indikator',
            'conclusion' => 'kesimpulan',
        ];

        $hits = [];

        foreach ((array) ($analysis['keywords'] ?? []) as $term) {
            foreach ($labels as $field => $label) {
                if (!isset($hits[$label]) && str_contains($fields[$field] ?? '', $term)) {
                    $hits[$label] = $label;
                }
            }
        }

        if (!empty($analysis['region']) && str_contains($fields['region'], $analysis['region'])) {
            $hits['wilayah'] = 'wilayah';
        }

        if (!empty($analysis['year']) && (int) $publication->year === (int) $analysis['year']) {
            $hits['tahun'] = 'tahun';
        }

        if (empty($hits)) {
            return '';
        }

        return 'Relevan pada ' . implode(', ', array_slice(array_values($hits), 0, 3));
    }

    /**
     * @param  Collection<int, Publication>  $publications
     * @param  array<int, string>  $terms
     * @return array<int, array{publication: Publication, score: float}>
     */
    private function score(Collection $publications, array $terms, ?string $region, ?int $year): array
    {
        $results = [];

        foreach ($publications as $publication) {
            $fields = $this->fields($publication);
            $score = 0.0;

            foreach ($terms as $term) {
                $term = mb_strtolower(trim($term));

                if ($term === '') {
                    continue;
                }

                foreach (self::WEIGHTS as $field => $weight) {
                    if (str_contains($fields[$field] ?? '', $term)) {
                        // Frasa yang lebih spesifik diberi bobot tambahan.
                        $score += $weight + (str_contains($term, ' ') ? 2 : 0);
                    }
                }
            }

            if ($region !== null && $region !== '' && str_contains($fields['region'], $region)) {
                $score += 12;
            }

            if ($year !== null && (int) $publication->year === (int) $year) {
                $score += 8;
            }

            if ($score > 0) {
                $results[] = ['publication' => $publication, 'score' => $score];
            }
        }

        return $results;
    }

    /**
     * Perbaiki salah ketik ringan dengan mencari kata terdekat di katalog.
     *
     * @param  array<int, string>  $terms
     * @param  Collection<int, Publication>  $publications
     * @return array<int, string>
     */
    private function correctTerms(array $terms, Collection $publications): array
    {
        $vocabulary = [];

        foreach ($publications as $publication) {
            foreach ($this->fields($publication) as $text) {
                foreach (preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $word) {
                    if (mb_strlen($word) >= 4) {
                        $vocabulary[$word] = true;
                    }
                }
            }
        }

        $words = array_keys($vocabulary);
        $corrected = [];

        foreach ($terms as $term) {
            if (str_contains($term, ' ') || mb_strlen($term) < 5) {
                continue;
            }

            $threshold = mb_strlen($term) >= 8 ? 2 : 1;
            $best = null;
            $bestDistance = PHP_INT_MAX;

            foreach ($words as $word) {
                if (abs(mb_strlen($word) - mb_strlen($term)) > $threshold) {
                    continue;
                }

                $distance = levenshtein($term, $word);

                if ($distance < $bestDistance) {
                    $bestDistance = $distance;
                    $best = $word;
                }
            }

            if ($best !== null && $bestDistance <= $threshold) {
                $corrected[] = $best;
            }
        }

        return array_values(array_unique($corrected));
    }

    /**
     * @return array<string, string>
     */
    private function fields(Publication $publication): array
    {
        $ai = $publication->aiResult;

        return [
            'title' => mb_strtolower((string) $publication->title),
            'category' => mb_strtolower((string) $publication->category),
            'region' => mb_strtolower((string) $publication->region),
            'topics' => $this->flatten($ai?->topics ?? $publication->topics),
            'keywords' => $this->flatten($ai?->keywords ?? $publication->keywords),
            'summary' => mb_strtolower(strip_tags((string) ($ai?->summary ?? $publication->summary ?? ''))),
            'conclusion' => mb_strtolower(strip_tags((string) ($ai?->conclusion ?? $publication->conclusion ?? ''))),
            'indicators' => $this->flatten($ai?->indicators ?? $publication->indicators),
        ];
    }

    private function flatten(mixed $value): string
    {
        $parts = [];

        if (is_array($value)) {
            array_walk_recursive($value, function ($item) use (&$parts) {
                if (is_scalar($item)) {
                    $parts[] = (string) $item;
                }
            });
        } elseif (is_scalar($value)) {
            $parts[] = (string) $value;
        }

        return mb_strtolower(implode(' ', $parts));
    }

    /**
     * @return array<int, string>
     */
    private function valueList(mixed $value): array
    {
        if (!is_array($value)) {
            return is_scalar($value) && (string) $value !== '' ? [(string) $value] : [];
        }

        $items = [];
        array_walk_recursive($value, function ($item) use (&$items) {
            if (is_scalar($item) && trim((string) $item) !== '') {
                $items[] = trim((string) $item);
            }
        });

        return array_values(array_unique($items));
    }

    /**
     * @return array<int, string>
     */
    private function indicatorList(mixed $indicators): array
    {
        if (!is_array($indicators)) {
            return [];
        }

        $items = [];

        foreach ($indicators as $indicator) {
            if (!is_array($indicator) || empty($indicator['name'])) {
                continue;
            }

            $value = trim((string) ($indicator['value'] ?? ''));
            $unit = trim((string) ($indicator['unit'] ?? ''));
            $items[] = trim($indicator['name'] . ': ' . trim($value . ' ' . $unit));
        }

        return array_values(array_unique($items));
    }

    /**
     * @return array<int, string>
     */
    private function locationList(mixed $locations): array
    {
        if (!is_array($locations)) {
            return [];
        }

        $items = [];

        foreach ($locations as $location) {
            if (!is_array($location) || empty($location['topic'])) {
                continue;
            }

            $page = $location['page'] ?? null;
            $items[] = $location['topic'] . ($page ? " (hal. {$page})" : '');
        }

        return $items;
    }

    private function clip(string $text, int $length): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? $text);

        if ($text === '') {
            return '';
        }

        return mb_strlen($text) > $length
            ? mb_substr($text, 0, $length) . '…'
            : $text;
    }
}
