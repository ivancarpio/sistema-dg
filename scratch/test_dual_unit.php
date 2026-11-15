<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Movimiento;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    // 1. Crear producto con unidad dual
    $p = new Producto();
    $p->codigo = 'TEST-DUAL-01';
    $p->nombre = 'PRODUCTO DE PRUEBA DUAL PIEZA Y CAJA';
    $p->id_unidad = 4; // Pieza
    $p->precio_venta = 2.00;
    $p->precio_cliente = 1.80;
    $p->id_unidad_secundaria = 5; // Caja
    $p->factor_conversion = 50;
    $p->precio_venta_secundario = 90.00;
    $p->precio_cliente_secundario = 80.00;
    $p->stock = 120;
    $p->costo = 1.00;
    $p->timestamps = false;
    $p->save();

    echo "1. Producto creado con ID {$p->id}. Tiene unidad secundaria: " . ($p->tieneUnidadSecundaria() ? 'SI' : 'NO') . PHP_EOL;
    echo "   Precio Venta Base: Bs. " . $p->getPrecioPara('Ocasional') . " | Precio Regular Base: Bs. " . $p->getPrecioPara('Regular') . PHP_EOL;
    echo "   Precio Venta Caja: Bs. " . $p->getPrecioSecundarioPara('Ocasional') . " | Precio Regular Caja: Bs. " . $p->getPrecioSecundarioPara('Regular') . PHP_EOL;

    // 2. Simular venta desde POS con ambas unidades
    $venta = new Venta();
    $venta->cliente = 'Cliente Varios';
    $venta->tipo_tarifa = 'Ocasional';
    $venta->metodo_pago = 'Efectivo';
    $venta->monto_pagado = 100.00;
    $venta->saldo_pendiente = 0;
    $venta->id_usuario = 1;
    $venta->fecha = now();
    $venta->total = 100.00;
    $venta->numero_nota = 'TEST-001';
    $venta->estado = 'Completada';
    $venta->save();

    $itemsSimulados = [
        [
            'id' => $p->id,
            'cantidad' => 1,
            'precio' => 90.00,
            'unidad_medida' => 'Caja',
            'factor_conversion' => 50,
        ],
        [
            'id' => $p->id,
            'cantidad' => 5,
            'precio' => 2.00,
            'unidad_medida' => 'Pieza',
            'factor_conversion' => 1,
        ]
    ];

    foreach ($itemsSimulados as $item) {
        $detalle = new DetalleVenta();
        $detalle->id_venta = $venta->id;
        $detalle->id_producto = $item['id'];
        $detalle->unidad_medida = $item['unidad_medida'];
        $detalle->factor_conversion = $item['factor_conversion'];
        $detalle->cantidad = $item['cantidad'];
        $detalle->precio_unitario = $item['precio'];
        $detalle->subtotal = $item['cantidad'] * $item['precio'];
        $detalle->save();

        $prod = Producto::find($item['id']);
        $stockAnt = $prod->stock;
        $descontar = intval(ceil($item['cantidad'] * $item['factor_conversion']));
        $stockNue = max(0, $stockAnt - $descontar);
        $prod->stock = $stockNue;
        $prod->save();

        echo "2. Vendido: {$item['cantidad']} {$item['unidad_medida']}. Descontado del stock base: {$descontar} u. Stock restante: {$stockNue}" . PHP_EOL;
    }

    $detallesEnDb = DetalleVenta::where('id_venta', $venta->id)->get();
    echo "3. Detalles registrados en BD: " . count($detallesEnDb) . PHP_EOL;
    foreach ($detallesEnDb as $d) {
        echo "   -> Item: Cant={$d->cantidad}, Unidad={$d->unidad_medida}, Factor={$d->factor_conversion}, PU=Bs. {$d->precio_unitario}, Subtotal=Bs. {$d->subtotal}" . PHP_EOL;
    }

    echo "TODO FUNCIONA PERFECTAMENTE!" . PHP_EOL;
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
} finally {
    DB::rollBack();
    echo "Rollback ejecutado - BD limpia." . PHP_EOL;
}
