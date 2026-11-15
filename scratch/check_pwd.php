<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
$candidates = ['admin123', 'password', '123456', '12345678', 'admin', 'ivan123', 'secret', 'sistema123', 'admin@dg.com', '1234', 'ivan', 'IvanCarpio', 'ivan2026', 'DG2026', 'dg2026', 'distribuidora', 'distribuidoraguadalupe'];
foreach ($candidates as $c) {
    if (Illuminate\Support\Facades\Hash::check($c, $user->password)) {
        echo 'MATCH: ' . $c . PHP_EOL;
        exit;
    }
}
echo 'NO MATCH' . PHP_EOL;
