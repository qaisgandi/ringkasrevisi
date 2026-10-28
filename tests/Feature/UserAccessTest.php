<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Publication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccessTest extends TestCase
{
    use RefreshDatabase;
    public function test_public_user_can_access_home_and_view_summary(): void
    {
        $user = User::create([
            'name' => 'User Publik',
            'username' => 'user1',
            'email' => 'user@bps.go.id',
            'password' => \Illuminate\Support\Facades\Hash::make('user1234'),
            'role' => 'user',
        ]);

        $publication = Publication::create([
            'title' => 'Test Publikasi',
            'category' => 'Publikasi Umum',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/test.pdf',
            'status' => 'Selesai',
        ]);

        // User can access home
        $response = $this->actingAs($user)->get('/home');
        $response->assertStatus(200);

        // User can view publication summary
        $response = $this->actingAs($user)->get("/publications/{$publication->id}");
        $response->assertStatus(200);
        $response->assertSee($publication->title);

        // User CANNOT access admin dashboard (redirected to home)
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertRedirect('/home');
    }

    public function test_admin_on_user_portal_does_not_get_redirected_to_admin_dashboard_on_refresh(): void
    {
        $admin = User::create([
            'name' => 'Admin BPS',
            'username' => 'admin1',
            'email' => 'admin@bps.go.id',
            'password' => \Illuminate\Support\Facades\Hash::make('admin1234'),
            'role' => 'admin',
        ]);

        $publication = Publication::create([
            'title' => 'Test Publikasi',
            'category' => 'Publikasi Umum',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/test.pdf',
            'status' => 'Selesai',
        ]);

        // Admin visiting or refreshing /home stays on /home (200 OK, not redirected to admin.dashboard)
        $response = $this->actingAs($admin)->get('/home');
        $response->assertStatus(200);

        // Admin visiting or refreshing publication summary stays on the summary page
        $response = $this->actingAs($admin)->get("/publications/{$publication->id}");
        $response->assertStatus(200);
        $response->assertSee($publication->title);

        // Admin refreshing landing page / redirects to /home
        $response = $this->actingAs($admin)->get('/');
        $response->assertRedirect('/home');
    }

    public function test_user_login_portal_success(): void
    {
        $user = User::create([
            'name' => 'User Publik',
            'username' => 'user1',
            'email' => 'user@bps.go.id',
            'password' => \Illuminate\Support\Facades\Hash::make('user1234'),
            'role' => 'user',
        ]);

        $response = $this->post('/login', [
            'username' => 'user1',
            'password' => 'user1234',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_from_admin_login_form(): void
    {
        $user = User::create([
            'name' => 'User Publik',
            'username' => 'user1',
            'email' => 'user@bps.go.id',
            'password' => \Illuminate\Support\Facades\Hash::make('user1234'),
            'role' => 'user',
        ]);

        $response = $this->post('/admin/login', [
            'username' => 'user1',
            'password' => 'user1234',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_admin_cannot_login_from_user_login_form(): void
    {
        $admin = User::create([
            'name' => 'Admin BPS',
            'username' => 'admin1',
            'email' => 'admin@bps.go.id',
            'password' => \Illuminate\Support\Facades\Hash::make('admin1234'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'username' => 'admin1',
            'password' => 'admin1234',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_admin_login_portal_success(): void
    {
        $admin = User::create([
            'name' => 'Admin BPS',
            'username' => 'admin1',
            'email' => 'admin@bps.go.id',
            'password' => \Illuminate\Support\Facades\Hash::make('admin1234'),
            'role' => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'username' => 'admin1',
            'password' => 'admin1234',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_refreshing_summary_page_preserves_user_session_and_page(): void
    {
        $user = User::create([
            'name' => 'User Publik',
            'username' => 'user1',
            'email' => 'user@bps.go.id',
            'password' => \Illuminate\Support\Facades\Hash::make('user1234'),
            'role' => 'user',
        ]);

        $publication = Publication::create([
            'title' => 'Test Publikasi Hasil Sensus',
            'category' => 'Sensus & Survei',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/test.pdf',
            'status' => 'Selesai',
        ]);

        // First visit to publication summary
        $response = $this->actingAs($user)->get("/publications/{$publication->id}");
        $response->assertStatus(200);
        $response->assertSee('Test Publikasi Hasil Sensus');

        // Refresh publication summary (second GET request)
        $response = $this->actingAs($user)->get("/publications/{$publication->id}");
        $response->assertStatus(200);
        $response->assertSee('Test Publikasi Hasil Sensus');

        // Returning to home
        $response = $this->actingAs($user)->get('/home');
        $response->assertStatus(200);

        // User is still authenticated and NEVER redirected to admin dashboard
        $this->assertAuthenticatedAs($user);
    }

    public function test_home_only_lists_publications_that_have_been_summarized(): void
    {
        $user = User::create([
            'name' => 'User Katalog',
            'username' => 'user-katalog',
            'email' => 'user-katalog@bps.go.id',
            'password' => \Illuminate\Support\Facades\Hash::make('user1234'),
            'role' => 'user',
        ]);

        Publication::create([
            'title' => 'Publikasi Sudah Ringkas',
            'category' => 'Publikasi Umum',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/sudah.pdf',
            'summary' => 'Ringkasan siap dibaca.',
            'status' => 'Selesai',
        ]);

        Publication::create([
            'title' => 'Publikasi Belum Ringkas',
            'category' => 'Publikasi Umum',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/belum.pdf',
            'status' => 'Pending',
        ]);

        $this->actingAs($user)
            ->get('/home')
            ->assertOk()
            ->assertSee('Publikasi Sudah Ringkas')
            ->assertDontSee('Publikasi Belum Ringkas');
    }

    public function test_pending_publication_detail_is_not_accessible_to_public_user(): void
    {
        $user = User::create([
            'name' => 'User Publik',
            'username' => 'user1',
            'email' => 'user@bps.go.id',
            'password' => \Illuminate\Support\Facades\Hash::make('user1234'),
            'role' => 'user',
        ]);

        $publication = Publication::create([
            'title' => 'Publikasi Belum Ringkas',
            'category' => 'Publikasi Umum',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/belum.pdf',
            'status' => 'Pending',
        ]);

        $this->actingAs($user)
            ->get("/publications/{$publication->id}")
            ->assertRedirect(route('home'))
            ->assertSessionHas('error');
    }

    public function test_pending_publication_cannot_be_downloaded_by_public_user(): void
    {
        $user = User::create([
            'name' => 'User Publik',
            'username' => 'user1',
            'email' => 'user@bps.go.id',
            'password' => \Illuminate\Support\Facades\Hash::make('user1234'),
            'role' => 'user',
        ]);

        $publication = Publication::create([
            'title' => 'Publikasi Belum Ringkas',
            'category' => 'Publikasi Umum',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/belum.pdf',
            'status' => 'Pending',
        ]);

        $this->actingAs($user)
            ->get("/publications/{$publication->id}/download")
            ->assertNotFound();
    }

    public function test_chatbot_ignores_pending_publications(): void
    {
        config(['services.gemini.api_key' => null]);

        $user = User::create([
            'name' => 'User Chatbot',
            'username' => 'user-chatbot',
            'email' => 'user-chatbot@bps.go.id',
            'password' => \Illuminate\Support\Facades\Hash::make('user1234'),
            'role' => 'user',
        ]);

        Publication::create([
            'title' => 'Publikasi Sudah Ringkas',
            'category' => 'Publikasi Umum',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/sudah.pdf',
            'summary' => 'Ringkasan siap dibaca.',
            'status' => 'Selesai',
        ]);

        $pending = Publication::create([
            'title' => 'Publikasi Belum Ringkas',
            'category' => 'Publikasi Umum',
            'year' => 2026,
            'release_date' => '2026-01-01',
            'pdf_path' => 'publications/belum.pdf',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/chatbot/send', ['message' => 'rangkum'])
            ->assertOk();

        $this->assertStringContainsString('Publikasi Sudah Ringkas', $response->json('reply'));
        $this->assertStringNotContainsString('Publikasi Belum Ringkas', $response->json('reply'));

        $this->actingAs($user)
            ->getJson('/chatbot/suggestions?publication_id=' . $pending->id)
            ->assertOk()
            ->assertJsonPath('mode', 'universal');
    }
}
