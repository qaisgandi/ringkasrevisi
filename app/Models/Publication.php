<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
