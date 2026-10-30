<?php

namespace App\Services;

use App\Models\VisitLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Tebak wilayah pengunjung dari alamat IP.
 * - IP privat/lokal (127.0.0.1, 10.x, 192.168.x, ::1) → region "Lokal".
 * - Sebelum memanggil API, pakai wilayah terakhir yang sudah tersimpan di
 *   visit_logs untuk IP yang sama (hemat kuota dan latensi, terutama karena
 *   cache di Vercel memakai driver array yang tidak bertahan antar request).
 * - Hasil lookup di-cache 30 hari per IP bila cache store persisten.
 * - Jika gagal/offline/nonaktif → region "Tidak diketahui".
 */
class IpGeolocationService
{
    /** Nama provinsi dari ip-api.com (Inggris) → nama resmi Indonesia. */
    private const REGION_NAMES = [
        'aceh' => 'Aceh',
        'north sumatra' => 'Sumatera Utara',
        'west sumatra' => 'Sumatera Barat',
        'riau' => 'Riau',
        'riau islands' => 'Kepulauan Riau',
        'jambi' => 'Jambi',
        'south sumatra' => 'Sumatera Selatan',
        'bangka belitung' => 'Kepulauan Bangka Belitung',
        'bangka belitung islands' => 'Kepulauan Bangka Belitung',
        'bengkulu' => 'Bengkulu',
        'lampung' => 'Lampung',
        'banten' => 'Banten',
        'jakarta' => 'DKI Jakarta',
        'west java' => 'Jawa Barat',
        'central java' => 'Jawa Tengah',
        'yogyakarta' => 'DI Yogyakarta',
        'east java' => 'Jawa Timur',
        'bali' => 'Bali',
        'west nusa tenggara' => 'Nusa Tenggara Barat',
        'east nusa tenggara' => 'Nusa Tenggara Timur',
        'west kalimantan' => 'Kalimantan Barat',
        'central kalimantan' => 'Kalimantan Tengah',
        'south kalimantan' => 'Kalimantan Selatan',
        'east kalimantan' => 'Kalimantan Timur',
        'north kalimantan' => 'Kalimantan Utara',
        'north sulawesi' => 'Sulawesi Utara',
        'gorontalo' => 'Gorontalo',
        'central sulawesi' => 'Sulawesi Tengah',
        'south sulawesi' => 'Sulawesi Selatan',
        'southeast sulawesi' => 'Sulawesi Tenggara',
        'west sulawesi' => 'Sulawesi Barat',
        'maluku' => 'Maluku',
        'north maluku' => 'Maluku Utara',
        'west papua' => 'Papua Barat',
        'southwest papua' => 'Papua Barat Daya',
        'central papua' => 'Papua Tengah',
        'highland papua' => 'Papua Pegunungan',
        'south papua' => 'Papua Selatan',
        'papua' => 'Papua',
    ];

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

        if (!config('services.geoip.enabled', false)) {
            return ['region' => 'Tidak diketahui', 'city' => null];
        }

        try {
            return Cache::remember('geoip:' . $ip, now()->addDays(30), function () use ($ip) {
                // 1. Wilayah yang pernah tercatat untuk IP ini.
                $known = $this->lastKnownLocation($ip);
                if ($known !== null) {
                    return $known;
                }

                // 2. Lookup ke penyedia geolokasi (timeout singkat).
                $endpoint = (string) config('services.geoip.endpoint', 'https://ipapi.co/{ip}/json/');
                $endpoint = str_contains($endpoint, '{ip}')
                    ? str_replace('{ip}', $ip, $endpoint)
                    : rtrim($endpoint, '/') . '/' . $ip;

                $response = Http::timeout((int) config('services.geoip.timeout', 3))
                    ->withHeaders(['User-Agent' => config('app.name', 'RINGKAS') . ' visitor-stats'])
                    ->get($endpoint);

                if ($response->successful()) {
                    $data = $response->json();

                    // Mendukung dua bentuk respons: ip-api (status) dan ipapi.co.
                    $isSuccess = ($data['status'] ?? null) === 'success'
                        || ($data['success'] ?? null) === true
                        || (!isset($data['error']) && isset($data['regionName']));

                    if ($isSuccess) {
                        return [
                            'region' => $this->normalizeRegion($data['regionName'] ?? $data['region'] ?? null),
                            'city' => $this->normalizeCity($data['city'] ?? null),
                        ];
                    }
                }

                return ['region' => 'Tidak diketahui', 'city' => null];
            });
        } catch (\Throwable $e) {
            return $this->lastKnownLocation($ip) ?? ['region' => 'Tidak diketahui', 'city' => null];
        }
    }

    /**
     * @return array{region: string, city: string|null}|null
     */
    private function lastKnownLocation(string $ip): ?array
    {
        try {
            $row = VisitLog::query()
                ->where('ip_address', $ip)
                ->whereNotNull('region')
                ->whereNotIn('region', ['', 'Tidak diketahui'])
                ->latest('id')
                ->first(['region', 'city']);
        } catch (\Throwable $e) {
            return null;
        }

        if (!$row) {
            return null;
        }

        return ['region' => $row->region, 'city' => $row->city];
    }

    private function normalizeRegion(?string $region): string
    {
        $region = trim((string) $region);

        if ($region === '') {
            return 'Tidak diketahui';
        }

        return self::REGION_NAMES[strtolower($region)] ?? $region;
    }

    private function normalizeCity(?string $city): ?string
    {
        $city = trim((string) $city);

        if ($city === '') {
            return null;
        }

        // Sebagian penyedia mengirim nama kota dalam huruf kapital semua.
        if (mb_strtoupper($city) === $city) {
            $city = mb_convert_case(mb_strtolower($city), MB_CASE_TITLE);
        }

        return $city;
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
