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
    public function index()
    {
        $productos = Producto::with(['marca', 'unidad', 'categoria'])
            ->where('stock', '>', 0)
            ->orderBy('nombre', 'asc')
            ->get();

        $categorias = Categoria::orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombre', 'asc')->get();

        return view('pos.index', compact('productos', 'categorias', 'clientes'));
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
                $detalle = new DetalleVenta();
                $detalle->id_venta        = $venta->id;
                $detalle->id_producto     = $item['id'];
                $detalle->cantidad        = $item['cantidad'];
                $detalle->precio_unitario = $item['precio'];
                $detalle->subtotal        = $item['cantidad'] * $item['precio'];
                $detalle->save();

                // Rebajar stock y guardar auditoría Kardex
                $producto = Producto::find($item['id']);
                if ($producto) {
                    $stockAnterior = intval($producto->stock ?? 0);
                    $cantidadVendida = intval($item['cantidad']);
                    $stockNuevo = max(0, $stockAnterior - $cantidadVendida);

                    $producto->stock = $stockNuevo;
                    $producto->save();

                    // Bitácora de inventario (Kardex)
                    $movimiento = new Movimiento();
                    $movimiento->id_producto    = $item['id'];
                    $movimiento->id_usuario     = Auth::id() ?? 1;
                    $movimiento->tipo           = 'Salida';
                    $movimiento->cantidad       = $cantidadVendida;
                    $movimiento->stock_anterior = $stockAnterior;
                    $movimiento->stock_nuevo    = $stockNuevo;
                    $movimiento->motivo         = 'Venta en POS #' . $numeroFinal;
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
