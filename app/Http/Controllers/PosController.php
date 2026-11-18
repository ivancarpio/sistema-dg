<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Movimiento;
use App\Models\Categoria;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $productos = Producto::with(['marca', 'unidad', 'unidadSecundaria', 'categoria'])
            ->where('stock', '>', 0)
            ->orderBy('nombre', 'asc')
            ->get();

        $categorias = Categoria::orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombre', 'asc')->get();

        // Historial de clientes que ya registraron compras previas (para autocompletado y validación)
        $clientesHistorial = Venta::select('cliente', DB::raw('count(*) as total_compras'), DB::raw('max(fecha) as ultima_compra'))
            ->whereNotNull('cliente')
            ->where('cliente', '!=', '')
            ->groupBy('cliente')
            ->orderBy('total_compras', 'desc')
            ->get();

        $ventaRehacer = null;
        $carritoRehacer = [];

        // Rehacer venta anulada o previa: restaurar productos y precios tal cual se cobró
        if ($request->filled('rehacer_venta')) {
            $idVenta = intval($request->rehacer_venta);
            $ventaRehacer = Venta::with([
                'detalles.producto.marca', 
                'detalles.producto.unidad', 
                'detalles.producto.unidadSecundaria',
                'detalles.producto.categoria',
                'clienteRelacionado'
            ])->find($idVenta);

            if ($ventaRehacer) {
                foreach ($ventaRehacer->detalles as $det) {
                    $p = $det->producto;
                    if (!$p) continue;

                    $unidadMedidaDet = trim($det->unidad_medida ?? '');
                    $factorDet = floatval($det->factor_conversion ?? 1);
                    $nombreSec = optional($p->unidadSecundaria)->nombre;

                    // Determinar si fue vendido en unidad secundaria
                    $esSec = ($factorDet > 1) || (!empty($nombreSec) && strcasecmp($unidadMedidaDet, $nombreSec) === 0);
                    $tipoUnidad = $esSec ? 'secundaria' : 'base';

                    $unidadBase = optional($p->unidad)->nombre ?? 'Pieza';
                    $unidadSec = $nombreSec ?? 'Paquete';
                    $factorSec = max(1, floatval($p->factor_conversion ?? 1));

                    $pVentaBase = floatval($p->precio_venta ?? 0);
                    $pClienteBase = ($p->precio_cliente !== null && floatval($p->precio_cliente) > 0) ? floatval($p->precio_cliente) : $pVentaBase;
                    $tienePCBase = ($p->precio_cliente !== null && floatval($p->precio_cliente) > 0);

                    $pVentaSec = floatval($p->precio_venta_secundario ?? 0);
                    $pClienteSec = ($p->precio_cliente_secundario !== null && floatval($p->precio_cliente_secundario) > 0) ? floatval($p->precio_cliente_secundario) : $pVentaSec;
                    $tienePCSec = ($p->precio_cliente_secundario !== null && floatval($p->precio_cliente_secundario) > 0);

                    $stockBase = intval($p->stock ?? 0);
                    $stockMax = $esSec ? intval(floor($stockBase / $factorSec)) : $stockBase;
                    $cantVendida = floatval($det->cantidad);

                    $precioCobrado = floatval($det->precio_unitario);

                    $carritoRehacer[] = [
                        'key' => "{$p->id}_{$tipoUnidad}",
                        'id' => $p->id,
                        'codigo' => $p->codigo ?? '',
                        'nombre' => $p->nombre,
                        'tipoUnidad' => $tipoUnidad,
                        'unidadMedida' => $unidadMedidaDet ?: ($esSec ? $unidadSec : $unidadBase),
                        'factorConversion' => $esSec ? $factorSec : 1,
                        'tieneSecundaria' => !empty($p->id_unidad_secundaria),
                        'unidadBase' => $unidadBase,
                        'unidadSecundaria' => $unidadSec,
                        'factorSecundaria' => $factorSec,
                        'precioVentaBase' => $pVentaBase,
                        'precioClienteBase' => $pClienteBase,
                        'tienePrecioClienteBase' => $tienePCBase,
                        'precioVentaSecundario' => $pVentaSec,
                        'precioClienteSecundario' => $pClienteSec,
                        'tienePrecioClienteSec' => $tienePCSec,
                        'stockBase' => $stockBase,
                        'precioVenta' => $esSec ? $pVentaSec : $pVentaBase,
                        'precioCliente' => $esSec ? $pClienteSec : $pClienteBase,
                        'tienePrecioCliente' => $esSec ? $tienePCSec : $tienePCBase,
                        'precio' => $precioCobrado, // Precio exacto con el que se cobró
                        'esTarifaCliente' => ($ventaRehacer->tipo_tarifa === 'Regular'),
                        'cantidad' => $cantVendida,
                        'stockMaximo' => max(intval($cantVendida), $stockMax),
                    ];
                }
            }
        }

        return view('pos.index', compact('productos', 'categorias', 'clientes', 'clientesHistorial', 'ventaRehacer', 'carritoRehacer'));
    }

    /**
     * Retorna el catálogo actualizado para refrescar la tabla del POS vía AJAX sin recargar la página.
     */
    public function productosAjax()
    {
        $productos = Producto::with(['marca', 'unidad', 'unidadSecundaria', 'categoria'])
            ->where('stock', '>', 0)
            ->orderBy('nombre', 'asc')
            ->get();

        return response()->json([
            'success'   => true,
            'total'     => $productos->count(),
            'productos' => $productos
        ]);
    }

    public function cobrar(Request $request)
    {
        if (!$request->has('productos') || empty($request->productos)) {
            return back()->with('error', 'El carrito está vacío. Agrega al menos un producto.');
        }

        $request->validate([
            'cliente'     => 'required|string|max:255',
            'metodo_pago' => 'required|in:Efectivo,QR,Por Pagar',
        ], [
            'cliente.required'     => 'El nombre del cliente es obligatorio.',
            'metodo_pago.required' => 'Debes seleccionar un método de pago.',
        ]);

        DB::beginTransaction();

        try {
            $totalVenta = floatval($request->total_venta);
            $metodoPago = $request->metodo_pago;

            // Gestión de "Por Pagar"
            if ($metodoPago === 'Por Pagar') {
                $montoAdelanto = max(0, floatval($request->monto_adelanto ?? 0));
                if ($montoAdelanto > $totalVenta) {
                    $montoAdelanto = $totalVenta;
                }
                $montoPagado    = $montoAdelanto;
                $saldoPendiente = max(0, $totalVenta - $montoPagado);
                $metodoAdelanto = ($montoPagado > 0) ? ($request->metodo_adelanto ?? 'Efectivo') : null;
                $estado         = ($saldoPendiente > 0) ? 'Pendiente' : 'Completada';
            } else {
                $montoPagado    = $totalVenta;
                $saldoPendiente = 0.00;
                $metodoAdelanto = null;
                $estado         = 'Completada';
            }

            // Gestión de número de nota / talonario
            $notaManual = trim($request->input('numero_nota', ''));
            if (!empty($notaManual)) {
                $numeroFinal = $notaManual;
            } else {
                $ultimoId = DB::table('ventas')->max('id') + 1;
                $numeroFinal = 'SYS-' . str_pad($ultimoId, 6, '0', STR_PAD_LEFT);
            }

            // Asociar cliente registrado
            $idCliente = $request->id_cliente ? intval($request->id_cliente) : null;
            if (!$idCliente && !empty($request->cliente)) {
                $cliEncontrado = Cliente::where('nombre', trim($request->cliente))->first();
                if ($cliEncontrado) {
                    $idCliente = $cliEncontrado->id;
                }
            }

            // Registrar venta
            $venta = new Venta();
            $venta->id_cliente      = $idCliente;
            $venta->cliente         = $request->cliente;
            $venta->tipo_tarifa     = $request->input('tipo_tarifa', 'Ocasional');
            $venta->metodo_pago     = $metodoPago;
            $venta->metodo_adelanto = $metodoAdelanto;
            $venta->monto_pagado    = $montoPagado;
            $venta->saldo_pendiente = $saldoPendiente;
            $venta->id_usuario      = Auth::id() ?? 1;
            $venta->fecha           = now();
            $venta->total           = $totalVenta;
            $venta->numero_nota     = $numeroFinal;
            $venta->estado          = $estado;
            $venta->save();

            // Detalle de la venta y rebaja de stock
            foreach ($request->productos as $item) {
                $idProducto = $item['id'];
                $cant = floatval($item['cantidad']);
                $factor = (!empty($item['factor_conversion']) && floatval($item['factor_conversion']) > 0)
                    ? floatval($item['factor_conversion'])
                    : 1.0;
                $unidad = !empty($item['unidad_medida']) ? trim($item['unidad_medida']) : null;
                $precio = floatval($item['precio']);

                $detalle = new DetalleVenta();
                $detalle->id_venta          = $venta->id;
                $detalle->id_producto       = $idProducto;
                $detalle->unidad_medida     = $unidad;
                $detalle->factor_conversion = $factor;
                $detalle->cantidad          = $cant;
                $detalle->precio_unitario   = $precio;
                $detalle->subtotal          = $cant * $precio;
                $detalle->save();

                // Rebajar stock y guardar auditoría Kardex
                $producto = Producto::find($idProducto);
                if ($producto) {
                    $stockAnterior = intval($producto->stock ?? 0);
                    // Las unidades físicas a descontar equivalen a cantidad vendida * factor_conversion
                    $unidadesFisicas = intval(ceil($cant * $factor));
                    $stockNuevo = max(0, $stockAnterior - $unidadesFisicas);

                    $producto->stock = $stockNuevo;
                    $producto->save();

                    // Bitácora de inventario (Kardex)
                    $textoUnidad = $unidad ? " ({$cant} {$unidad})" : "";
                    $movimiento = new Movimiento();
                    $movimiento->id_producto    = $idProducto;
                    $movimiento->id_usuario     = Auth::id() ?? 1;
                    $movimiento->tipo           = 'Salida';
                    $movimiento->cantidad       = $unidadesFisicas;
                    $movimiento->stock_anterior = $stockAnterior;
                    $movimiento->stock_nuevo    = $stockNuevo;
                    $movimiento->motivo         = 'Venta en POS #' . $numeroFinal . $textoUnidad;
                    $movimiento->fecha          = now();
                    $movimiento->save();
                }
            }

            DB::commit();

            $mensaje = ($metodoPago === 'Por Pagar' && $saldoPendiente > 0)
                ? '¡Venta Por Pagar registrada! Adelanto: Bs. ' . number_format($montoPagado, 2) . ' | Saldo Pendiente: Bs. ' . number_format($saldoPendiente, 2)
                : '¡Venta cobrada con éxito!';

            return redirect('/pos')
                ->with('success_venta_id', $venta->id)
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }
}
