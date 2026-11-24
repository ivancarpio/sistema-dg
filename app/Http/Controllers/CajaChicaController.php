<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CajaChicaMovimiento;
use App\Models\Gasto;
use App\Models\CategoriaGasto;
use App\Models\ArqueoCaja;
use App\Models\MovimientoCaja;
use App\Models\CierreMes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CajaChicaController extends Controller
{
    /**
     * Panel principal de Caja Chica (Fondo Fijo).
     */
    public function index(Request $request)
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        $fechaDesde = $request->filled('fecha_desde') ? Carbon::parse($request->fecha_desde)->startOfDay() : $inicioMes;
        $fechaHasta = $request->filled('fecha_hasta') ? Carbon::parse($request->fecha_hasta)->endOfDay() : $finMes;

        $query = CajaChicaMovimiento::with(['usuario', 'gasto.categoria', 'turnoCaja'])
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta]);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('buscar')) {
            $term = trim($request->buscar);
            $query->where(function ($q) use ($term) {
                $q->where('motivo', 'LIKE', "%{$term}%")
                  ->orWhere('comprobante', 'LIKE', "%{$term}%");
            });
        }

        // Métricas financieras de Caja Chica
        $saldoActual = CajaChicaMovimiento::saldoActual();

        $movimientosMes = CajaChicaMovimiento::whereBetween('fecha', [$inicioMes, $finMes])->get();
        $reposicionesMes = $movimientosMes->where('tipo', 'apertura_o_reposicion')->sum('monto');
        $gastosMes = $movimientosMes->where('tipo', 'gasto')->sum('monto');
        $reversionesMes = $movimientosMes->where('tipo', 'reversion_gasto')->sum('monto');
        $ajustesCount = $movimientosMes->whereIn('tipo', ['ajuste_ingreso', 'ajuste_egreso'])->count();

        // Movimientos paginados
        $movimientos = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        // Helpers de vista
        $turnoActivo = ArqueoCaja::sesionActiva();
        $categorias = CategoriaGasto::where('activo', true)->orderBy('nombre')->get();

        return view('finanzas.caja_chica.index', compact(
            'movimientos',
            'saldoActual',
            'reposicionesMes',
            'gastosMes',
            'reversionesMes',
            'ajustesCount',
            'turnoActivo',
            'categorias',
            'fechaDesde',
            'fechaHasta'
        ));
    }

    /**
     * Registrar apertura o reposición de fondos en Caja Chica.
     */
    public function storeReposicion(Request $request)
    {
        $request->validate([
            'monto'          => 'required|numeric|min:0.5',
            'origen_fondos'  => 'required|in:gaveta,externo',
            'motivo'         => 'nullable|string|max:255',
            'comprobante'    => 'nullable|string|max:100',
        ], [
            'monto.required'         => 'Ingresa el monto a reponer en Caja Chica.',
            'monto.min'              => 'El monto debe ser de al menos Bs. 0.50.',
            'origen_fondos.required' => 'Indica el origen del dinero para la reposición.',
        ]);

        $monto = floatval($request->monto);

        try {
            DB::transaction(function () use ($request, $monto) {
                $turnoId = null;
                $movCajaId = null;

                if ($request->origen_fondos === 'gaveta') {
                    $sesion = ArqueoCaja::sesionActiva();
                    if (!$sesion) {
                        throw new \Exception('Para retirar fondos de la gaveta debe existir un Turno de Caja abierto en este momento.');
                    }

                    // Registrar egreso de tipo retiro en el turno (no es gasto operativo)
                    $movCaja = MovimientoCaja::create([
                        'arqueo_caja_id' => $sesion->id,
                        'id_usuario'     => Auth::id() ?? 1,
                        'tipo'           => 'egreso_retiro',
                        'metodo_pago'    => 'Efectivo',
                        'monto'          => $monto,
                        'motivo'         => 'Traslado a Caja Chica: ' . (trim($request->motivo) ?: 'Reposición de Fondo Fijo'),
                        'fecha'          => now(),
                    ]);

                    $sesion->increment('total_egresos', $monto);
                    $turnoId = $sesion->id;
                    $movCajaId = $movCaja->id;
                }

                $saldoPrevio = CajaChicaMovimiento::saldoActual();
                $nuevoSaldo = round($saldoPrevio + $monto, 2);

                CajaChicaMovimiento::create([
                    'fecha'              => now(),
                    'tipo'               => 'apertura_o_reposicion',
                    'monto'              => $monto,
                    'saldo_resultante'   => $nuevoSaldo,
                    'motivo'             => trim($request->motivo) ?: ($request->origen_fondos === 'gaveta' ? 'Reposición desde Gaveta de Turno #' . $turnoId : 'Aporte / Reposición Externa'),
                    'turno_caja_id'      => $turnoId,
                    'movimiento_caja_id' => $movCajaId,
                    'comprobante'        => $request->comprobante ? trim($request->comprobante) : null,
                    'user_id'            => Auth::id() ?? 1,
                ]);
            });

            return back()->with('success', '¡Reposición de Bs. ' . number_format($monto, 2) . ' registrada exitosamente en Caja Chica!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Registrar un gasto rápido pagado desde Caja Chica.
     */
    public function storeGasto(Request $request)
    {
        $request->validate([
            'categoria_gasto_id' => 'required|exists:categorias_gasto,id',
            'descripcion'        => 'required|string|min:3|max:255',
            'monto'              => 'required|numeric|min:0.01',
            'nro_comprobante'    => 'nullable|string|max:100',
            'observacion'        => 'nullable|string|max:500',
        ], [
            'categoria_gasto_id.required' => 'Selecciona la categoría del gasto.',
            'descripcion.required'        => 'Describe en qué se gastó el dinero.',
            'monto.required'              => 'Ingresa el monto del gasto.',
        ]);

        $monto = floatval($request->monto);
        $saldoActual = CajaChicaMovimiento::saldoActual();

        if ($saldoActual < $monto) {
            return back()->with('error', 'Saldo insuficiente en Caja Chica. Saldo disponible actual: Bs. ' . number_format($saldoActual, 2) . '. Repón fondos antes de registrar este egreso.');
        }

        try {
            DB::transaction(function () use ($request, $monto, $saldoActual) {
                // 1. Crear registro en gastos
                $gasto = Gasto::create([
                    'fecha'              => now(),
                    'categoria_gasto_id' => $request->categoria_gasto_id,
                    'descripcion'        => trim($request->descripcion),
                    'monto'              => $monto,
                    'origen_pago'        => 'caja_chica',
                    'turno_caja_id'      => null,
                    'movimiento_caja_id' => null,
                    'nro_comprobante'    => $request->nro_comprobante ? trim($request->nro_comprobante) : null,
                    'observacion'        => $request->observacion ? trim($request->observacion) : null,
                    'user_id'            => Auth::id() ?? 1,
                ]);

                // 2. Registrar movimiento en caja chica
                $nuevoSaldo = round($saldoActual - $monto, 2);
                CajaChicaMovimiento::create([
                    'fecha'            => now(),
                    'tipo'             => 'gasto',
                    'monto'            => $monto,
                    'saldo_resultante' => $nuevoSaldo,
                    'motivo'           => 'Gasto Operativo: ' . trim($request->descripcion),
                    'gasto_id'         => $gasto->id,
                    'comprobante'      => $request->nro_comprobante ? trim($request->nro_comprobante) : null,
                    'user_id'          => Auth::id() ?? 1,
                ]);
            });

            return back()->with('success', '¡Gasto de Bs. ' . number_format($monto, 2) . ' pagado desde Caja Chica registrado!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el gasto: ' . $e->getMessage());
        }
    }

    /**
     * Registrar un ajuste manual auditable (ingreso o egreso).
     */
    public function storeAjuste(Request $request)
    {
        $request->validate([
            'tipo'        => 'required|in:ajuste_ingreso,ajuste_egreso',
            'monto'       => 'required|numeric|min:0.01',
            'motivo'      => 'required|string|min:5|max:255',
            'comprobante' => 'nullable|string|max:100',
        ], [
            'tipo.required'   => 'Especifica si el ajuste es un ingreso (+) o un egreso (-).',
            'monto.required'  => 'Ingresa el monto del ajuste.',
            'motivo.required' => 'Debes detallar la justificación o motivo de auditoría para este ajuste.',
            'motivo.min'      => 'El motivo de auditoría debe tener al menos 5 caracteres.',
        ]);

        $monto = floatval($request->monto);
        $saldoPrevio = CajaChicaMovimiento::saldoActual();

        if ($request->tipo === 'ajuste_egreso' && $saldoPrevio < $monto) {
            return back()->with('error', 'El ajuste de egreso (Bs. ' . number_format($monto, 2) . ') no puede superar el saldo actual de Caja Chica (Bs. ' . number_format($saldoPrevio, 2) . ').');
        }

        try {
            DB::transaction(function () use ($request, $monto, $saldoPrevio) {
                $nuevoSaldo = ($request->tipo === 'ajuste_ingreso')
                    ? round($saldoPrevio + $monto, 2)
                    : round($saldoPrevio - $monto, 2);

                CajaChicaMovimiento::create([
                    'fecha'            => now(),
                    'tipo'             => $request->tipo,
                    'monto'            => $monto,
                    'saldo_resultante' => $nuevoSaldo,
                    'motivo'           => 'Ajuste de Auditoría: ' . trim($request->motivo),
                    'comprobante'      => $request->comprobante ? trim($request->comprobante) : null,
                    'user_id'          => Auth::id() ?? 1,
                ]);
            });

            $signo = $request->tipo === 'ajuste_ingreso' ? '+' : '-';
            return back()->with('success', "¡Ajuste de auditoría ({$signo}Bs. " . number_format($monto, 2) . ") registrado con éxito!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al registrar el ajuste: ' . $e->getMessage());
        }
    }

    /**
     * Exportación de movimientos de Caja Chica a CSV / Excel con UTF-8 BOM.
     */
    public function exportarExcel(Request $request)
    {
        $fechaDesde = $request->filled('fecha_desde') ? Carbon::parse($request->fecha_desde)->startOfDay() : Carbon::now()->startOfMonth();
        $fechaHasta = $request->filled('fecha_hasta') ? Carbon::parse($request->fecha_hasta)->endOfDay() : Carbon::now()->endOfMonth();

        $query = CajaChicaMovimiento::with(['usuario', 'gasto.categoria'])
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta]);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('buscar')) {
            $term = trim($request->buscar);
            $query->where(function ($q) use ($term) {
                $q->where('motivo', 'LIKE', "%{$term}%")
                  ->orWhere('comprobante', 'LIKE', "%{$term}%");
            });
        }

        $movimientos = $query->orderBy('id', 'desc')->get();

        $filename = 'Caja_Chica_Movimientos_' . $fechaDesde->format('Ymd') . '_' . $fechaHasta->format('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($movimientos) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID',
                'Fecha y Hora',
                'Tipo de Movimiento',
                'Monto (Bs.)',
                'Saldo Resultante (Bs.)',
                'Concepto / Justificación',
                'N° Comprobante',
                'Gasto Vinculado #',
                'Turno Gaveta #',
                'Usuario',
            ], ';');

            foreach ($movimientos as $m) {
                $tipoTexto = match($m->tipo) {
                    'apertura_o_reposicion' => 'Reposición / Entrada',
                    'gasto'                 => 'Gasto Operativo',
                    'reversion_gasto'       => 'Reversión de Gasto (+)',
                    'ajuste_ingreso'        => 'Ajuste Ingreso (+)',
                    'ajuste_egreso'         => 'Ajuste Egreso (-)',
                    default                 => $m->tipo,
                };

                fputcsv($file, [
                    $m->id,
                    $m->fecha ? $m->fecha->format('d/m/Y H:i') : '',
                    $tipoTexto,
                    number_format($m->monto, 2, '.', ''),
                    number_format($m->saldo_resultante, 2, '.', ''),
                    $m->motivo,
                    $m->comprobante ?? '',
                    $m->gasto_id ?? 'N/A',
                    $m->turno_caja_id ?? 'N/A',
                    $m->usuario ? $m->usuario->name : 'Sistema',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
