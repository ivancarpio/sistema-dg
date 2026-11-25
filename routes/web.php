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
use App\Http\Controllers\GastoController;
use App\Http\Controllers\CajaChicaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\TipoCambioController;

// ============================================================
// 1. RUTAS PÚBLICAS DE AUTENTICACIÓN (LOGIN)
// ============================================================
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ============================================================
// 2. RUTAS PROTEGIDAS DEL SISTEMA (REQUIEREN INICIO DE SESIÓN)
// ============================================================
Route::middleware(['auth'])->group(function () {
    
    // Cierre de Sesión y Keep-Alive
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/ping-session', function () {
        return response()->json(['status' => 'active', 'timestamp' => time()]);
    });

    // Raíz y Dashboard de Inicio
    Route::get('/', function () {
        return redirect()->route('inicio');
    });
    Route::get('/inicio', [HomeController::class, 'index'])->name('inicio');

    // Rutas para Cotización y Tipo de Cambio en Vivo (Ragnar Capital BOB / PEN)
    Route::get('/tipo-cambio/actual', [TipoCambioController::class, 'actual'])->name('tipo-cambio.actual');
    Route::post('/tipo-cambio/actualizar', [TipoCambioController::class, 'actualizarAjax'])->name('tipo-cambio.actualizar');
    Route::post('/tipo-cambio/convertir', [TipoCambioController::class, 'convertirAjax'])->name('tipo-cambio.convertir');

    // ============================================================
    // MÓDULO 1: PUNTO DE VENTA (POS)
    // ============================================================
    Route::middleware(['permiso:pos'])->group(function () {
        Route::get('/pos', [PosController::class, 'index']);
        Route::get('/pos/productos-ajax', [PosController::class, 'productosAjax']);
        Route::post('/pos/cobrar', [PosController::class, 'cobrar']);
    });

    // ============================================================
    // MÓDULO 2: CATÁLOGO DE PRODUCTOS
    // ============================================================
    Route::middleware(['permiso:productos'])->group(function () {
        Route::get('/productos', [ProductoController::class, 'index']);
        Route::get('/productos/generar-codigo', [ProductoController::class, 'generarCodigoAjax']);
        Route::post('/productos', [ProductoController::class, 'store']);
        Route::put('/productos/{id}', [ProductoController::class, 'update']);
        Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);
        Route::post('/categorias', [ProductoController::class, 'storeCategoria']);
    });

    // ============================================================
    // MÓDULO 3: GESTIÓN DE INVENTARIO Y STOCK
    // ============================================================
    Route::middleware(['permiso:inventario'])->group(function () {
        Route::get('/inventario', [InventarioController::class, 'index']);
        Route::post('/inventario', [InventarioController::class, 'store']);
    });

    // ============================================================
    // MÓDULO 4: HISTORIAL DE VENTAS Y COMPROBANTES
    // ============================================================
    Route::middleware(['permiso:ventas'])->group(function () {
        Route::get('/ventas', [VentaController::class, 'index']);
        Route::get('/ventas/{id}/pdf', [VentaController::class, 'pdf']);
        Route::post('/ventas/{id}/abono', [VentaController::class, 'registrarAbono']);
    });

    // Sub-permiso de Anulación de Ventas
    Route::post('/ventas/{id}/anular', [VentaController::class, 'anular'])->middleware(['permiso:anular_ventas']);

    // ============================================================
    // MÓDULO 5: CLIENTES REGULARES Y FRECUENTES
    // ============================================================
    Route::middleware(['permiso:clientes'])->group(function () {
        Route::get('/clientes', [ClienteController::class, 'index']);
        Route::post('/clientes', [ClienteController::class, 'store']);
        Route::get('/clientes/buscar', [ClienteController::class, 'buscarAjax']);
        Route::get('/clientes/{id}', [ClienteController::class, 'show']);
        Route::put('/clientes/{id}', [ClienteController::class, 'update']);
        Route::post('/clientes/{id}/abono', [ClienteController::class, 'registrarAbono']);
        Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);
    });

    // ============================================================
    // MÓDULO 5B: COTIZACIONES (CLIENTES Y TIENDAS EXTERNAS)
    // ============================================================
    Route::middleware(['permiso:cotizaciones'])->group(function () {
        Route::get('/cotizaciones', [CotizacionController::class, 'index'])->name('cotizaciones.index');
        Route::get('/cotizaciones/crear', [CotizacionController::class, 'create'])->name('cotizaciones.create');
        Route::post('/cotizaciones', [CotizacionController::class, 'store'])->name('cotizaciones.store');
        Route::get('/cotizaciones/{id}/pdf', [CotizacionController::class, 'pdf'])->name('cotizaciones.pdf');
        Route::get('/cotizaciones/{id}/imprimir', [CotizacionController::class, 'imprimir'])->name('cotizaciones.imprimir');
        Route::get('/cotizaciones/{id}/editar', [CotizacionController::class, 'edit'])->name('cotizaciones.edit');
        Route::get('/cotizaciones/{id}', [CotizacionController::class, 'show'])->name('cotizaciones.show');
        Route::put('/cotizaciones/{id}', [CotizacionController::class, 'update'])->name('cotizaciones.update');
        Route::post('/cotizaciones/{id}/estado', [CotizacionController::class, 'cambiarEstado'])->name('cotizaciones.estado');
        Route::delete('/cotizaciones/{id}', [CotizacionController::class, 'destroy'])->name('cotizaciones.destroy');
    });

    // ============================================================
    // MÓDULO 6: CAJA Y FINANZAS (TURNOS, GASTOS Y CAJA CHICA)
    // ============================================================
    Route::middleware(['permiso:arqueos'])->group(function () {
        // Gaveta de Efectivo (Sesiones y Cierres)
        Route::redirect('/arqueos', '/arqueos/sesion');
        Route::redirect('/arqueos/crear', '/arqueos/sesion');
        Route::get('/arqueos/sesion', [ArqueoController::class, 'sesionActual'])->name('arqueos.sesion');
        Route::get('/arqueos/sesion/{id}/detalle', [ArqueoController::class, 'detalleSesion'])->name('arqueos.sesion.detalle');
        Route::post('/arqueos/abrir', [ArqueoController::class, 'abrirCaja'])->name('arqueos.abrir');
        Route::post('/arqueos/cerrar', [ArqueoController::class, 'cerrarCaja'])->name('arqueos.cerrar');
        Route::post('/arqueos/movimiento', [ArqueoController::class, 'registrarMovimiento'])->name('arqueos.movimiento');
        Route::post('/arqueos', [ArqueoController::class, 'store']);
        Route::get('/arqueos/{id}', [ArqueoController::class, 'show']);
        Route::get('/arqueos/{id}/excel', [ArqueoController::class, 'exportarExcel']);

        // Gastos Operativos
        Route::get('/finanzas/gastos', [GastoController::class, 'index'])->name('finanzas.gastos.index');
        Route::post('/finanzas/gastos', [GastoController::class, 'store'])->name('finanzas.gastos.store');
        Route::delete('/finanzas/gastos/{id}', [GastoController::class, 'destroy'])->name('finanzas.gastos.destroy');
        Route::get('/finanzas/gastos/excel', [GastoController::class, 'exportarExcel'])->name('finanzas.gastos.excel');

        // Caja Chica (Fondo Fijo)
        Route::get('/finanzas/caja-chica', [CajaChicaController::class, 'index'])->name('finanzas.cajachica.index');
        Route::post('/finanzas/caja-chica/reposicion', [CajaChicaController::class, 'storeReposicion'])->name('finanzas.cajachica.reposicion');
        Route::post('/finanzas/caja-chica/gasto', [CajaChicaController::class, 'storeGasto'])->name('finanzas.cajachica.gasto');
        Route::post('/finanzas/caja-chica/ajuste', [CajaChicaController::class, 'storeAjuste'])->name('finanzas.cajachica.ajuste');
        Route::get('/finanzas/caja-chica/excel', [CajaChicaController::class, 'exportarExcel'])->name('finanzas.cajachica.excel');
    });

    // ============================================================
    // MÓDULO 7: REPORTES Y CIERRE DE MES
    // ============================================================
    Route::middleware(['permiso:reportes'])->group(function () {
        Route::get('/reportes', [ReporteController::class, 'hub']);
        Route::get('/reportes/ventas', [ReporteController::class, 'ventas']);
        Route::get('/reportes/ventas/excel', [ReporteController::class, 'exportarVentasExcel']);
        Route::get('/reportes/excel', [ReporteController::class, 'exportarVentasExcel']);

        Route::get('/reportes/productos', [ReporteController::class, 'productos'])->name('reportes.productos');
        Route::get('/reportes/productos/excel', [ReporteController::class, 'exportarProductosExcel'])->name('reportes.productos.excel');

        Route::get('/reportes/finanzas', [ReporteController::class, 'finanzas'])->name('reportes.finanzas');
        Route::get('/reportes/finanzas/excel', [ReporteController::class, 'exportarFinanzasExcel'])->name('reportes.finanzas.excel');

        Route::get('/reportes/cierre-mes', [ReporteController::class, 'cierreMes']);
        Route::post('/reportes/cierre-mes/cerrar', [ReporteController::class, 'storeCierreMes']);
        Route::post('/reportes/cierre-mes/reabrir', [ReporteController::class, 'reabrirCierreMes']);
        Route::post('/reportes/cierre-mes/toggle-check', [ReporteController::class, 'toggleCheckCierreMes']);
        Route::post('/reportes/cierre-mes/check-all', [ReporteController::class, 'checkAllCierreMes']);
        Route::get('/reportes/cierre-mes/excel', [ReporteController::class, 'exportarCierreMesExcel']);
    });

    // ============================================================
    // MÓDULO 8: ADMINISTRACIÓN DE USUARIOS
    // ============================================================
    Route::middleware(['permiso:usuarios'])->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index']);
        Route::post('/usuarios', [UsuarioController::class, 'store']);
        Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
        Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
    });

    // ============================================================
    // MÓDULO 9: CONFIGURACIÓN DEL SISTEMA
    // ============================================================
    Route::middleware(['permiso:configuracion'])->group(function () {
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
        Route::post('/configuracion/moneda', [ConfiguracionController::class, 'updateMoneda'])->name('configuracion.moneda');
    });

});
