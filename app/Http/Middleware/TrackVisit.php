<?php

namespace App\Http\Middleware;

use App\Services\VisitTracker;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    /** Kunjungan ulang ke halaman yang sama dihitung lagi setelah jeda ini (5 menit). */
    private const WINDOW_SECONDS = 5 * 60;

    /** Halaman publik yang dianggap sebagai kunjungan pembaca. */
    private const TRACKED_ROUTES = [
        'landing',
        'home',
        'profile',
        'login',
        'register',
        'user.publications.show',
    ];

    public function __construct(private readonly VisitTracker $tracker)
    {
    }

    /**
     * Catat kunjungan halaman publik dengan jeda deduplikasi 5 menit per halaman/sesi.
     * Refresh beruntun tidak menggelembungkan angka, tetapi kunjungan ulang setelah
     * jeda tetap tercatat.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->isMethod('get') || !$response->isSuccessful()) {
            return $response;
        }

        $routeName = $request->route()?->getName();
        if (!in_array($routeName, self::TRACKED_ROUTES, true)) {
            return $response;
        }

        $publicationId = null;
        if ($routeName === 'user.publications.show') {
            $publication = $request->route('publication');
            $publicationId = is_object($publication) ? $publication->id : (int) $publication;
        }

        // Gunakan kunci baru agar penanda sesi lama (yang berlaku selamanya)
        // tidak terus menahan statistik setelah pembaruan aplikasi.
        $sessionKey = 'visit:last-recorded:' . $routeName . ':' . ($publicationId ?? 'home');
        $lastRecordedAt = (int) $request->session()->get($sessionKey, 0);
        if ($lastRecordedAt > 0 && (now()->timestamp - $lastRecordedAt) < self::WINDOW_SECONDS) {
            return $response;
        }

        $request->session()->put($sessionKey, now()->timestamp);

        $this->tracker->record($request, VisitTracker::VIEW, $publicationId);

        return $response;
    }
}
