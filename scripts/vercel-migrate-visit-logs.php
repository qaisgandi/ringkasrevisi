<?php

declare(strict_types=1);

// The Vercel PHP builder runs Composer's `vercel` script during deployment.
// Apply only the additive analytics migration on Production; Preview builds
// must never alter the Production database.
if (getenv('VERCEL_ENV') !== 'production') {
    fwrite(STDOUT, "Skipping visit-log migration outside Production.\n");
    exit(0);
}

$migration = 'database/migrations/2026_09_23_000001_create_visit_logs_table.php';
$command = escapeshellarg(PHP_BINARY)
    . ' ' . escapeshellarg(__DIR__ . '/../artisan')
    . ' migrate --force --path=' . escapeshellarg($migration);

fwrite(STDOUT, "Ensuring the visit-log table exists for Production analytics.\n");
passthru($command, $exitCode);

if ($exitCode !== 0) {
    fwrite(STDERR, "Visit-log migration failed; refusing to continue the deployment.\n");
    exit($exitCode);
}

fwrite(STDOUT, "Visit-log migration completed.\n");
