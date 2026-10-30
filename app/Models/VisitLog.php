<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'publication_id',
        'event_type',
        'ip_address',
        'region',
        'city',
        'user_agent',
        'url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public const EVENT_LABELS = [
        'view' => 'Kunjungan',
        'download' => 'Unduhan',
        'login' => 'Login',
        'register' => 'Registrasi',
    ];

    /**
     * Label event berbahasa Indonesia untuk dashboard.
     */
    public function eventLabel(): string
    {
        return self::EVENT_LABELS[$this->event_type] ?? ucfirst((string) $this->event_type);
    }

    /**
     * Warna badge event pada tabel aktivitas terbaru.
     */
    public function eventBadgeClass(): string
    {
        return match ($this->event_type) {
            'download' => 'bg-blue-50 text-blue-700',
            'login' => 'bg-violet-50 text-violet-700',
            'register' => 'bg-amber-50 text-amber-700',
            default => 'bg-emerald-50 text-emerald-700',
        };
    }

    /**
     * Nama pengguna yang melakukan aktivitas (atau "Anonim").
     */
    public function actorLabel(): string
    {
        return $this->user?->name ?: ($this->user?->username ?: 'Anonim');
    }

    /**
     * Wilayah pengakses: kota/kabupaten, dengan provinsi sebagai pelengkap.
     */
    public function locationLabel(): string
    {
        $city = trim((string) $this->city);
        $region = trim((string) $this->region);

        if ($city !== '' && $region !== '' && $city !== $region && $region !== 'Tidak diketahui') {
            return $city . ', ' . $region;
        }

        if ($city !== '') {
            return $city;
        }

        return $region !== '' ? $region : '-';
    }

    /**
     * Samarkan IP untuk ditampilkan di dashboard (privasi).
     */
    public function maskedIp(): string
    {
        if (empty($this->ip_address)) {
            return '-';
        }

        // IPv4: sembunyikan oktet terakhir. IPv6: potong separuh.
        if (str_contains($this->ip_address, '.')) {
            $parts = explode('.', $this->ip_address);
            if (count($parts) === 4) {
                return $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.xxx';
            }
        }

        return substr($this->ip_address, 0, 8) . ':xxxx';
    }
}
