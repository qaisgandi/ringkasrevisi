<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reversible migration for installations that already have public PDF uploads.
Artisan::command('security:privatize-pdfs {--apply : Copy, verify, then remove the publicly served original}', function () {
    $source = \Illuminate\Support\Facades\Storage::disk('public');
    $target = \Illuminate\Support\Facades\Storage::disk('publications');
    $count = 0;
    $skipped = false;
    foreach ($source->files('publications') as $path) {
        if (!preg_match('/\Apublications\/[a-zA-Z0-9_.-]+\.pdf\z/i', $path)) {
            $skipped = true;
            $this->warn('Skipped a file with an unexpected name; review public storage manually.');
            continue;
        }
        $count++;
        if (!$this->option('apply')) continue;
        $sourceHash = hash_file('sha256', $source->path($path));
        if (!$target->exists($path)) {
            $stream = $source->readStream($path);
            try { $target->put($path, $stream); } finally { if (is_resource($stream)) fclose($stream); }
        }
        if (!hash_equals($sourceHash, hash_file('sha256', $target->path($path)))) {
            $this->error('Destination mismatch; original retained.');
            return 1;
        }
        if (!$source->delete($path)) {
            $this->error('Could not remove public copy; verified private copy retained.');
            return 1;
        }
    }
    $this->info($count.' PDF file(s) '.($this->option('apply') ? 'migrated/verified.' : 'found. Use --apply to migrate.'));
    return $skipped ? 1 : 0;
})->purpose('Move public PDF uploads into authenticated private storage without changing database paths');
