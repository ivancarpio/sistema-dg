<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Movimiento;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Configuracion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    // 1. Mostrar el historial de ventas y análisis de compradores
    public function index(Request $request)
    {
        $query = Venta::with(['usuario', 'usuarioAnulacion']);

        // --- FILTROS ---
        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where(function($q) use ($buscar) {
                $q->where('cliente', 'LIKE', "%{$buscar}%")
                  ->orWhere('numero_nota', 'LIKE', "%{$buscar}%");
            });
        }
        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }
        if ($request->filled('cajero')) {
            $query->where('id_usuario', $request->cajero);
        }

        // Filtro de Estado / Tipo amigable e intuitivo
        if ($request->filled('filtro_tipo')) {
            $ft = $request->filtro_tipo;
            if ($ft === 'completadas') {
                $query->where('estado', '!=', 'Anulada');
            } elseif ($ft === 'anuladas') {
                $query->where('estado', 'Anulada');
            } elseif ($ft === 'fisicas') {
                $query->where('numero_nota', 'NOT LIKE', 'SYS-%');
            } elseif ($ft === 'sistema') {
                $query->where('numero_nota', 'LIKE', 'SYS-%');
            }
        } else {
            // Compatibilidad
            if ($request->has('notas_fisicas')) {
                $query->where('numero_nota', 'NOT LIKE', 'SYS-%');
            }
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
        }

        // --- ORDENAMIENTO (SORTING) ---
        $sortBy = $request->input('sort_by', 'fecha');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSorts = ['numero_nota', 'fecha', 'cliente', 'total'];
        
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('fecha', 'desc');
        }

        $ventas = $query->get();
        $cajeros = User::orderBy('name')->get();

        // Métricas superiores para el panel
        $totalVentasCount = Venta::count();
        $totalFacturado = Venta::where('estado', '!=', 'Anulada')->sum('total');
        $totalCompletadas = Venta::where('estado', '!=', 'Anulada')->count();
        $totalFisicas = Venta::where('estado', '!=', 'Anulada')->where('numero_nota', 'NOT LIKE', 'SYS-%')->count();
        $totalAnuladas = Venta::where('estado', 'Anulada')->count();

        // Compradores Recurrentes / Análisis de Clientes de Mostrador
        $compradores = Venta::where('estado', '!=', 'Anulada')
            ->whereNotNull('cliente')
            ->where('cliente', '!=', '')
            ->where('cliente', '!=', 'Cliente Ocasional')
            ->select(
                'cliente',
                DB::raw('COUNT(*) as total_compras'),
                DB::raw('SUM(total) as total_gastado'),
                DB::raw('MAX(fecha) as ultima_compra'),
                DB::raw('MAX(id_cliente) as id_cliente_asociado')
            )
            ->groupBy('cliente')
            ->orderByDesc('total_compras')
            ->get();

        $clientesRegulares = Cliente::pluck('id', 'nombre')->toArray();

        return view('ventas.index', compact(
            'ventas', 'cajeros', 'totalFacturado', 'totalCompletadas',
            'totalFisicas', 'totalAnuladas', 'totalVentasCount',
            'compradores', 'clientesRegulares'
        ));
    }

    // 2. Generar el PDF
    public function pdf($id)
    {
        $venta = Venta::with(['detalles.producto', 'usuario'])->findOrFail($id);

        $empresa = [
            'nombre'    => Configuracion::get('nombre_empresa', 'Distribuidora Guadalupe'),
            'telefono'  => Configuracion::get('telefono_empresa', ''),
            'direccion' => Configuracion::get('direccion_empresa', ''),
            'pie'       => Configuracion::get('pie_ticket', '¡Gracias por su preferencia!'),
        ];

        // Codificación base64 para que DomPDF procese el logo sin restricciones de sistema de archivos
        $logoPath = public_path('img/logo_white.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('img/logo.png');
        }
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }

        $pdf = Pdf::loadView('ventas.pdf', compact('venta', 'empresa', 'logoBase64'));
        return $pdf->stream('Nota_Venta_' . $venta->numero_nota . '.pdf');
    }

    // 3. Anular venta con registro de auditoría (motivo, usuario y fecha)
    public function anular(Request $request, $id)
    {
        if (auth()->check() && !auth()->user()->tienePermiso('anular_ventas')) {
            return back()->with('error', 'No tienes permiso para anular ventas. Contacta a un administrador.');
        }

        $request->validate([
            'motivo' => 'required|string|min:3',
        ], [
            'motivo.required' => 'Debes ingresar el motivo de la anulación.',
            'motivo.min'      => 'El motivo debe tener al menos 3 caracteres.',
        ]);

        $venta = Venta::with('detalles')->findOrFail($id);

        if ($venta->estado == 'Anulada') {
            return back()->with('error', 'Esta venta ya fue anulada anteriormente.');
        }

        DB::beginTransaction();
        try {
            $motivoTexto = trim($request->motivo);
            $usuarioId   = Auth::id() ?? 1;

            // 1. Recorrer los productos de esta venta
            foreach ($venta->detalles as $detalle) {
                $producto = Producto::find($detalle->id_producto ?? $detalle->producto_id); 
                
                if ($producto) {
                    $stockAnterior = intval($producto->stock ?? 0);
                    $cantidadDevuelta = intval($detalle->cantidad);
                    $stockNuevo = $stockAnterior + $cantidadDevuelta;

                    // A. Devolvemos el stock físico al módulo de Productos
                    $producto->stock = $stockNuevo; 
                    $producto->save();
                    
                    // B. Registramos la auditoría en el módulo de Inventario (Kardex de Devolución)
                    $movimiento = new Movimiento();
                    $movimiento->id_producto    = $producto->id;
                    $movimiento->tipo           = 'Devolución';
                    $movimiento->cantidad       = $cantidadDevuelta;
                    $movimiento->stock_anterior = $stockAnterior;
                    $movimiento->stock_nuevo    = $stockNuevo;
                    $movimiento->motivo         = 'Devolución por Anulación #' . ($venta->numero_nota ?? ('SYS-'.str_pad($venta->id, 6, '0', STR_PAD_LEFT))) . ' (Motivo: ' . $motivoTexto . ')';
                    $movimiento->id_usuario     = $usuarioId;
                    $movimiento->fecha          = now();
                    $movimiento->save();
                }
            }

            // 2. Marcar la venta como anulada con trazabilidad completa
            $venta->estado               = 'Anulada';
            $venta->motivo_anulacion     = $motivoTexto;
            $venta->id_usuario_anulacion = $usuarioId;
            $venta->fecha_anulacion      = now();
            $venta->save();

            DB::commit();
            return back()->with('success', 'Venta anulada correctamente. Stock devuelto al inventario y bitácora de anulación registrada.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error crítico al anular: ' . $e->getMessage());
        }
    }

    // 4. Registrar abono o cobro de saldo pendiente para una nota
    public function registrarAbono(Request $request, $id)
    {
        $request->validate([
            'monto_abono' => 'required|numeric|min:0.5',
            'metodo_pago' => 'required|in:Efectivo,QR',
            'observacion' => 'nullable|string|max:255',
        ], [
            'monto_abono.required' => 'Debes ingresar el monto a cobrar.',
            'monto_abono.numeric'  => 'El monto debe ser un valor numérico válido.',
            'monto_abono.min'      => 'El monto mínimo a cobrar es Bs. 0.50.',
            'metodo_pago.required' => 'Debes seleccionar el método de pago del cobro.',
        ]);

        $venta = Venta::findOrFail($id);

        if ($venta->estado === 'Anulada') {
            return back()->with('error', 'No se puede registrar pagos en una venta anulada.');
        }

        $saldoActual = floatval($venta->saldo_pendiente ?? 0);
        if ($saldoActual <= 0) {
            return back()->with('error', 'Esta venta ya se encuentra liquidada al 100%. Saldo pendiente: Bs. 0.00.');
        }

        $abono = floatval($request->monto_abono);
        if ($abono > $saldoActual) {
            $abono = $saldoActual;
        }

        DB::beginTransaction();
        try {
            $nuevoSaldo = max(0, round($saldoActual - $abono, 2));
            $nuevoPagado = round(floatval($venta->monto_pagado ?? 0) + $abono, 2);

            $venta->monto_pagado = $nuevoPagado;
            $venta->saldo_pendiente = $nuevoSaldo;
            if ($nuevoSaldo == 0) {
                $venta->estado = 'Completada';
            }
            $venta->save();

            \App\Models\PagoVenta::create([
                'id_venta'    => $venta->id,
                'id_cliente'  => $venta->id_cliente,
                'id_usuario'  => auth()->id() ?? 1,
                'monto'       => $abono,
                'metodo_pago' => $request->metodo_pago,
                'observacion' => $request->observacion ? trim($request->observacion) : 'Cobro de saldo pendiente',
                'fecha'       => now(),
            ]);

            DB::commit();

            $mensaje = "¡Cobro registrado exitosamente! Se cobraron Bs. " . number_format($abono, 2) . " mediante " . $request->metodo_pago . ".";
            if ($nuevoSaldo == 0) {
                $mensaje .= " La nota " . ($venta->numero_nota ?? ('SYS-'.$venta->id)) . " quedó 100% LIQUIDADA.";
            } else {
                $mensaje .= " Saldo restante: Bs. " . number_format($nuevoSaldo, 2) . ".";
            }

            return back()->with('success', $mensaje);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el cobro: ' . $e->getMessage());
        }
    }
}