<?php

namespace App\Http\Middleware;

use App\Models\VisitLog;
use App\Services\IpGeolocationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    /**
     * Catat kunjungan halaman user (1x per session per halaman).
     * Hanya untuk GET pada route home & user.publications.show agar
     * request chatbot/download tidak ikut tercatat sebagai view.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->isMethod('get')) {
            return $response;
        }

        $routeName = $request->route()?->getName();
        if (!in_array($routeName, ['home', 'user.publications.show'], true)) {
            return $response;
        }

        $publicationId = null;
        if ($routeName === 'user.publications.show') {
            $publication = $request->route('publication');
            $publicationId = is_object($publication) ? $publication->id : (int) $publication;
        }

        // Deduplikasi: 1x per session per halaman.
        $sessionKey = 'visited:' . $routeName . ':' . ($publicationId ?? 'home');
        if ($request->session()->has($sessionKey)) {
            return $response;
        }
        $request->session()->put($sessionKey, true);

        try {
            $ip = $request->ip();
            $geo = app(IpGeolocationService::class)->locate($ip);

            VisitLog::create([
                'user_id' => Auth::id(),
                'publication_id' => $publicationId,
                'event_type' => 'view',
                'ip_address' => $ip,
                'region' => $geo['region'] ?? 'Tidak diketahui',
                'city' => $geo['city'] ?? null,
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'url' => substr('/' . ltrim($request->path(), '/'), 0, 500),
            ]);
        } catch (\Exception $e) {
            // Tracking tidak boleh menggagalkan request utama.
        }

        return $response;
    }
}
