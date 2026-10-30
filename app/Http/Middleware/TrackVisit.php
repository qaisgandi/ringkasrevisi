<?php

namespace App\Http\Middleware;

use App\Services\VisitTracker;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    /** Kunjungan ulang ke halaman yang sama dihitung lagi setelah jeda ini. */
    private const WINDOW_SECONDS = 15 * 60;

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
     * Catat kunjungan halaman publik.
     *
     * Deduplikasi memakai jendela 15 menit per halaman per session: refresh
     * beruntun tidak menggelembungkan angka, tetapi kunjungan ulang setelah
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

        $sessionKey = 'visited:' . $routeName . ':' . ($publicationId ?? 'index');
        $now = now()->getTimestamp();

        // Simpan semua penanda dalam satu kunci dan buang yang sudah kedaluwarsa,
        // agar cookie session (SESSION_DRIVER=cookie di Vercel) tidak membengkak.
        $visited = $request->session()->get('visited_pages', []);
        if (!is_array($visited)) {
            $visited = [];
        }

        $visited = array_filter(
            $visited,
            fn ($timestamp) => is_numeric($timestamp) && ($now - (int) $timestamp) < self::WINDOW_SECONDS
        );

        if (isset($visited[$sessionKey])) {
            $request->session()->put('visited_pages', $visited);

            return $response;
        }

        $visited[$sessionKey] = $now;
        $request->session()->put('visited_pages', $visited);

        $this->tracker->record($request, VisitTracker::VIEW, $publicationId);

        return $response;
    }
}
