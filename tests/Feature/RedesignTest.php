<?php

namespace Tests\Feature;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedesignTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['username' => 'review-admin', 'role' => 'admin']);
    }

    private function publication(string $title, string $status = 'Selesai'): Publication
    {
        return Publication::create(['title' => $title, 'category' => 'Statistik Sosial', 'year' => 2026,
            'release_date' => '2026-01-01', 'pdf_path' => 'publications/test.pdf', 'status' => $status]);
    }

    private function addSummary(Publication $publication, string $summary): void
    {
        $publication->aiResult()->create(['summary' => $summary, 'publication_information' => [],
            'topics' => [], 'keywords' => [], 'key_points' => [], 'indicators' => [],
            'trends' => [], 'discussion_locations' => [], 'conclusion' => '']);
    }

    public function test_public_landing_and_private_access_are_preserved(): void
    {
        $publication = $this->publication('Publikasi privat');
        $this->get('/')->assertOk()->assertSee('Ilustrasi antarmuka')->assertSee(route('login'))
            ->assertDontSee('4.500+')->assertDontSee('&lt; 30 Detik', false);
        foreach (['/home', '/profile', '/publications/'.$publication->id, '/admin/dashboard', '/admin/summaries'] as $url) {
            $this->get($url)->assertRedirect();
        }
        foreach (['/login', '/register', '/admin/login'] as $url) {
            $this->get($url)->assertOk()->assertSee('autocomplete="username"', false);
        }
    }

    public function test_summaries_only_list_actual_completed_results_and_search_their_contents(): void
    {
        $this->actingAs($this->admin());
        $complete = $this->publication('Dokumen dengan hasil');
        $this->addSummary($complete, 'Indikator kesejahteraan pendidikan');
        $this->publication('Dokumen tanpa hasil');
        $pending = $this->publication('Dokumen masih pending', 'Pending');
        $this->addSummary($pending, 'Hasil lama pendidikan');
        $this->get('/admin/summaries?search=pendidikan')->assertOk()
            ->assertSee('Semua Ringkasan')->assertSee('Dokumen dengan hasil')
            ->assertDontSee('Dokumen tanpa hasil')->assertDontSee('Dokumen masih pending');
        $this->get('/admin/summaries?search=tidak-ditemukan')->assertOk()->assertSee('Tidak ada ringkasan yang sesuai');
    }

    public function test_empty_statistics_have_no_chart_canvas_or_fabricated_distribution(): void
    {
        $this->actingAs($this->admin())->get('/admin/dashboard?view=statistics')->assertOk()
            ->assertSee('Statistik Ringkasan')->assertSee('Belum ada data kategori')
            ->assertDontSee('<canvas', false)->assertDontSee('[1, 1, 1, 1, 1, 1, 1, 1]', false);
    }

    public function test_summary_metrics_use_actual_results_and_their_creation_date(): void
    {
        $this->actingAs($this->admin());
        $publication = $this->publication('Publikasi lama');
        $publication->forceFill(['created_at' => now()->subYear()])->save();
        $this->addSummary($publication, 'Ringkasan baru');
        $this->publication('Status selesai tanpa hasil');
        $this->get('/admin/dashboard')->assertOk()->assertViewHas('totalSummaries', 1)
            ->assertViewHas('monthlyData', fn ($data) => array_sum($data) === 1)
            ->assertSee('<canvas id="categoryChart"', false)->assertSee('Lihat rincian data');
    }

    public function test_all_admin_pages_and_publication_readers_render(): void
    {
        $admin = $this->admin();
        $publication = $this->publication('Dokumen uji tampilan');
        $this->addSummary($publication, 'Ringkasan untuk uji tampilan.');
        $this->actingAs($admin);
        foreach (['/admin/dashboard', '/admin/publications', '/admin/publications/create',
            '/admin/publications/'.$publication->id, '/admin/publications/'.$publication->id.'/edit',
            '/admin/summaries', '/admin/activities', '/admin/account-settings', '/admin/users',
            '/admin/users/create', '/admin/users/'.$admin->id.'/edit', '/home', '/profile',
            '/publications/'.$publication->id] as $url) {
            $response = $this->get($url)->assertOk();
            if (getenv('RINGKAS_CAPTURE_HTML')) {
                $directory = storage_path('app/redesign-qa');
                if (!is_dir($directory)) mkdir($directory, 0755, true);
                $html = str_replace(['http://localhost', 'https://localhost'], 'http://127.0.0.1:8000', $response->getContent());
                $html = str_replace(['http://127.0.0.1:8000/build/', 'http://127.0.0.1:8000/images/'], ['/build/', '/images/'], $html);
                file_put_contents($directory.'/'.str_replace('/', '-', trim($url, '/')).'.html', $html);
            }
        }
    }
}
