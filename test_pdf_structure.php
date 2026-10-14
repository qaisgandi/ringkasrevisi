<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admin = App\Models\User::where('role', 'admin')->first();
Auth::login($admin);

$request = Illuminate\Http\Request::create('/admin/publications', 'GET');
$response = $app->handle($request);

$html = $response->getContent();

// Find all data-delete-url occurrences in the HTML
preg_match_all('/data-delete-url="([^"]+)"/', $html, $matches);
echo "Generated data-delete-urls in the view:\n";
foreach ($matches[1] as $url) {
    echo "  $url\n";
}

// Find form id="delete-form"
if (preg_match('/<form id="delete-form"[^>]*>(.*?)<\/form>/is', $html, $formMatch)) {
    echo "Delete form HTML:\n" . trim($formMatch[0]) . "\n";
} else {
    echo "Delete form not found in the HTML!\n";
}
