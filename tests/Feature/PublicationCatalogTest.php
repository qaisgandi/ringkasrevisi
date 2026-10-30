<?php

namespace Tests\Feature;

use App\Models\Publication;
use App\Services\Chatbot\PublicationCatalog;
use App\Services\Chatbot\QueryAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicationCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_finds_publications_through_topics_and_summary(): void
    {
        $match = $this->createPublication([
            'title' => 'Laporan Tahunan Daerah',
            'summary' => 'Analisis kemiskinan dan kesejahteraan masyarakat di daerah.',
            'topics' => ['Kemiskinan', 'Kesejahteraan'],
        ]);

        $pending = $this->createPublication([
            'title' => 'Kemiskinan Belum Ringkas',
            'status' => 'Pending',
        ]);

        $results = $this->catalog()->search($this->analyzer()->analyze('ada data kemiskinan ga?'));

        $this->assertTrue($results->contains('id', $match->id));
        $this->assertFalse($results->contains('id', $pending->id));
    }

    public function test_search_tolerates_small_typos(): void
    {
        $match = $this->createPublication([
            'title' => 'Statistik Kemiskinan',
        ]);

        $results = $this->catalog()->search($this->analyzer()->analyze('kemiskina'));

        $this->assertTrue($results->contains('id', $match->id));
    }

    public function test_search_prefers_title_matches_over_summary_matches(): void
    {
        $titleMatch = $this->createPublication([
            'title' => 'Statistik Kemiskinan',
        ]);

        $this->createPublication([
            'title' => 'Laporan Umum',
            'summary' => 'Membahas kemiskinan secara umum.',
        ]);

        $results = $this->catalog()->search($this->analyzer()->analyze('kemiskinan'));

        $this->assertSame($titleMatch->id, $results->first()->id);
    }

    public function test_search_uses_region_and_year_hints(): void
    {
        $tasikmalaya = $this->createPublication([
            'title' => 'Statistik Daerah',
            'region' => 'Kota Tasikmalaya',
            'year' => 2026,
            'topics' => ['Kemiskinan'],
        ]);

        $this->createPublication([
            'title' => 'Statistik Provinsi',
            'region' => 'Jawa Barat',
            'year' => 2025,
            'topics' => ['Kemiskinan'],
        ]);

        $results = $this->catalog()->search($this->analyzer()->analyze('kemiskinan tasikmalaya 2026'));

        $this->assertSame($tasikmalaya->id, $results->first()->id);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createPublication(array $overrides = []): Publication
    {
        static $counter = 0;
        $counter++;

        return Publication::create(array_merge([
            'title' => 'Publikasi Uji ' . $counter,
            'category' => 'Publikasi Umum',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/uji-' . $counter . '.pdf',
            'summary' => 'Ringkasan pengujian.',
            'status' => 'Selesai',
        ], $overrides));
    }

    private function catalog(): PublicationCatalog
    {
        return new PublicationCatalog();
    }

    private function analyzer(): QueryAnalyzer
    {
        return new QueryAnalyzer();
    }
}
