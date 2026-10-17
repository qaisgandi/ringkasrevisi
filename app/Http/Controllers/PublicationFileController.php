<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Support\Facades\Storage;

class PublicationFileController extends Controller
{
    public function show(Publication $publication)
    {
        // Only files created in the publications directory may be served.
        abort_unless(preg_match('/\Apublications\/[a-zA-Z0-9_.-]+\.pdf\z/i', (string) $publication->pdf_path), 404);
        $disk = Storage::disk('publications');
        abort_unless($disk->exists($publication->pdf_path), 404);

        return response()->file($disk->path($publication->pdf_path), [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'private, no-store',
            'Content-Security-Policy' => "sandbox; default-src 'none'; frame-ancestors 'self'",
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
