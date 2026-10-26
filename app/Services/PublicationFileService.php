<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PublicationFileService
{
    public function isTrustedBlobUrl(string $url): bool
    {
        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));

        return ($parts['scheme'] ?? null) === 'https'
            && str_ends_with($host, '.public.blob.vercel-storage.com')
            && str_starts_with((string) ($parts['path'] ?? ''), '/publications/');
    }

    public function url(?string $path): string
    {
        if (!$path) return '';
        if ($this->isTrustedBlobUrl($path)) return $path;

        return Storage::disk('public')->url($path);
    }

    /** Return a local path usable by PDF parsers and the path to clean up, if any. */
    public function materialize(string $path): array
    {
        if (!$this->isTrustedBlobUrl($path)) {
            return [Storage::disk('public')->path($path), null];
        }

        $response = Http::timeout(45)->get($path);
        if (!$response->successful()) {
            throw new RuntimeException('Dokumen PDF tidak dapat diambil dari penyimpanan.');
        }

        $temporaryPath = tempnam(sys_get_temp_dir(), 'ringkas-pdf-');
        if ($temporaryPath === false || file_put_contents($temporaryPath, $response->body()) === false) {
            throw new RuntimeException('Dokumen PDF tidak dapat disiapkan untuk diproses.');
        }

        return [$temporaryPath, $temporaryPath];
    }
}
