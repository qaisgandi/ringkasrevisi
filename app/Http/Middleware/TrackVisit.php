<?php

namespace App\Http\Middleware;

use App\Models\VisitLog;
use App\Services\IpGeolocationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    /**
     * Catat kunjungan halaman user, dengan deduplikasi 5 menit per halaman/sesi.
     * Hanya untuk GET pada landing, home, dan user.publications.show agar
     * request chatbot/download tidak ikut tercatat sebagai view.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->isMethod('get') || !$response->isSuccessful()) {
            return $response;
        }

        $routeName = $request->route()?->getName();
        if (!in_array($routeName, ['landing', 'home', 'user.publications.show'], true)) {
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
        if ($lastRecordedAt > 0 && now()->timestamp - $lastRecordedAt < 300) {
            return $response;
        }

        try {
            $ip = $request->ip();
            $geo = app(IpGeolocationService::class)->locateRequest($request);

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
            $request->session()->put($sessionKey, now()->timestamp);
        } catch (\Throwable $e) {
            // Tracking tidak boleh menggagalkan request utama.
            Log::warning('Visit tracking could not save an event.', [
                'exception' => $e::class,
                'route' => $routeName,
            ]);
        }

        return $response;
    }
}
