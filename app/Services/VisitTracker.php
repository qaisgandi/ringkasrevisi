<?php

namespace App\Services;

use App\Models\VisitLog;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Satu pintu untuk mencatat aktivitas pembaca (kunjungan, unduhan, login, registrasi).
 *
 * Pemanggilan dari middleware maupun controller sengaja tidak pernah melempar
 * exception: kegagalan pencatatan tidak boleh menggagalkan request utama, tetapi
 * tetap dicatat ke log agar masalah (mis. migrasi visit_logs belum dijalankan)
 * bisa didiagnosis.
 */
class VisitTracker
{
    public const VIEW = 'view';

    public const DOWNLOAD = 'download';

    public const LOGIN = 'login';

    public const REGISTER = 'register';

    public const EVENT_TYPES = [self::VIEW, self::DOWNLOAD, self::LOGIN, self::REGISTER];

    public function __construct(private readonly IpGeolocationService $geolocation)
    {
    }

    public function record(
        Request $request,
        string $eventType = self::VIEW,
        ?int $publicationId = null,
        ?int $userId = null,
        ?string $url = null,
    ): ?VisitLog {
        if (!in_array($eventType, self::EVENT_TYPES, true)) {
            $eventType = self::VIEW;
        }

        $ipAddress = $request->ip();

        try {
            $location = $this->geolocation->locate($ipAddress);

            return VisitLog::create([
                'user_id' => $userId ?? Auth::id(),
                'publication_id' => $publicationId,
                'event_type' => $eventType,
                'ip_address' => $ipAddress,
                'region' => $location['region'] ?? 'Tidak diketahui',
                'city' => $location['city'] ?? null,
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'url' => substr($url ?? ('/' . ltrim($request->path(), '/')), 0, 500),
            ]);
        } catch (\Throwable $e) {
            Log::warning('visit_log.not_recorded', [
                'event' => $eventType,
                'exception' => $e::class,
                // Untuk QueryException cukup simpan SQLSTATE agar isi query (berisi IP
                // dan user agent) tidak ikut masuk ke log.
                'reason' => $e instanceof QueryException
                    ? ($e->errorInfo[0] ?? (string) $e->getCode())
                    : substr($e->getMessage(), 0, 160),
            ]);

            return null;
        }
    }
}
