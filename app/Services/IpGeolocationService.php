<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Tebak wilayah pengunjung dari alamat IP.
 * - IP privat/lokal (127.0.0.1, 10.x, 192.168.x, ::1) → region "Lokal".
 * - Hasil lookup di-cache 30 hari per IP agar hemat dan cepat.
 * - Lookup memakai ip-api.com (gratis, tanpa key) dengan timeout singkat;
 *   jika gagal/offline → region "Tidak diketahui".
 */
class IpGeolocationService
{
    /**
     * @return array{region: string, city: string|null}
     */
    public function locate(?string $ip): array
    {
        if (empty($ip)) {
            return ['region' => 'Tidak diketahui', 'city' => null];
        }

        if ($this->isPrivateIp($ip)) {
            return ['region' => 'Lokal', 'city' => 'Lokal'];
        }

        // The legacy free endpoint sends visitor IPs over HTTP; opt in explicitly.
        if (!config('services.geoip.enabled', false)) {
            return ['region' => 'Tidak diketahui', 'city' => null];
        }

        return Cache::remember('geoip:' . $ip, now()->addDays(30), function () use ($ip) {
            try {
                $response = Http::timeout(4)->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,regionName,city',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (($data['status'] ?? '') === 'success') {
                        return [
                            'region' => $data['regionName'] ?? 'Tidak diketahui',
                            'city' => $data['city'] ?? null,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Abaikan: fallback di bawah.
            }

            return ['region' => 'Tidak diketahui', 'city' => null];
        });
    }

    private function isPrivateIp(string $ip): bool
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }

        // filter_var dengan flag menolak IP privat/reserved → false = privat.
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
