<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;


// 1. RUTA TEMPORAL PARA CREAR TU USUARIO MAESTRO (Borra esto después de usarlo)
Route::get('/setup', function() {
    \App\Models\User::updateOrCreate(
        ['email' => 'admin@sistemadg.com'],
        [
            'name' => 'Iván Carpio',
            'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
            'rol' => 'admin'
        ]
    );
    return 'Administrador creado. Ve a la ruta /login';
});

// 2. RUTAS DE INICIO DE SESIÓN
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// 3. RUTAS PROTEGIDAS (Nadie entra aquí sin login)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/productos', [ProductoController::class, 'index']);

// Rutas de Inventario
Route::get('/inventario', [InventarioController::class, 'index']);
Route::post('/inventario', [InventarioController::class, 'store']);

Route::get('/pos', [PosController::class, 'index']);
Route::post('/pos/cobrar', [PosController::class, 'cobrar']);
Route::get('/ventas/{id}/pdf', [VentaController::class, 'pdf']);
Route::get('/ventas', [VentaController::class, 'index']);


Route::post('/productos', [App\Http\Controllers\ProductoController::class, 'store']);
Route::put('/productos/{id}', [App\Http\Controllers\ProductoController::class, 'update']);
Route::delete('/productos/{id}', [App\Http\Controllers\ProductoController::class, 'destroy']);

Route::get('/inicio', [HomeController::class, 'index']);
// Opcional: Si quieres que el sistema abra el dashboard apenas entras, usa esta también:
Route::get('/', [HomeController::class, 'index']);

Route::get('/configuracion', [ConfiguracionController::class, 'index']);
Route::post('/configuracion/marcas', [ConfiguracionController::class, 'storeMarca']);
Route::put('/configuracion/marcas/{id}', [ConfiguracionController::class, 'updateMarca']);
Route::post('/configuracion/unidades', [ConfiguracionController::class, 'storeUnidad']);
Route::put('/configuracion/unidades/{id}', [ConfiguracionController::class, 'updateUnidad']);
Route::post('/configuracion/qr', [ConfiguracionController::class, 'updateQr']);

Route::post('/categorias', [App\Http\Controllers\ProductoController::class, 'storeCategoria']);

Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

Route::post('/ventas/{id}/anular', [App\Http\Controllers\VentaController::class, 'anular']);