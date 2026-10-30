<?php

namespace Tests\Feature;

use App\Models\Publication;
use App\Models\User;
use App\Models\VisitLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RedesignedViewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_redesigned_admin_pages_render_with_original_application_data(): void
    {
        $admin = User::create([
            'name' => 'Admin UI',
            'username' => 'admin-ui',
            'email' => 'admin-ui@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'admin',
        ]);

        $publication = Publication::create([
            'title' => 'Publikasi Uji Antarmuka',
            'category' => 'Publikasi Umum',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/ui-test.pdf',
            'summary' => 'Ringkasan untuk pengujian tampilan.',
            'status' => 'Selesai',
        ]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Panel administrator');

        $this->get('/admin/publications')
            ->assertOk()
            ->assertSee($publication->title);

        $this->get('/admin/publications/create')
            ->assertOk()
            ->assertSee('Unggah publikasi');

        $this->get('/admin/summaries')
            ->assertOk()
            ->assertSee($publication->title)
            ->assertSee('Ringkasan untuk pengujian tampilan.');

        $this->get('/admin/publications/' . $publication->id)
            ->assertOk()
            ->assertSee($publication->title);

        $this->get('/admin/account-settings')->assertOk();
        $this->get('/admin/activities')->assertOk();
        $this->get('/admin/users')->assertOk();
        $this->get('/admin/users/create')->assertOk();

        $managedUser = User::create([
            'name' => 'User UI',
            'username' => 'user-ui',
            'email' => 'user-ui@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'user',
        ]);
        $this->get('/admin/users/' . $managedUser->id . '/edit')->assertOk();
        $this->actingAs($managedUser)->get('/profile')->assertOk();
        $this->get('/home')
            ->assertSee('bpsChatbotRoot')
            ->assertSee('Tanya BPS AI')
            ->assertSee('Kategori Publikasi')
            ->assertSee('data-lucide="users"', false)
            ->assertSee('Lihat Ringkasan');
    }

    public function test_redesigned_authentication_pages_render_for_guests(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk')->assertDontSee('portal admin');
        $this->get('/register')->assertOk()->assertSee('Buat akun');
        $this->get('/admin/login')->assertOk()->assertSee('Masuk');
    }

    public function test_public_landing_shows_category_cards_and_records_anonymous_visits(): void
    {
        Publication::create([
            'title' => 'Publikasi Landing Test',
            'category' => 'Statistik Ekonomi',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/landing.pdf',
            'summary' => 'Ringkasan publikasi landing.',
            'status' => 'Selesai',
        ]);

        // Publikasi yang belum diringkas tidak boleh dihitung di landing publik.
        Publication::create([
            'title' => 'Publikasi Belum Ringkas',
            'category' => 'Statistik Sosial',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/pending.pdf',
            'status' => 'Pending',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Katalog publik')
            ->assertSee('data-lucide="trending-up"', false)
            ->assertSee('/home?category=Statistik%20Ekonomi')
            ->assertSee('1 publikasi')
            ->assertDontSee('2 publikasi');
        $this->get('/');

        $this->assertSame(1, VisitLog::where('event_type', 'view')->count(), 'An anonymous landing visit is counted once per session.');
    }

    public function test_user_page_visits_and_pdf_downloads_are_counted_for_admin_statistics(): void
    {
        $user = User::create([
            'name' => 'User Statistik',
            'username' => 'user-statistik',
            'email' => 'user-statistik@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'user',
        ]);
        $publication = Publication::create([
            'title' => 'Dokumen Statistik',
            'category' => 'Publikasi Umum',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/dokumen-statistik.pdf',
            'summary' => 'Ringkasan uji statistik.',
            'status' => 'Selesai',
        ]);

        Storage::disk('public')->put($publication->pdf_path, '%PDF-test');

        $this->withSession(['visited:user.publications.show:' . $publication->id => true])
            ->actingAs($user)
            ->get('/publications/' . $publication->id)
            ->assertOk();
        $this->get('/publications/' . $publication->id)->assertOk();
        $this->get('/publications/' . $publication->id . '/download')
            ->assertDownload('dokumen-statistik.pdf');

        $this->assertSame(1, VisitLog::where('event_type', 'view')->count(), 'Reloads within the visit cooldown do not inflate the count.');
        $this->assertSame(1, VisitLog::where('event_type', 'download')->count());

        $this->travel(6)->minutes();
        $this->get('/publications/' . $publication->id)->assertOk();
        $this->assertSame(2, VisitLog::where('event_type', 'view')->count(), 'A return visit is counted after the cooldown.');
    }

    public function test_admin_visit_dashboard_aggregates_recent_visits_and_renders_popular_and_recent_activity(): void
    {
        Carbon::setTestNow('2026-10-30 12:00:00');

        try {
            $admin = User::create([
                'name' => 'Admin Statistik',
                'username' => 'admin-statistik',
                'email' => 'admin-statistik@example.test',
                'password' => Hash::make('temporary-test-password'),
                'role' => 'admin',
            ]);
            $publication = Publication::create([
                'title' => 'Publikasi Paling Sering Dibaca',
                'category' => 'Statistik Sosial',
                'year' => 2026,
                'release_date' => '2026-01-01',
                'pdf_path' => 'publications/most-read.pdf',
                'summary' => 'Ringkasan uji.',
                'status' => 'Selesai',
            ]);

            foreach ([
                ['2026-10-30 10:00:00', 'Jawa Barat'],
                ['2026-10-29 10:00:00', 'Jawa Barat'],
                ['2026-10-20 10:00:00', 'DKI Jakarta'],
            ] as $index => [$createdAt, $region]) {
                $visit = VisitLog::create([
                    'publication_id' => $publication->id,
                    'event_type' => 'view',
                    'ip_address' => '203.0.113.' . ($index + 1),
                    'region' => $region,
                    'url' => '/publications/' . $publication->id,
                ]);
                $visit->forceFill(['created_at' => Carbon::parse($createdAt)])->save();
            }

            $response = $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
            $response->assertViewHas('visitStats', function (array $stats) use ($publication) {
                return $stats['visitsByDay']['30 Oct'] === 1
                    && $stats['visitsByDay']['29 Oct'] === 1
                    && !isset($stats['visitsByDay']['20 Oct'])
                    && $stats['visitsByRegion']['Jawa Barat'] === 2
                    && $stats['topPublications']->first()->publication_id === $publication->id
                    && $stats['recentVisits']->count() === 3;
            });
            $response->assertSee('Publikasi Terpopuler')
                ->assertSee('Aktivitas Terbaru')
                ->assertSee('Publikasi Paling Sering Dibaca')
                ->assertSee('Jawa Barat');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_visit_geolocation_uses_vercel_region_headers_without_external_lookup(): void
    {
        Config::set('services.geoip.use_vercel_headers', true);
        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_X_VERCEL_IP_COUNTRY' => 'ID',
            'HTTP_X_VERCEL_IP_COUNTRY_REGION' => 'JB',
            'HTTP_X_VERCEL_IP_CITY' => 'Bandung%20Kota',
        ]);

        $location = app(\App\Services\IpGeolocationService::class)->locateRequest($request);

        $this->assertSame('Jawa Barat', $location['region']);
        $this->assertSame('Bandung Kota', $location['city']);
    }

    public function test_chatbot_routes_return_suggestions_and_a_safe_fallback_without_an_api_key(): void
    {
        $user = User::create([
            'name' => 'User Chatbot',
            'username' => 'user-chatbot',
            'email' => 'user-chatbot@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->getJson('/chatbot/suggestions')
            ->assertOk()
            ->assertJsonPath('mode', 'universal');

        $this->postJson('/chatbot/send', ['message' => 'Cari publikasi tentang IPM'])
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['reply', 'mode']);
    }

    public function test_chatbot_uses_the_publication_catalog_when_gemini_is_not_configured(): void
    {
        config(['services.gemini.api_key' => null]);
        $user = User::create([
            'name' => 'User Katalog',
            'username' => 'user-katalog',
            'email' => 'user-katalog@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'user',
        ]);
        Publication::create([
            'title' => 'Statistik Kemiskinan Kota Tasikmalaya',
            'category' => 'Statistik Sosial',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/kemiskinan.pdf',
            'summary' => 'Ringkasan kemiskinan untuk pengujian.',
            'status' => 'Selesai',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/chatbot/send', ['message' => 'Ada publikasi kemiskinan?'])
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('mode', 'universal');

        $reply = $response->json('reply');

        $this->assertStringContainsString('Statistik Kemiskinan Kota Tasikmalaya', $reply);
        $this->assertStringContainsString('/publications/', $reply);
        $this->assertStringContainsString('Gemini belum dikonfigurasi', $reply);
    }

    public function test_chatbot_summarizes_the_only_publication_when_user_says_rangkum(): void
    {
        config(['services.gemini.api_key' => 'test-api-key']);
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['error' => ['message' => 'temporary failure']], 503)]);
        $user = User::create([
            'name' => 'User Ringkasan',
            'username' => 'user-ringkasan',
            'email' => 'user-ringkasan@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'user',
        ]);
        $publication = Publication::create([
            'title' => 'Statistik Perusahaan Hortikultura 2026',
            'category' => 'Statistik Pertanian',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/hortikultura.pdf',
            'summary' => 'Publikasi ini merangkum jumlah usaha dan tenaga kerja hortikultura.',
            'conclusion' => 'Usaha hortikultura tersebar di berbagai wilayah.',
            'status' => 'Selesai',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/chatbot/send', ['message' => 'rangkum'])
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $reply = $response->json('reply');

        $this->assertStringContainsString('Koneksi Gemini sedang bermasalah', $reply);
        $this->assertStringContainsString('Statistik Perusahaan Hortikultura 2026', $reply);
        $this->assertStringContainsString('Publikasi ini merangkum jumlah usaha dan tenaga kerja hortikultura.', $reply);
        $this->assertStringContainsString('Usaha hortikultura tersebar di berbagai wilayah.', $reply);
        $this->assertStringContainsString('/publications/' . $publication->id, $reply);
    }

    public function test_chatbot_fallback_finds_publications_from_topics_and_summary(): void
    {
        config(['services.gemini.api_key' => null]);

        $user = User::create([
            'name' => 'User Topik',
            'username' => 'user-topik',
            'email' => 'user-topik@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'user',
        ]);

        Publication::create([
            'title' => 'Laporan Tahunan Daerah',
            'category' => 'Statistik Sosial',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/laporan-tahunan.pdf',
            'summary' => 'Analisis kemiskinan dan kesejahteraan masyarakat di daerah.',
            'topics' => ['Kemiskinan', 'Kesejahteraan'],
            'status' => 'Selesai',
        ]);

        $reply = $this->actingAs($user)
            ->postJson('/chatbot/send', ['message' => 'ada data kemiskinan ga?'])
            ->assertOk()
            ->json('reply');

        $this->assertStringContainsString('Laporan Tahunan Daerah', $reply);
    }

    public function test_chatbot_fallback_understands_follow_up_year_questions(): void
    {
        config(['services.gemini.api_key' => null]);

        $user = User::create([
            'name' => 'User Lanjutan',
            'username' => 'user-lanjutan',
            'email' => 'user-lanjutan@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'user',
        ]);

        Publication::create([
            'title' => 'Indeks Pembangunan Manusia 2025',
            'category' => 'Publikasi Umum',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/ipm.pdf',
            'summary' => 'Ringkasan capaian IPM.',
            'topics' => ['Indeks Pembangunan Manusia'],
            'status' => 'Selesai',
        ]);

        $reply = $this->actingAs($user)
            ->postJson('/chatbot/send', [
                'message' => 'kalau tahun 2020 ada?',
                'history' => [
                    ['role' => 'user', 'text' => 'Apakah ada publikasi IPM?'],
                    ['role' => 'model', 'text' => 'Ada, berikut publikasinya.'],
                ],
            ])
            ->assertOk()
            ->json('reply');

        $this->assertStringContainsString('2020', $reply);
        $this->assertStringContainsString('Indeks Pembangunan Manusia 2025', $reply);
    }

    public function test_chatbot_fallback_offers_catalog_hints_when_nothing_matches(): void
    {
        config(['services.gemini.api_key' => null]);

        $user = User::create([
            'name' => 'User Tidak Cocok',
            'username' => 'user-tidak-cocok',
            'email' => 'user-tidak-cocok@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'user',
        ]);

        Publication::create([
            'title' => 'Statistik Pertanian',
            'category' => 'Statistik Pertanian',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/pertanian.pdf',
            'summary' => 'Ringkasan pertanian.',
            'status' => 'Selesai',
        ]);

        $reply = $this->actingAs($user)
            ->postJson('/chatbot/send', ['message' => 'zzzqqq'])
            ->assertOk()
            ->json('reply');

        $this->assertStringContainsString('belum menemukan', $reply);
        $this->assertStringContainsString('Kategori tersedia', $reply);
        $this->assertStringContainsString('Tahun tersedia', $reply);
    }

    public function test_chatbot_removes_links_to_publications_that_do_not_exist(): void
    {
        config(['services.gemini.api_key' => 'test-api-key']);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => 'Lihat [Publikasi Asli](/publications/1) dan [Publikasi Hantu](/publications/999).',
                        ]],
                    ],
                ]],
            ], 200),
        ]);

        $user = User::create([
            'name' => 'User Sanitasi',
            'username' => 'user-sanitasi',
            'email' => 'user-sanitasi@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'user',
        ]);

        Publication::create([
            'title' => 'Publikasi Asli',
            'category' => 'Publikasi Umum',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/asli.pdf',
            'summary' => 'Ringkasan asli.',
            'status' => 'Selesai',
        ]);

        $reply = $this->actingAs($user)
            ->postJson('/chatbot/send', ['message' => 'tampilkan publikasi'])
            ->assertOk()
            ->json('reply');

        $this->assertStringContainsString('/publications/1', $reply);
        $this->assertStringNotContainsString('/publications/999', $reply);
        $this->assertStringContainsString('Publikasi Hantu', $reply);
    }

    public function test_chatbot_sends_relevant_publication_context_to_gemini(): void
    {
        config(['services.gemini.api_key' => 'test-api-key']);

        $capturedBody = '';

        Http::fake(function ($request) use (&$capturedBody) {
            $capturedBody = $request->body();

            return Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [['text' => 'Berikut publikasi yang relevan.']],
                    ],
                ]],
            ], 200);
        });

        $user = User::create([
            'name' => 'User Konteks',
            'username' => 'user-konteks',
            'email' => 'user-konteks@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'user',
        ]);

        Publication::create([
            'title' => 'Statistik Kemiskinan Kota Tasikmalaya',
            'category' => 'Statistik Sosial',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/kemiskinan-kota.pdf',
            'summary' => 'Analisis kemiskinan di Kota Tasikmalaya.',
            'topics' => ['Kemiskinan'],
            'status' => 'Selesai',
        ]);

        $this->actingAs($user)
            ->postJson('/chatbot/send', ['message' => 'Bagaimana data kemiskinan Tasikmalaya?'])
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertStringContainsString('Statistik Kemiskinan Kota Tasikmalaya', $capturedBody);
        $this->assertStringContainsString('DETAIL PUBLIKASI PALING RELEVAN', $capturedBody);
    }

    public function test_admin_can_upload_a_pdf_through_the_original_upload_flow(): void
    {
        Storage::fake('public');
        $admin = User::create([
            'name' => 'Admin Unggah',
            'username' => 'admin-unggah',
            'email' => 'admin-unggah@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/publications', [
            'title' => 'Publikasi Upload Test',
            'category' => 'Publikasi Umum',
            'release_date' => '2026-10-26',
            'pdf' => UploadedFile::fake()->createWithContent('upload-test.pdf', '%PDF-1.4 test'),
        ]);

        $response->assertRedirect(route('publications.index'));
        $this->assertDatabaseHas('publications', ['title' => 'Publikasi Upload Test']);
        Storage::disk('public')->assertExists(Publication::where('title', 'Publikasi Upload Test')->value('pdf_path'));
    }

    public function test_admin_blob_upload_metadata_is_persisted_and_blob_urls_are_restricted(): void
    {
        config(['services.vercel_blob.enabled' => true]);
        $admin = User::create([
            'name' => 'Admin Blob',
            'username' => 'admin-blob',
            'email' => 'admin-blob@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/publications', [
            'title' => 'Publikasi Blob Test',
            'category' => 'Publikasi Umum',
            'release_date' => '2026-10-26',
            'pdf_blob_url' => 'https://example.public.blob.vercel-storage.com/publications/test.pdf',
            'uploaded_file_size' => 1024,
        ]);

        $response->assertRedirect(route('publications.index'));
        $this->assertDatabaseHas('publications', [
            'title' => 'Publikasi Blob Test',
            'pdf_path' => 'https://example.public.blob.vercel-storage.com/publications/test.pdf',
        ]);

        $this->from('/admin/publications/create')->post('/admin/publications', [
            'title' => 'Publikasi Blob Tidak Valid',
            'category' => 'Publikasi Umum',
            'release_date' => '2026-10-26',
            'pdf_blob_url' => 'https://attacker.example/publications/evil.pdf',
            'uploaded_file_size' => 1024,
        ])->assertRedirect('/admin/publications/create')->assertSessionHasErrors('pdf');
    }

    public function test_vercel_upload_never_falls_back_to_ephemeral_local_storage(): void
    {
        config(['services.vercel_blob.serverless' => true]);
        Storage::fake('public');
        $admin = User::create([
            'name' => 'Admin Vercel',
            'username' => 'admin-vercel',
            'email' => 'admin-vercel@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)->from('/admin/publications/create')->post('/admin/publications', [
            'title' => 'Publikasi Tanpa Blob',
            'category' => 'Publikasi Umum',
            'release_date' => '2026-10-26',
            'pdf' => UploadedFile::fake()->createWithContent('no-blob.pdf', '%PDF-1.4 test'),
        ])->assertRedirect('/admin/publications/create')->assertSessionHasErrors('pdf');

        $this->assertDatabaseMissing('publications', ['title' => 'Publikasi Tanpa Blob']);
        Storage::disk('public')->assertMissing('publications');
    }

    public function test_repeat_page_visits_are_counted_again_after_the_dedup_window(): void
    {
        $this->get('/')->assertOk();
        $this->get('/')->assertOk();
        $this->assertSame(1, VisitLog::where('event_type', 'view')->count(), 'Refresh beruntun dalam 15 menit dihitung sekali.');

        $this->travel(16)->minutes();

        $this->get('/')->assertOk();
        $this->assertSame(2, VisitLog::where('event_type', 'view')->count(), 'Kunjungan ulang setelah 15 menit tercatat lagi.');
    }

    public function test_successful_user_login_is_recorded_as_activity(): void
    {
        $user = User::create([
            'name' => 'User Login',
            'username' => 'user-login',
            'email' => 'user-login@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'user',
        ]);

        $this->post('/login', [
            'username' => 'user-login',
            'password' => 'temporary-test-password',
        ])->assertRedirect(route('home'));

        $this->assertDatabaseHas('visit_logs', [
            'event_type' => 'login',
            'user_id' => $user->id,
            'url' => '/login',
        ]);
    }

    public function test_successful_admin_login_is_recorded_as_activity(): void
    {
        $admin = User::create([
            'name' => 'Admin Login',
            'username' => 'admin-login',
            'email' => 'admin-login@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'admin',
        ]);

        $this->post('/admin/login', [
            'username' => 'admin-login',
            'password' => 'temporary-test-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('visit_logs', [
            'event_type' => 'login',
            'user_id' => $admin->id,
        ]);
        $this->assertSame(1, VisitLog::where('event_type', 'login')->count(), 'Satu login hanya boleh tercatat satu kali.');
    }

    public function test_registration_is_recorded_once_without_a_duplicate_login_event(): void
    {
        $this->post('/register', [
            'username' => 'user-baru',
            'email' => 'user-baru@example.test',
            'password' => 'temporary-test-password',
            'password_confirmation' => 'temporary-test-password',
        ])->assertRedirect(route('home'));

        $this->assertSame(1, VisitLog::where('event_type', 'register')->count());
        $this->assertSame(0, VisitLog::where('event_type', 'login')->count(), 'Registrasi tidak boleh dihitung dua kali.');
    }

    public function test_admin_dashboard_reports_visits_regions_publications_and_recent_activity(): void
    {
        $admin = User::create([
            'name' => 'Admin Statistik',
            'username' => 'admin-statistik',
            'email' => 'admin-statistik@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'Pembaca Aktif',
            'username' => 'pembaca-aktif',
            'email' => 'pembaca-aktif@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'user',
        ]);

        $popular = Publication::create([
            'title' => 'Publikasi Paling Populer',
            'category' => 'Statistik Ekonomi',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/populer.pdf',
            'summary' => 'Ringkasan populer.',
            'status' => 'Selesai',
        ]);

        $other = Publication::create([
            'title' => 'Publikasi Biasa',
            'category' => 'Statistik Sosial',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/biasa.pdf',
            'summary' => 'Ringkasan biasa.',
            'status' => 'Selesai',
        ]);

        foreach ([1, 2, 3] as $daysAgo) {
            $this->recordVisitLog([
                'publication_id' => $popular->id,
                'region' => 'Jawa Barat',
                'city' => 'Bandung',
                'ip_address' => '36.1.1.' . $daysAgo,
            ], now()->subDays($daysAgo));
        }

        $this->recordVisitLog([
            'publication_id' => $other->id,
            'region' => 'Jawa Barat',
            'city' => 'Bandung',
            'ip_address' => '36.1.1.1',
        ], now()->subDay());

        $this->recordVisitLog([
            'event_type' => 'download',
            'publication_id' => $popular->id,
            'region' => 'DKI Jakarta',
            'city' => 'Jakarta',
            'ip_address' => '36.1.1.9',
            'url' => '/publications/' . $popular->id . '/download',
        ], now());

        $this->recordVisitLog([
            'event_type' => 'login',
            'user_id' => $user->id,
            'region' => 'Sulawesi Selatan',
            'city' => 'Makassar',
            'ip_address' => '36.1.1.9',
            'url' => '/login',
        ], now());

        // Di luar jendela 7 hari: tidak boleh masuk grafik harian.
        $this->recordVisitLog([
            'event_type' => 'register',
            'region' => 'Papua',
            'city' => 'Jayapura',
            'ip_address' => '36.1.1.7',
            'url' => '/register',
        ], now()->subDays(10));

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertOk();

        $response->assertViewHas('visitStats', function (array $stats) {
            return $stats['trackingAvailable'] === true
                && $stats['totalVisits'] === 4
                && $stats['totalDownloads'] === 1
                && $stats['totalLogins'] === 1
                && $stats['uniqueVisitors'] === 4
                && array_sum($stats['visitsByDay']) === 5
                && (int) ($stats['visitsByRegion']['Bandung'] ?? 0) === 4
                && (int) ($stats['visitsByRegion']['Makassar'] ?? 0) === 1
                && $stats['topPublications']->first()->publication->title === 'Publikasi Paling Populer'
                && (int) $stats['topPublications']->first()->total === 4;
        });

        $response->assertSee('Kunjungan 7 Hari Terakhir')
            ->assertSee('Kunjungan per Wilayah (Top 8)')
            ->assertDontSee('tracking-wider">Halaman</th>', false)
            ->assertSee('Jawa Barat')
            ->assertSee('Sulawesi Selatan')
            ->assertSee('Bandung')
            ->assertSeeInOrder(['Publikasi Paling Populer', 'Publikasi Biasa'])
            ->assertSee('Registrasi')
            ->assertSee('Unduhan')
            ->assertSee('Pembaca Aktif');
    }

    public function test_visit_region_chart_uses_the_visitor_city_and_falls_back_to_province(): void
    {
        $admin = User::create([
            'name' => 'Admin Wilayah',
            'username' => 'admin-wilayah',
            'email' => 'admin-wilayah@example.test',
            'password' => Hash::make('temporary-test-password'),
            'role' => 'admin',
        ]);

        $this->recordVisitLog(['region' => 'Jawa Barat', 'city' => 'Tasikmalaya', 'ip_address' => '36.2.2.1'], now());
        $this->recordVisitLog(['region' => 'Jawa Barat', 'city' => 'Tasikmalaya', 'ip_address' => '36.2.2.2'], now());
        // Kota tidak diketahui → pakai provinsi.
        $this->recordVisitLog(['region' => 'Lampung', 'city' => null, 'ip_address' => '36.2.2.3'], now());

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertOk();

        $response->assertViewHas('visitStats', function (array $stats) {
            return (int) ($stats['visitsByRegion']['Tasikmalaya'] ?? 0) === 2
                && (int) ($stats['visitsByRegion']['Lampung'] ?? 0) === 1;
        });

        $response->assertSee('Tasikmalaya')
            ->assertSee('Tasikmalaya, Jawa Barat');
    }

    /**
     * Buat satu baris visit_logs dengan waktu tertentu.
     */
    private function recordVisitLog(array $attributes, $createdAt): VisitLog
    {
        $log = VisitLog::create(array_merge([
            'event_type' => 'view',
            'url' => '/home',
            'user_agent' => 'phpunit',
        ], $attributes));

        $log->forceFill(['created_at' => $createdAt])->save();

        return $log;
    }
}
