<?php

namespace Tests\Feature;

use App\Models\Publication;
use App\Models\User;
use App\Models\VisitLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
            'status' => 'Pending',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Katalog publik')
            ->assertSee('data-lucide="trending-up"', false)
            ->assertSee('/home?category=Statistik%20Ekonomi')
            ->assertSee('1 publikasi');
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

        $this->actingAs($user)
            ->get('/publications/' . $publication->id)
            ->assertOk();
        $this->get('/publications/' . $publication->id)->assertOk();
        $this->get('/publications/' . $publication->id . '/download')
            ->assertDownload('dokumen-statistik.pdf');

        $this->assertSame(1, VisitLog::where('event_type', 'view')->count(), 'A page is counted once per session.');
        $this->assertSame(1, VisitLog::where('event_type', 'download')->count());
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

        $this->actingAs($user)
            ->postJson('/chatbot/send', ['message' => 'Ada publikasi kemiskinan?'])
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('reply', "💡 *Gemini belum dikonfigurasi; saya menjawab dari data yang tersimpan di RINGKAS.*\n\nBerikut publikasi yang mungkin relevan. Pastikan kembali detailnya pada dokumen sumber:\n\n📘 **Statistik Kemiskinan Kota Tasikmalaya** (2026)\n• Kategori: Statistik Sosial | Wilayah: Nasional\n👉 [Buka Publikasi](/publications/1)\n\n");
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

        $this->actingAs($user)
            ->postJson('/chatbot/send', ['message' => 'rangkum'])
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonFragment(['reply' => "💡 *Koneksi Gemini sedang bermasalah; saya menjawab dari data yang tersimpan di RINGKAS.*\n\n📘 **Statistik Perusahaan Hortikultura 2026** (2026)\n\nPublikasi ini merangkum jumlah usaha dan tenaga kerja hortikultura.\n\n**Kesimpulan:** Usaha hortikultura tersebar di berbagai wilayah.\n\n[Buka publikasi](/publications/{$publication->id})"]);
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
}
