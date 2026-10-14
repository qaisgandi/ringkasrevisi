<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationAiResult extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'publications_ai_results';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'publication_id',
        'summary',
        'publication_information',
        'topics',
        'keywords',
        'key_points',
        'indicators',
        'trends',
        'discussion_locations',
        'conclusion',
    ];

    /**
     * Atribut yang harus dicast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'publication_information' => 'array',
        'topics' => 'array',
        'keywords' => 'array',
        'key_points' => 'array',
        'indicators' => 'array',
        'trends' => 'array',
        'discussion_locations' => 'array',
    ];

    /**
     * Relasi ke model Publication.
     */
    public function publication()
    {
        return $this->belongsTo(Publication::class, 'publication_id');
    }
}
