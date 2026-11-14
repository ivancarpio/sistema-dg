<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

echo "Movimientos antes: " . DB::table('movimientos')->count() . PHP_EOL;
echo "Stock total antes: " . DB::table('productos')->sum('stock') . PHP_EOL;

DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
DB::statement('TRUNCATE TABLE movimientos;');
DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

Artisan::call('cache:clear');

echo "Movimientos después: " . DB::table('movimientos')->count() . PHP_EOL;
echo "Stock total después: " . DB::table('productos')->sum('stock') . PHP_EOL;
echo "Total productos: " . DB::table('productos')->count() . PHP_EOL;
