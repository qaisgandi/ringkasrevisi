<?php

namespace Tests\Feature;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
    }

    public function test_redesigned_authentication_pages_render_for_guests(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk');
        $this->get('/register')->assertOk()->assertSee('Buat akun');
        $this->get('/admin/login')->assertOk()->assertSee('Masuk');
    }
}
