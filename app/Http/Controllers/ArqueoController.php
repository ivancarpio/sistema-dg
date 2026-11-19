<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arqueo;
use App\Models\ArqueoCaja;
use App\Models\MovimientoCaja;
use App\Models\PagoVenta;
use App\Models\Venta;
use App\Models\User;
use App\Exports\ArqueoExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArqueoController extends Controller
{
    // ============================================================
    // 0. SESIÓN DE CAJA EN TIEMPO REAL (ARQUEO POR SESIONES)
    // ============================================================

    /**
     * Muestra la sesión de caja activa o la opción para abrir un nuevo turno.
     */
    public function sesionActual(Request $request)
    {
        $sesion = ArqueoCaja::with([
            'usuario', 
            'pagos.venta', 
            'pagos.cliente', 
            'movimientos.usuario', 
            'movimientos.venta'
        ])->where('estado', 'abierto')->latest('fecha_apertura')->first();

        $sesionesCerradas = ArqueoCaja::with('usuario')
            ->where('estado', 'cerrado')
            ->latest('fecha_cierre')
            ->take(10)
            ->get();

        return view('arqueos.sesion', compact('sesion', 'sesionesCerradas'));
    }

    /**
     * Abre un nuevo turno de caja con su monto inicial.
     */
    public function abrirCaja(Request $request)
    {
        $sesionActiva = ArqueoCaja::where('estado', 'abierto')->first();
        if ($sesionActiva) {
            return back()->with('error', 'Ya existe un turno de caja abierto actualmente (Turno #' . $sesionActiva->id . '). Debes cerrarlo antes de abrir uno nuevo.');
        }

        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
        ], [
            'monto_inicial.required' => 'Debes ingresar el monto inicial en gaveta (puede ser 0.00).',
            'monto_inicial.numeric'  => 'El monto inicial debe ser un número válido.',
        ]);

        $montoInicial = floatval($request->monto_inicial ?? 0);

        $sesion = ArqueoCaja::create([
            'id_usuario'     => Auth::id() ?? 1,
            'fecha_apertura' => now(),
            'monto_inicial'  => $montoInicial,
            'estado'         => 'abierto',
            'observaciones'  => $request->observaciones ? trim($request->observaciones) : null,
        ]);

        return redirect('/arqueos/sesion')->with('success', '¡Turno de caja #' . $sesion->id . ' iniciado exitosamente con Bs. ' . number_format($montoInicial, 2) . ' en gaveta!');
    }

    /**
     * Cierra el turno de caja activo, calcula sobrante/faltante e inmuta la sesión.
     */
    public function cerrarCaja(Request $request)
    {
        $sesion = ArqueoCaja::where('estado', 'abierto')->latest('fecha_apertura')->first();
        if (!$sesion) {
            return back()->with('error', 'No existe ningún turno de caja abierto para cerrar.');
        }

        $request->validate([
            'monto_fisico_declarado' => 'required|numeric|min:0',
        ], [
            'monto_fisico_declarado.required' => 'Debes ingresar el total de efectivo físico contado en tu gaveta.',
            'monto_fisico_declarado.numeric'  => 'El monto físico debe ser un valor numérico válido.',
        ]);

        $declarado = floatval($request->monto_fisico_declarado);
        $esperado  = $sesion->efectivo_esperado;
        $diferencia = round($declarado - $esperado, 2);

        $sesion->fecha_cierre = now();
        $sesion->monto_fisico_declarado = $declarado;
        $sesion->diferencia = $diferencia;
        $sesion->estado = 'cerrado';

        if ($request->filled('observaciones')) {
            $notaCierre = trim($request->observaciones);
            $sesion->observaciones = ($sesion->observaciones ? $sesion->observaciones . ' | Cierre: ' : 'Cierre: ') . $notaCierre;
        }

        $sesion->save();

        $estadoDif = ($diferencia == 0)
            ? '¡Caja cuadra exactamente al centavo!'
            : ($diferencia > 0 ? 'Sobrante en caja de +Bs. ' . number_format($diferencia, 2) : 'Faltante en caja de -Bs. ' . number_format(abs($diferencia), 2));

        return redirect('/arqueos/sesion')->with('success', "¡Turno de caja #{$sesion->id} cerrado con éxito! Efectivo esperado: Bs. " . number_format($esperado, 2) . " | Declarado: Bs. " . number_format($declarado, 2) . " ({$estadoDif})");
    }

    /**
     * Registra un movimiento directo en caja (gasto operativo, retiro o ingreso extra).
     */
    public function registrarMovimiento(Request $request)
    {
        $sesion = ArqueoCaja::where('estado', 'abierto')->latest('fecha_apertura')->first();
        if (!$sesion) {
            return back()->with('error', 'Debes tener un turno de caja abierto para registrar gastos o movimientos.');
        }

        $request->validate([
            'tipo'        => 'required|in:egreso_gasto,ingreso_extra',
            'metodo_pago' => 'required|in:Efectivo,QR,Transferencia',
            'monto'       => 'required|numeric|min:0.5',
            'motivo'      => 'required|string|min:3|max:255',
        ], [
            'tipo.required'        => 'Selecciona el tipo de movimiento.',
            'metodo_pago.required' => 'Selecciona el método de pago.',
            'monto.required'       => 'Ingresa el monto del movimiento.',
            'motivo.required'      => 'Describe el motivo u observación del movimiento.',
        ]);

        $monto = floatval($request->monto);

        MovimientoCaja::create([
            'arqueo_caja_id' => $sesion->id,
            'id_usuario'     => Auth::id() ?? 1,
            'tipo'           => $request->tipo,
            'metodo_pago'    => $request->metodo_pago,
            'monto'          => $monto,
            'motivo'         => trim($request->motivo),
            'fecha'          => now(),
        ]);

        if ($request->tipo === 'egreso_gasto') {
            $sesion->increment('total_egresos', $monto);
            $msg = "Gasto de Bs. " . number_format($monto, 2) . " registrado como egreso en caja.";
        } else {
            $msg = "Ingreso extra de Bs. " . number_format($monto, 2) . " registrado.";
        }

        return back()->with('success', $msg);
    }

    // ============================================================
    // 1. LISTADO DE ARQUEOS (Pantalla principal)
    // ============================================================
    public function index(Request $request)
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes    = Carbon::now()->endOfMonth();

        $arqueosEsteMes = Arqueo::whereBetween('created_at', [$inicioMes, $finMes])->count();
        $totalArqueadoMes = Arqueo::whereBetween('created_at', [$inicioMes, $finMes])->sum('total_general');

        // Ventas no arqueadas y no anuladas
        $ventasSinArquear = Venta::whereNull('arqueo_id')
            ->where('estado', '!=', 'Anulada')
            ->count();

        // El monto sin arquear es el dinero real cobrado (monto_pagado)
        $montoSinArquear = Venta::whereNull('arqueo_id')
            ->where('estado', '!=', 'Anulada')
            ->sum(DB::raw('COALESCE(monto_pagado, total)'));

        $query = Arqueo::with('usuario')->latest();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('id', 'LIKE', "%{$buscar}%")
                  ->orWhereHas('usuario', function ($qu) use ($buscar) {
                      $qu->where('name', 'LIKE', "%{$buscar}%");
                  });
            });
        }

        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        $arqueos = $query->paginate(20)->withQueryString();
        $usuarios = User::all();

        return view('arqueos.index', compact(
            'arqueos',
            'arqueosEsteMes',
            'totalArqueadoMes',
            'ventasSinArquear',
            'montoSinArquear',
            'usuarios'
        ));
    }

    // ============================================================
    // 2. FORMULARIO DE CREACIÓN (ver ventas pendientes)
    // ============================================================
    public function create(Request $request)
    {
        $query = Venta::with(['usuario', 'detalles.producto', 'pagos'])
            ->whereNull('arqueo_id')
            ->where('estado', '!=', 'Anulada');

        $periodo = $request->input('periodo', 'hoy');
        switch ($periodo) {
            case 'semana':
                $query->whereBetween('fecha', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'mes':
                $query->whereBetween('fecha', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                break;
            case 'hoy':
            default:
                $query->whereDate('fecha', Carbon::today());
                break;
        }

        if ($request->filled('metodo_pago')) {
            $metodo = $request->metodo_pago;
            if ($metodo === 'Por Pagar') {
                $query->where('saldo_pendiente', '>', 0);
            } elseif ($metodo === 'QR') {
                $query->where(function($q) {
                    $q->where('metodo_pago', 'QR')
                      ->orWhereHas('pagos', function($qp) {
                          $qp->where('metodo_pago', 'QR');
                      });
                });
            } elseif ($metodo === 'Efectivo') {
                $query->where(function($q) {
                    $q->where('metodo_pago', 'Efectivo')
                      ->orWhereHas('pagos', function($qp) {
                          $qp->where('metodo_pago', 'Efectivo');
                      });
                });
            }
        }

        if ($request->filled('cajero')) {
            $query->where('id_usuario', $request->cajero);
        }

        $ventasPendientes = $query->orderBy('fecha', 'desc')->get();

        // Calcular dinero real cobrado por canal
        $totalEfectivo = 0;
        $totalQr = 0;

        foreach ($ventasPendientes as $v) {
            $desglose = $this->calcularDesgloseCobro($v);
            $totalEfectivo += $desglose['efectivo'];
            $totalQr       += $desglose['qr'];
        }

        $resumen = [
            'efectivo' => $totalEfectivo,
            'qr'       => $totalQr,
            'total'    => $totalEfectivo + $totalQr,
            'cantidad' => $ventasPendientes->count(),
        ];

        $usuarios = User::all();

        return view('arqueos.create', compact('ventasPendientes', 'resumen', 'usuarios', 'periodo'));
    }

    // ============================================================
    // 3. GUARDAR EL ARQUEO
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'ventas_seleccionadas'   => 'required|array|min:1',
            'ventas_seleccionadas.*' => 'integer|exists:ventas,id',
        ], [
            'ventas_seleccionadas.required' => 'Debes seleccionar al menos una venta para crear el arqueo.',
            'ventas_seleccionadas.min'      => 'Selecciona al menos 1 venta.',
        ]);

        DB::beginTransaction();
        try {
            $ids = $request->ventas_seleccionadas;

            $ventas = Venta::with(['pagos'])
                ->whereIn('id', $ids)
                ->whereNull('arqueo_id')
                ->where('estado', '!=', 'Anulada')
                ->get();

            if ($ventas->isEmpty()) {
                return back()->with('error', 'Las ventas seleccionadas no son válidas o ya fueron arqueadas.');
            }

            $totalEfectivo = 0;
            $totalQr = 0;

            foreach ($ventas as $v) {
                $desglose = $this->calcularDesgloseCobro($v);
                $totalEfectivo += $desglose['efectivo'];
                $totalQr       += $desglose['qr'];
            }

            $totalGeneral = $totalEfectivo + $totalQr;

            $arqueo = Arqueo::create([
                'id_usuario'     => Auth::id() ?? 1,
                'total_efectivo' => $totalEfectivo,
                'total_qr'       => $totalQr,
                'total_general'  => $totalGeneral,
                'cantidad_ventas'=> $ventas->count(),
                'observaciones'  => $request->observaciones,
            ]);

            foreach ($ventas as $venta) {
                $venta->arqueo_id = $arqueo->id;
                $venta->save();

                DB::table('arqueo_venta')->insert([
                    'arqueo_id' => $arqueo->id,
                    'venta_id'  => $venta->id,
                ]);
            }

            DB::commit();
            return redirect('/arqueos/' . $arqueo->id)
                ->with('success', '¡Arqueo de Caja #' . $arqueo->id . ' creado correctamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el arqueo: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 4. DETALLE DE UN ARQUEO
    // ============================================================
    public function show($id)
    {
        $arqueo = Arqueo::with(['usuario', 'ventas.usuario', 'ventas.detalles.producto', 'ventas.pagos'])->findOrFail($id);
        return view('arqueos.show', compact('arqueo'));
    }

    // ============================================================
    // 5. EXPORTAR ARQUEO A EXCEL
    // ============================================================
    public function exportarExcel($id)
    {
        $arqueo = Arqueo::with(['usuario', 'ventas.usuario', 'ventas.detalles.producto', 'ventas.pagos'])->findOrFail($id);
        $nombreArchivo = 'Arqueo_Caja_#' . $arqueo->id . '_' . $arqueo->created_at->format('Ymd_His') . '.xlsx';
        return Excel::download(new ArqueoExport($arqueo), $nombreArchivo);
    }

    /**
     * Calcula el desglose real de dinero recaudado por canal (efectivo y qr).
     */
    private function calcularDesgloseCobro(Venta $v): array
    {
        $efectivo = 0.0;
        $qr = 0.0;

        // Si tiene pagos registrados en la tabla pagos_ventas
        $pagos = $v->relationLoaded('pagos') ? $v->pagos : $v->pagos()->get();
        if ($pagos->isNotEmpty()) {
            foreach ($pagos as $p) {
                if (strtolower($p->metodo_pago) === 'qr') {
                    $qr += floatval($p->monto);
                } else {
                    $efectivo += floatval($p->monto);
                }
            }
            return ['efectivo' => $efectivo, 'qr' => $qr];
        }

        // Si no tiene pagos en pagos_ventas, evaluar venta directa
        $monto = $v->monto_pagado !== null ? floatval($v->monto_pagado) : floatval($v->total);
        if (strtolower($v->metodo_pago) === 'qr' || strtolower($v->metodo_adelanto ?? '') === 'qr') {
            $qr += $monto;
        } elseif (strtolower($v->metodo_pago) === 'efectivo' || strtolower($v->metodo_adelanto ?? '') === 'efectivo') {
            $efectivo += $monto;
        } elseif ($v->metodo_pago === 'Por Pagar') {
            // Sin ningún abono ni adelanto
            // No suma dinero real todavía
        } else {
            $efectivo += $monto;
        }

        return ['efectivo' => $efectivo, 'qr' => $qr];
    }
}
