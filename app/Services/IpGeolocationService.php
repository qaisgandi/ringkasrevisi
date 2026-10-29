<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
     * Prefer Vercel's request geolocation headers. This avoids sending a visitor IP
     * to a third-party geolocation API and gives the dashboard a useful region.
     *
     * @return array{region: string, city: string|null}
     */
    public function locateRequest(Request $request): array
    {
        if (config('services.geoip.use_vercel_headers', false)) {
            $country = strtoupper(trim((string) $request->header('x-vercel-ip-country')));
            $regionCode = strtoupper(trim((string) $request->header('x-vercel-ip-country-region')));
            $city = rawurldecode(trim((string) $request->header('x-vercel-ip-city')));
            $city = $city !== '' ? Str::limit($city, 100, '') : null;

            if (preg_match('/\A[A-Z]{2}\z/', $country)) {
                $region = $this->regionName($country, $regionCode);
                if ($region !== null) {
                    return ['region' => $region, 'city' => $city];
                }

                return ['region' => $country, 'city' => $city];
            }
        }

        return $this->locate($request->ip());
    }

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

    private function regionName(string $country, string $regionCode): ?string
    {
        if ($country !== 'ID' || !preg_match('/\A[A-Z0-9]{1,3}\z/', $regionCode)) {
            return $regionCode !== '' && preg_match('/\A[A-Z0-9]{1,3}\z/', $regionCode)
                ? $country . '-' . $regionCode
                : null;
        }

        $indonesianRegions = [
            'AC' => 'Aceh', 'SU' => 'Sumatera Utara', 'SB' => 'Sumatera Barat',
            'RI' => 'Riau', 'KR' => 'Kepulauan Riau', 'JA' => 'Jambi',
            'SS' => 'Sumatera Selatan', 'BE' => 'Bengkulu', 'LA' => 'Lampung',
            'BB' => 'Kepulauan Bangka Belitung', 'JK' => 'DKI Jakarta',
            'JB' => 'Jawa Barat', 'JT' => 'Jawa Tengah', 'JI' => 'Jawa Timur',
            'YO' => 'DI Yogyakarta', 'BT' => 'Banten', 'BA' => 'Bali',
            'NB' => 'Nusa Tenggara Barat', 'NT' => 'Nusa Tenggara Timur',
            'KB' => 'Kalimantan Barat', 'KT' => 'Kalimantan Tengah',
            'KI' => 'Kalimantan Timur', 'KS' => 'Kalimantan Selatan',
            'KU' => 'Kalimantan Utara', 'SA' => 'Sulawesi Utara',
            'ST' => 'Sulawesi Tengah', 'SG' => 'Sulawesi Tenggara',
            'SR' => 'Sulawesi Barat', 'SN' => 'Sulawesi Selatan',
            'MA' => 'Maluku', 'MU' => 'Maluku Utara', 'PA' => 'Papua',
            'PB' => 'Papua Barat', 'PD' => 'Papua Barat Daya',
            'PE' => 'Papua Pegunungan', 'PS' => 'Papua Selatan',
            'PT' => 'Papua Tengah',
        ];

        return $indonesianRegions[$regionCode] ?? ($regionCode !== '' ? 'ID-' . $regionCode : null);
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
