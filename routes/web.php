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
use App\Http\Controllers\ArqueoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ClienteController;


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
Route::get('/productos/generar-codigo', [ProductoController::class, 'generarCodigoAjax']);

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
Route::delete('/configuracion/marcas/{id}', [ConfiguracionController::class, 'destroyMarca']);

Route::post('/configuracion/unidades', [ConfiguracionController::class, 'storeUnidad']);
Route::put('/configuracion/unidades/{id}', [ConfiguracionController::class, 'updateUnidad']);
Route::delete('/configuracion/unidades/{id}', [ConfiguracionController::class, 'destroyUnidad']);

Route::post('/configuracion/categorias', [ConfiguracionController::class, 'storeCategoria']);
Route::post('/configuracion/categorias/asignar-masivo', [ConfiguracionController::class, 'asignarCategoriaMasiva']);
Route::put('/configuracion/categorias/{id}', [ConfiguracionController::class, 'updateCategoria']);
Route::delete('/configuracion/categorias/{id}', [ConfiguracionController::class, 'destroyCategoria']);

Route::post('/configuracion/qr', [ConfiguracionController::class, 'updateQr']);
Route::post('/configuracion/qr-toggle', [ConfiguracionController::class, 'toggleQrModal']);
Route::post('/configuracion/logo', [ConfiguracionController::class, 'updateLogo']);
Route::post('/configuracion/ajustes', [ConfiguracionController::class, 'updateAjustes']);

Route::post('/categorias', [App\Http\Controllers\ProductoController::class, 'storeCategoria']);

Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

Route::post('/ventas/{id}/anular', [App\Http\Controllers\VentaController::class, 'anular']);
Route::post('/ventas/{id}/abono',  [App\Http\Controllers\VentaController::class, 'registrarAbono']);

// ============================================================
// RUTAS: ARQUEO DE CAJA
// ============================================================
Route::get('/arqueos',              [ArqueoController::class, 'index']);
Route::get('/arqueos/crear',        [ArqueoController::class, 'create']);
Route::post('/arqueos',             [ArqueoController::class, 'store']);
Route::get('/arqueos/{id}',         [ArqueoController::class, 'show']);
Route::get('/arqueos/{id}/excel',   [ArqueoController::class, 'exportarExcel']);

// ============================================================
// RUTAS: MÓDULO DE REPORTES (CATÁLOGO / VENTAS / CIERRE DE MES)
// ============================================================
Route::get('/reportes',                     [ReporteController::class, 'hub']);
Route::get('/reportes/ventas',              [ReporteController::class, 'ventas']);
Route::get('/reportes/ventas/excel',         [ReporteController::class, 'exportarVentasExcel']);
Route::get('/reportes/excel',               [ReporteController::class, 'exportarVentasExcel']); // compatibilidad

Route::get('/reportes/cierre-mes',          [ReporteController::class, 'cierreMes']);
Route::post('/reportes/cierre-mes/cerrar',  [ReporteController::class, 'storeCierreMes']);
Route::post('/reportes/cierre-mes/reabrir', [ReporteController::class, 'reabrirCierreMes']);
Route::post('/reportes/cierre-mes/toggle-check', [ReporteController::class, 'toggleCheckCierreMes']);
Route::post('/reportes/cierre-mes/check-all',   [ReporteController::class, 'checkAllCierreMes']);
Route::get('/reportes/cierre-mes/excel',    [ReporteController::class, 'exportarCierreMesExcel']);
// ============================================================
// RUTAS: MÓDULO DE CLIENTES (REGULARES Y FRECUENTES)
// ============================================================
Route::get('/clientes',             [ClienteController::class, 'index']);
Route::post('/clientes',            [ClienteController::class, 'store']);
Route::get('/clientes/buscar',      [ClienteController::class, 'buscarAjax']);
Route::get('/clientes/{id}',        [ClienteController::class, 'show']);
Route::put('/clientes/{id}',        [ClienteController::class, 'update']);
Route::post('/clientes/{id}/abono',  [ClienteController::class, 'registrarAbono']);
Route::delete('/clientes/{id}',     [ClienteController::class, 'destroy']);
