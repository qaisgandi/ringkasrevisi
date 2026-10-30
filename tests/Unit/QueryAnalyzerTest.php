<?php

namespace Tests\Unit;

use App\Services\Chatbot\QueryAnalyzer;
use PHPUnit\Framework\TestCase;

class QueryAnalyzerTest extends TestCase
{
    private QueryAnalyzer $analyzer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->analyzer = new QueryAnalyzer();
    }

    public function test_it_extracts_synonyms_region_and_year_from_casual_questions(): void
    {
        $result = $this->analyzer->analyze('data penduduk jabar 2024');

        $this->assertSame(QueryAnalyzer::INTENT_SEARCH, $result['intent']);
        $this->assertSame('jawa barat', $result['region']);
        $this->assertSame(2024, $result['year']);
        $this->assertContains('penduduk', $result['keywords']);
        $this->assertContains('kependudukan', $result['keywords']);
        $this->assertContains('demografi', $result['keywords']);
    }

    public function test_it_resolves_follow_up_questions_using_history(): void
    {
        $result = $this->analyzer->analyze('kalau tahun 2020 ada?', [
            ['role' => 'user', 'text' => 'Ada publikasi IPM?'],
            ['role' => 'model', 'text' => 'Ada, berikut publikasinya.'],
        ]);

        $this->assertTrue($result['is_follow_up']);
        $this->assertSame(2020, $result['year']);
        $this->assertContains('ipm', $result['keywords']);
        $this->assertContains('indeks pembangunan manusia', $result['keywords']);
    }

    public function test_it_detects_common_intents(): void
    {
        $this->assertSame(QueryAnalyzer::INTENT_GREETING, $this->analyzer->analyze('Halo kak')['intent']);
        $this->assertSame(QueryAnalyzer::INTENT_DEFINITION, $this->analyzer->analyze('apa itu IPM?')['intent']);
        $this->assertSame(QueryAnalyzer::INTENT_SUMMARIZE, $this->analyzer->analyze('tolong rangkum dong')['intent']);
        $this->assertSame(QueryAnalyzer::INTENT_LIST, $this->analyzer->analyze('publikasi apa saja yang tersedia?')['intent']);
    }

    public function test_short_standalone_questions_are_not_treated_as_follow_ups(): void
    {
        $result = $this->analyzer->analyze('data penduduk', [
            ['role' => 'user', 'text' => 'Apa itu inflasi?'],
        ]);

        $this->assertFalse($result['is_follow_up']);
    }
}
