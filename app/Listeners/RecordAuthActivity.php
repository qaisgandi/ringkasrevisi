<?php

namespace App\Listeners;

use App\Services\VisitTracker;
use Illuminate\Auth\Events\Login;

/**
 * Mencatat login berhasil (user umum maupun admin) sebagai aktivitas
 * yang tampil di dashboard, lengkap dengan IP dan wilayah.
 *
 * Registrasi dicatat langsung oleh UserAuthController agar tidak dobel
 * dengan event Login yang dipicu oleh Auth::login() setelahnya.
 */
class RecordAuthActivity
{
    public function __construct(private readonly VisitTracker $tracker)
    {
    }

    public function handleLogin(Login $event): void
    {
        $request = request();

        if ($request->attributes->get('visit.login.already_recorded')) {
            return;
        }

        $this->tracker->record(
            $request,
            VisitTracker::LOGIN,
            null,
            $event->user?->getAuthIdentifier(),
            '/login'
        );
    }
}
