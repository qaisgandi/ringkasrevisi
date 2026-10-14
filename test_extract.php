<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = App\Models\Publication::find(13);
if (!$p) {
    echo "Publication 13 not found\n";
    exit;
}
$path = storage_path('app/public/' . $p->pdf_path);
echo "Path: $path\n";
echo "Filesize: " . filesize($path) . "\n";

$extractor = new App\Services\PdfExtractionService();
$reflection = new ReflectionClass($extractor);
$method = $reflection->getMethod('extractPagesText');
$method->setAccessible(true);

$start = microtime(true);
$pages = $method->invoke($extractor, $path);
$end = microtime(true);

$fullText = implode(' ', $pages);
echo "Time: " . ($end - $start) . "s\n";
echo "Text length: " . strlen($fullText) . "\n";
echo "Page count: " . count($pages) . "\n";
echo "Preview: " . substr($fullText, 0, 500) . "\n";
