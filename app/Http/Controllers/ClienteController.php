<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::with(['ventas' => function ($q) {
            $q->where('estado', '!=', 'Anulada');
        }]);

        // Buscador
        if ($request->filled('buscar')) {
            $b = trim($request->buscar);
            $query->where(function ($q) use ($b) {
                $q->where('nombre', 'LIKE', "%{$b}%")
                  ->orWhere('telefono', 'LIKE', "%{$b}%")
                  ->orWhere('ci_nit', 'LIKE', "%{$b}%");
            });
        }

        $clientes = $query->orderBy('nombre', 'asc')->get();

        // Estadísticas
        $totalClientes = $clientes->count();
        $clientesConDeuda = $clientes->filter(fn($c) => $c->saldo_pendiente_total > 0)->count();
        $totalDeudaPorCobrar = $clientes->sum(fn($c) => $c->saldo_pendiente_total);
        $totalCompradoHistorico = $clientes->sum(fn($c) => $c->total_comprado);

        // Filtro por estado de cuenta
        if ($request->filled('estado')) {
            if ($request->estado === 'deuda') {
                $clientes = $clientes->filter(fn($c) => $c->saldo_pendiente_total > 0);
            } elseif ($request->estado === 'al_dia') {
                $clientes = $clientes->filter(fn($c) => $c->saldo_pendiente_total == 0);
            }
        }

        return view('clientes.index', compact(
            'clientes',
            'totalClientes',
            'clientesConDeuda',
            'totalDeudaPorCobrar',
            'totalCompradoHistorico'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'ci_nit'   => 'nullable|string|max:50',
            'direccion'=> 'nullable|string|max:255',
            'email'    => 'nullable|email|max:100',
            'notas'    => 'nullable|string',
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
        ]);

        $cliente = Cliente::create([
            'nombre'    => trim($request->nombre),
            'telefono'  => $request->telefono ? trim($request->telefono) : null,
            'ci_nit'    => $request->ci_nit ? trim($request->ci_nit) : null,
            'direccion' => $request->direccion ? trim($request->direccion) : null,
            'email'     => $request->email ? trim($request->email) : null,
            'notas'     => $request->notas ? trim($request->notas) : null,
        ]);

        // Si la petición viene vía AJAX (desde el POS)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cliente' => $cliente,
                'mensaje' => '¡Cliente registrado con éxito!',
            ]);
        }

        return back()->with('success', '¡Cliente registrado con éxito en el sistema!');
    }

    public function show($id)
    {
        $cliente = Cliente::with([
            'ventas' => function ($q) {
                $q->orderBy('fecha', 'desc');
            },
            'ventas.detalles.producto',
            'pagos' => function ($q) {
                $q->orderBy('fecha', 'desc');
            },
            'pagos.usuario',
            'pagos.venta'
        ])->findOrFail($id);

        return view('clientes.show', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'ci_nit'   => 'nullable|string|max:50',
            'direccion'=> 'nullable|string|max:255',
            'email'    => 'nullable|email|max:100',
        ]);

        $cliente = Cliente::findOrFail($id);
        $cliente->update($request->only([
            'nombre', 'telefono', 'ci_nit', 'direccion', 'email', 'notas'
        ]));

        return back()->with('success', '¡Datos del cliente actualizados correctamente!');
    }

    public function destroy($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            
            // Verificar si tiene saldo pendiente (Bloqueo absoluto)
            if ($cliente->saldo_pendiente_total > 0) {
                return back()->withErrors(['No se puede eliminar al cliente "' . $cliente->nombre . '" porque tiene un saldo pendiente de Bs. ' . number_format($cliente->saldo_pendiente_total, 2) . '. El cliente debe liquidar su deuda antes de ser eliminado del sistema.']);
            }

            // Desvincular ventas históricas sin perder los comprobantes
            $cliente->ventas()->update(['id_cliente' => null]);

            $nombre = $cliente->nombre;
            $cliente->delete();
            return back()->with('success', "¡Cliente '{$nombre}' eliminado del sistema correctamente!");
        } catch (\Exception $e) {
            return back()->withErrors('Error al eliminar cliente: ' . $e->getMessage());
        }
    }

    // Registrar abono o pago de deuda de un cliente
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

        $cliente = Cliente::findOrFail($id);
        $saldoPendienteTotal = floatval($cliente->saldo_pendiente_total);

        if ($saldoPendienteTotal <= 0) {
            return back()->with('error', "El cliente '{$cliente->nombre}' no tiene deudas pendientes (Saldo: Bs. 0.00).");
        }

        $abonoTotal = floatval($request->monto_abono);
        if ($abonoTotal > $saldoPendienteTotal) {
            $abonoTotal = $saldoPendienteTotal;
        }

        $montoRestante = $abonoTotal;

        DB::beginTransaction();
        try {
            // Si el usuario especificó una nota concreta
            if ($request->filled('id_venta') && $request->id_venta !== 'todas') {
                $ventas = $cliente->ventas()
                    ->where('id', $request->id_venta)
                    ->where('estado', '!=', 'Anulada')
                    ->where('saldo_pendiente', '>', 0)
                    ->get();
            } else {
                // Cobrar a las notas más antiguas primero (FIFO)
                $ventas = $cliente->ventas()
                    ->where('estado', '!=', 'Anulada')
                    ->where('saldo_pendiente', '>', 0)
                    ->orderBy('fecha', 'asc')
                    ->get();
            }

            foreach ($ventas as $v) {
                if ($montoRestante <= 0) break;

                $saldoNota = floatval($v->saldo_pendiente ?? 0);
                $pagoNota = min($montoRestante, $saldoNota);

                $nuevoSaldoNota = max(0, round($saldoNota - $pagoNota, 2));
                $nuevoPagadoNota = round(floatval($v->monto_pagado ?? 0) + $pagoNota, 2);

                $v->monto_pagado = $nuevoPagadoNota;
                $v->saldo_pendiente = $nuevoSaldoNota;
                if ($nuevoSaldoNota == 0) {
                    $v->estado = 'Completada';
                }
                $v->save();

                \App\Models\PagoVenta::create([
                    'id_venta'    => $v->id,
                    'id_cliente'  => $cliente->id,
                    'id_usuario'  => auth()->id() ?? 1,
                    'monto'       => $pagoNota,
                    'metodo_pago' => $request->metodo_pago,
                    'observacion' => $request->observacion ? trim($request->observacion) : 'Abono a cuenta de cliente',
                    'fecha'       => now(),
                ]);

                $montoRestante = round($montoRestante - $pagoNota, 2);
            }

            DB::commit();

            $saldoRestanteCliente = max(0, round($saldoPendienteTotal - $abonoTotal, 2));
            $msg = "¡Cobro registrado! Se cobraron Bs. " . number_format($abonoTotal, 2) . " a {$cliente->nombre} por {$request->metodo_pago}.";
            if ($saldoRestanteCliente == 0) {
                $msg .= " ¡La deuda quedó 100% SALDADA (Al día)!";
            } else {
                $msg .= " Saldo pendiente restante: Bs. " . number_format($saldoRestanteCliente, 2) . ".";
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el cobro: ' . $e->getMessage());
        }
    }

    // Endpoint AJAX para buscar clientes en tiempo real en el POS
    public function buscarAjax(Request $request)
    {
        $term = trim($request->query('q', ''));
        if (empty($term)) {
            $clientes = Cliente::orderBy('nombre', 'asc')->take(10)->get();
        } else {
            $clientes = Cliente::where('nombre', 'LIKE', "%{$term}%")
                ->orWhere('telefono', 'LIKE', "%{$term}%")
                ->orWhere('ci_nit', 'LIKE', "%{$term}%")
                ->orderBy('nombre', 'asc')
                ->take(15)
                ->get();
        }

        return response()->json($clientes);
    }
}
