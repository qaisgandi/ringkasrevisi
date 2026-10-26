<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\PublicationFileService;

class Publication extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'publications';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'category',
        'year',
        'pdf_path',
        'region',
        'page_count',
        'file_size',
        'extracted_text',
        'summary',
        'topics',
        'keywords',
        'key_points',
        'indicators',
        'trends',
        'page_locations',
        'conclusion',
        'status',
        'uploaded_by',
        'release_date',
    ];

    /**
     * Atribut yang harus dicast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'topics' => 'array',
        'keywords' => 'array',
        'key_points' => 'array',
        'indicators' => 'array',
        'trends' => 'array',
        'page_locations' => 'array',
        'release_date' => 'date',
    ];

    /**
     * Relasi ke model PublicationAiResult.
     */
    public function aiResult()
    {
        return $this->hasOne(PublicationAiResult::class, 'publication_id');
    }

    public function getPdfUrlAttribute(): string
    {
        return app(PublicationFileService::class)->url($this->pdf_path);
    }

    public function getFileSizeBytesAttribute(): int
    {
        if (!preg_match('/^\s*([0-9]+(?:\.[0-9]+)?)\s*(B|KB|MB|GB)?\s*$/i', (string) $this->file_size, $matches)) {
            return 0;
        }

        $unit = strtoupper($matches[2] ?? 'B');
        $multiplier = ['B' => 1, 'KB' => 1024, 'MB' => 1048576, 'GB' => 1073741824][$unit] ?? 1;

        return (int) round((float) $matches[1] * $multiplier);
    }
}
