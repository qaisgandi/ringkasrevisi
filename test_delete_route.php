<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = App\Models\Publication::latest()->first();
if (!$p) {
    echo "No publications found to delete\n";
    exit;
}

echo "Testing delete route for publication ID: {$p->id} (Title: {$p->title})\n";

// Login as admin
$admin = App\Models\User::where('role', 'admin')->first();
if (!$admin) {
    echo "Admin user not found\n";
    exit;
}
Auth::login($admin);

// Create request to delete route
$request = Illuminate\Http\Request::create(
    "/admin/publications/{$p->id}",
    'DELETE',
    ['_token' => csrf_token(), '_method' => 'DELETE']
);

$response = $app->handle($request);
echo "Response status: " . $response->getStatusCode() . "\n";
echo "Redirect URL: " . $response->headers->get('Location') . "\n";
