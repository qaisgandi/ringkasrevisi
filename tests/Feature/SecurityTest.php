<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Publication;
use App\Models\User;
use App\Services\AiSummarizerService;
use App\Services\GeminiChatService;
use App\Services\IpGeolocationService;
use App\Services\PdfExtractionService;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        Http::preventStrayRequests();
    }

    private function user(string $role = 'user'): User
    {
        return User::factory()->create(['username' => 'security-'.$role, 'role' => $role, 'password' => Hash::make('Existing-pass-123')]);
    }

    private function publication(): Publication
    {
        return Publication::create(['title' => 'Security PDF', 'category' => 'Publikasi Umum', 'year' => 2026,
            'release_date' => '2026-01-01', 'pdf_path' => 'publications/test.pdf', 'status' => 'Pending']);
    }

    public function test_notifications_cannot_be_changed_by_guests_or_regular_users(): void
    {
        $notice = Notification::create(['title' => 'Uji', 'message' => 'Pesan', 'type' => 'info', 'is_read' => false]);
        $this->postJson('/notifications/read-all')->assertUnauthorized();
        $this->actingAs($this->user())->postJson('/notifications/read-all')->assertForbidden();
        $this->assertFalse((bool) $notice->fresh()->is_read);
        session()->forget('password_hash_web');
        $this->actingAs($this->user('admin'))->post('/notifications/read-all')->assertRedirect();
        $this->assertTrue((bool) $notice->fresh()->is_read);
    }

    public function test_regular_user_cannot_mutate_admin_resources(): void
    {
        $publication = $this->publication();
        $this->actingAs($this->user());
        foreach (['post' => ['/admin/publications', '/admin/publications/'.$publication->id.'/extract', '/admin/users'],
            'put' => ['/admin/publications/'.$publication->id], 'delete' => ['/admin/publications/'.$publication->id, '/admin/activities/clear']] as $method => $urls) {
            foreach ($urls as $url) {
                $this->json(strtoupper($method), $url, [])->assertForbidden();
            }
        }
        $this->assertDatabaseHas('publications', ['id' => $publication->id]);
    }

    public function test_login_is_limited_across_both_portals_and_spoofed_forwarded_ips(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->withHeader('X-Forwarded-For', '203.0.113.'.($i + 1))->post('/login', ['username' => 'unknown', 'password' => 'invalid'])->assertRedirect();
        }
        $this->postJson('/admin/login', ['username' => 'unknown', 'password' => 'invalid'])->assertStatus(429)->assertHeader('Retry-After');
    }

    public function test_registration_enforces_password_length_and_ignores_admin_role(): void
    {
        $data = ['username' => 'new-user', 'email' => 'new@example.test', 'password' => 'shortpass', 'password_confirmation' => 'shortpass', 'role' => 'admin'];
        $this->post('/register', $data)->assertSessionHasErrors('password');
        $data['password'] = $data['password_confirmation'] = 'Valid-passphrase-123';
        $this->post('/register', $data)->assertRedirect('/home');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['username' => 'new-user', 'role' => 'user']);
    }

    public function test_password_change_requires_current_password_and_rotates_session(): void
    {
        $user = $this->user();
        $data = ['name' => $user->name, 'username' => $user->username, 'email' => $user->email,
            'password' => 'New-passphrase-456', 'password_confirmation' => 'New-passphrase-456'];
        $this->actingAs($user)->get('/profile');
        $oldId = session()->getId();
        $this->post('/profile', $data)->assertSessionHasErrors('current_password');
        $this->post('/profile', $data + ['current_password' => 'wrong'])->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('Existing-pass-123', $user->fresh()->password));
        $this->post('/profile', $data + ['current_password' => 'Existing-pass-123'])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertTrue(Hash::check('New-passphrase-456', $user->fresh()->password));
        $this->assertNotEquals($oldId, session()->getId());
        $this->assertNotEmpty($user->fresh()->remember_token);
        $this->get('/profile')->assertOk();
    }

    public function test_stale_session_is_rejected_after_password_reset(): void
    {
        $user = $this->user();
        $this->actingAs($user)->get('/profile')->assertOk();
        $user->forceFill(['password' => Hash::make('Reset-passphrase-456')])->save();
        $this->getJson('/profile')->assertUnauthorized();
        $this->assertGuest();
    }

    public function test_password_is_not_flashed_when_admin_validation_fails(): void
    {
        $this->actingAs($this->user('admin'))->post('/admin/account-settings/password', [
            'old_password' => 'private-value', 'password' => 'short', 'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
        $this->assertArrayNotHasKey('old_password', session()->getOldInput());
        $this->assertArrayNotHasKey('password', session()->getOldInput());
    }

    public function test_pdf_access_requires_login_and_has_safe_headers(): void
    {
        Storage::fake('publications');
        $pub = $this->publication();
        Storage::disk('publications')->put($pub->pdf_path, "%PDF-1.4\n%%EOF");
        $this->getJson('/publications/'.$pub->id.'/file')->assertUnauthorized();
        $this->getJson('/publications/'.$pub->id.'/download')->assertUnauthorized();
        $this->actingAs($this->user())->get('/publications/'.$pub->id.'/file')->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Security-Policy', "sandbox; default-src 'none'; frame-ancestors 'self'");
        $this->get('/publications/'.$pub->id.'/download')->assertDownload('security-pdf.pdf');
        $pub->update(['pdf_path' => '../secret.pdf']);
        $this->get('/publications/'.$pub->id.'/file')->assertNotFound();
    }

    public function test_upload_rejects_php_extension_and_spoofed_pdf_contents(): void
    {
        Storage::fake('publications');
        $this->actingAs($this->user('admin'));
        $data = ['title' => 'Bad file', 'category' => 'Publikasi Umum', 'release_date' => '2026-01-01'];
        $this->post('/admin/publications', $data + ['pdf' => UploadedFile::fake()->createWithContent('evil.php', "%PDF-1.4\n<?php echo 'test'; ?>")->mimeType('application/pdf')])->assertSessionHasErrors('pdf');
        $fake = UploadedFile::fake()->createWithContent('fake.pdf', '<html>not a pdf</html>');
        $spoofed = new UploadedFile($fake->getPathname(), 'fake.pdf', 'application/pdf', null, true);
        $this->post('/admin/publications', $data + ['pdf' => $spoofed])->assertSessionHasErrors('pdf');
        $this->assertDatabaseCount('publications', 0);
        $this->assertEmpty(Storage::disk('publications')->allFiles());
    }

    public function test_valid_upload_uses_private_random_pdf_name_and_server_metadata(): void
    {
        Storage::fake('publications');
        Storage::fake('public');
        $this->mock(PdfExtractionService::class)->shouldReceive('extract')->once()->andReturn(['page_count' => 2]);
        $this->actingAs($this->user('admin'))->post('/admin/publications', [
            'title' => 'Safe PDF', 'category' => 'Publikasi Umum', 'release_date' => '2026-01-01',
            'file_size' => 'fake value', 'page_count' => 999999,
            'pdf' => UploadedFile::fake()->createWithContent('original.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF"),
        ])->assertSessionHasNoErrors()->assertRedirect(route('publications.index'));
        $pub = Publication::firstOrFail();
        $this->assertMatchesRegularExpression('/^publications\/[a-f0-9-]{36}\.pdf$/', $pub->pdf_path);
        Storage::disk('publications')->assertExists($pub->pdf_path);
        Storage::disk('public')->assertMissing($pub->pdf_path);
        $this->assertEquals(2, $pub->page_count);
        $this->assertNotEquals('fake value', $pub->file_size);
    }

    public function test_chat_history_and_processing_inputs_are_bounded(): void
    {
        $pub = $this->publication();
        $this->actingAs($this->user('admin'));
        $this->postJson('/chatbot/send', ['message' => 'Hello', 'history' => array_fill(0, 21, ['role' => 'user', 'text' => 'Hi'])])
            ->assertUnprocessable()->assertJsonValidationErrors('history');
        $this->postJson('/admin/publications/'.$pub->id.'/save-extracted-text', ['text' => 'x', 'pages' => ['bad' => ['nested']], 'page_count' => -1, 'file_size' => '1 B'])
            ->assertUnprocessable()->assertJsonValidationErrors(['pages.bad', 'page_count']);
        Http::assertNothingSent();
    }

    public function test_security_headers_and_untrusted_proxy_headers(): void
    {
        $this->get('/')->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        Route::get('/security-proxy-test', fn (Request $request) => response()->json(['ip' => $request->ip(), 'secure' => $request->isSecure()]));
        $this->withHeaders(['X-Forwarded-For' => '203.0.113.77', 'X-Forwarded-Proto' => 'https'])
            ->getJson('/security-proxy-test')->assertJson(['ip' => '127.0.0.1', 'secure' => false]);
    }

    public function test_csrf_rejects_mutation_without_token(): void
    {
        $this->app->bind(PreventRequestForgery::class, CsrfEnabledForTest::class);
        $this->post('/login', ['username' => 'x', 'password' => 'x'])->assertStatus(419);
    }

    public function test_gemini_key_is_sent_in_header_not_url(): void
    {
        config(['services.gemini.api_key' => 'test-key-not-real']);
        Http::fake(function ($request, $options) {
            $this->assertNotSame(false, $options['verify'] ?? true);

            return Http::response(['candidates' => [['content' => ['parts' => [['text' => 'Jawaban uji']]]]]]);
        });
        $result = app(GeminiChatService::class)->chat('Apa itu data?');
        $this->assertSame('Jawaban uji', $result['reply']);
        Http::assertSent(fn ($request) => $request->hasHeader('x-goog-api-key', 'test-key-not-real') && ! str_contains($request->url(), 'key='));
    }

    public function test_existing_client_extraction_payload_still_works(): void
    {
        $pub = $this->publication();
        $this->mock(AiSummarizerService::class)->shouldReceive('summarize')->once()
            ->andReturn(['summary' => 'Hasil uji', 'publication_information' => [], 'topics' => [], 'keywords' => [],
                'key_points' => [], 'indicators' => [], 'trends' => [], 'discussion_locations' => [], 'conclusion' => '']);
        $this->actingAs($this->user('admin'))->postJson('/admin/publications/'.$pub->id.'/save-extracted-text', [
            'text' => 'Teks uji dokumen.', 'pages' => [1 => 'Teks uji dokumen.'], 'page_count' => 1, 'file_size' => '1 KB',
        ])->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('publications', ['id' => $pub->id, 'status' => 'Selesai']);
    }

    public function test_chatbot_rate_limit_prevents_extra_ai_calls(): void
    {
        $this->mock(GeminiChatService::class)->shouldReceive('chat')->times(10)
            ->andReturn(['reply' => 'Uji', 'mode' => 'universal', 'publication' => null]);
        $this->actingAs($this->user());
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/chatbot/send', ['message' => 'Uji'])->assertOk();
        }
        $this->postJson('/chatbot/send', ['message' => 'Uji'])->assertStatus(429);
    }

    public function test_admin_self_password_reset_cannot_bypass_current_password_check(): void
    {
        $user = $this->user('admin');
        $this->actingAs($user)->put('/admin/users/'.$user->id, [
            'name' => $user->name, 'username' => $user->username, 'email' => $user->email, 'role' => 'admin',
            'password' => 'New-passphrase-456', 'password_confirmation' => 'New-passphrase-456',
        ])->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('Existing-pass-123', $user->fresh()->password));
    }

    public function test_oversized_request_and_file_are_rejected(): void
    {
        $this->actingAs($this->user('admin'));
        $this->post('/admin/publications', ['title' => 'Large', 'category' => 'Publikasi Umum',
            'release_date' => '2026-01-01', 'pdf' => UploadedFile::fake()->create('large.pdf', 25601, 'application/pdf')])
            ->assertSessionHasErrors('pdf');
        $this->postJson('/chatbot/send', ['message' => str_repeat('x', 270000)])->assertStatus(413);
    }

    public function test_geoip_does_not_send_visitors_to_plaintext_service_by_default(): void
    {
        config(['services.geoip.enabled' => false]);
        $result = app(IpGeolocationService::class)->locate('8.8.8.8');
        $this->assertSame('Tidak diketahui', $result['region']);
        Http::assertNothingSent();
    }

    public function test_public_pdf_migration_verifies_copy_before_removing_original(): void
    {
        Storage::fake('public');
        Storage::fake('publications');
        Storage::disk('public')->put('publications/old.pdf', '%PDF-1.4 safe');
        $this->artisan('security:privatize-pdfs')->assertSuccessful();
        Storage::disk('public')->assertExists('publications/old.pdf');
        $this->artisan('security:privatize-pdfs', ['--apply' => true])->assertSuccessful();
        Storage::disk('public')->assertMissing('publications/old.pdf');
        $this->assertSame('%PDF-1.4 safe', Storage::disk('publications')->get('publications/old.pdf'));
    }
}

class CsrfEnabledForTest extends PreventRequestForgery
{
    protected function runningUnitTests()
    {
        return false;
    }
}
