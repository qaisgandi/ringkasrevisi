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
