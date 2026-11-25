<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gasto;
use App\Models\CategoriaGasto;
use App\Models\ArqueoCaja;
use App\Models\MovimientoCaja;
use App\Models\CajaChicaMovimiento;
use App\Models\CierreMes;
use App\Models\Configuracion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GastoController extends Controller
{
    /**
     * Listado centralizado de Gastos Operativos con filtros y KPIs.
     */
    public function index(Request $request)
    {
        $fechaHoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        // Fechas de filtro
        $fechaDesde = $request->filled('fecha_desde') ? Carbon::parse($request->fecha_desde)->startOfDay() : $inicioMes;
        $fechaHasta = $request->filled('fecha_hasta') ? Carbon::parse($request->fecha_hasta)->endOfDay() : $finMes;

        $query = Gasto::with(['categoria', 'usuario', 'turnoCaja', 'movimientoCaja'])
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta]);

        if ($request->filled('categoria_id')) {
            $query->where('categoria_gasto_id', $request->categoria_id);
        }

        if ($request->filled('origen_pago')) {
            $query->where('origen_pago', $request->origen_pago);
        }

        if ($request->filled('buscar')) {
            $term = trim($request->buscar);
            $query->where(function ($q) use ($term) {
                $q->where('descripcion', 'LIKE', "%{$term}%")
                  ->orWhere('nro_comprobante', 'LIKE', "%{$term}%")
                  ->orWhere('observacion', 'LIKE', "%{$term}%");
            });
        }

        // Totales calculados sobre la selección filtrada
        $totalesQuery = clone $query;
        $gastosTotales = $totalesQuery->get();

        $totalGastos    = $gastosTotales->sum('monto');
        $gastosGaveta   = $gastosTotales->where('origen_pago', 'gaveta')->sum('monto');
        $gastosCajaChica = $gastosTotales->where('origen_pago', 'caja_chica')->sum('monto');
        $gastosBancoQr  = $gastosTotales->whereIn('origen_pago', ['banco_qr', 'gerencia'])->sum('monto');
        $conteoGastos   = $gastosTotales->count();

        // Paginación para la tabla
        $gastos = $query->orderBy('fecha', 'desc')->paginate(20)->withQueryString();

        // Datos auxiliares para la interfaz
        $categorias = CategoriaGasto::where('activo', true)->orderBy('nombre')->get();
        $turnoActivo = ArqueoCaja::sesionActiva();
        $saldoCajaChica = CajaChicaMovimiento::saldoActual();

        return view('finanzas.gastos.index', compact(
            'gastos',
            'categorias',
            'turnoActivo',
            'saldoCajaChica',
            'totalGastos',
            'gastosGaveta',
            'gastosCajaChica',
            'gastosBancoQr',
            'conteoGastos',
            'fechaDesde',
            'fechaHasta'
        ));
    }

    /**
     * Registro unificado de un Gasto Operativo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha'              => 'required|date',
            'categoria_gasto_id' => 'required|exists:categorias_gasto,id',
            'descripcion'        => 'required|string|min:3|max:255',
            'monto'              => 'required|numeric|min:0.01',
            'origen_pago'        => 'required|in:gaveta,caja_chica,banco_qr,gerencia',
            'nro_comprobante'    => 'nullable|string|max:100',
            'observacion'        => 'nullable|string|max:500',
        ], [
            'categoria_gasto_id.required' => 'Debes seleccionar una categoría para el gasto.',
            'descripcion.required'        => 'Ingresa una descripción clara del gasto.',
            'monto.required'              => 'Ingresa el monto del gasto.',
            'monto.min'                   => 'El monto debe ser mayor a 0.',
            'origen_pago.required'        => 'Selecciona el origen del dinero.',
        ]);

        $monto = floatval($request->monto);
        $fecha = Carbon::parse($request->fecha);

        // Verificar si el mes del gasto ya está cerrado oficialmente
        $mesGasto = $fecha->format('Y-m');
        $cierreMes = CierreMes::where('mes', $mesGasto)->where('estado', 'Cerrado')->first();
        if ($cierreMes) {
            return back()->with('error', "No se pueden registrar gastos en el período {$mesGasto} porque cuenta con un Cierre de Mes Oficial congelado.");
        }

        try {
            DB::transaction(function () use ($request, $monto, $fecha) {
                $turnoId = null;
                $movimientoCajaId = null;

                if ($request->origen_pago === 'gaveta') {
                    $sesion = ArqueoCaja::sesionActiva();
                    if (!$sesion) {
                        throw new \Exception('No hay ningún Turno de Caja abierto en este momento. Abre un turno en "Turno en Vivo" antes de registrar un gasto desde gaveta, o selecciona otro origen de pago.');
                    }

                    // 1. Crear el egreso en la gaveta activa
                    $movCaja = MovimientoCaja::create([
                        'arqueo_caja_id' => $sesion->id,
                        'id_usuario'     => Auth::id() ?? 1,
                        'tipo'           => 'egreso_gasto',
                        'metodo_pago'    => 'Efectivo',
                        'monto'          => $monto,
                        'motivo'         => 'Gasto Operativo: ' . trim($request->descripcion),
                        'fecha'          => $fecha,
                    ]);

                    $sesion->increment('total_egresos', $monto);
                    $turnoId = $sesion->id;
                    $movimientoCajaId = $movCaja->id;

                    // 2. Crear el Gasto unificado
                    $gasto = Gasto::create([
                        'fecha'              => $fecha,
                        'categoria_gasto_id' => $request->categoria_gasto_id,
                        'descripcion'        => trim($request->descripcion),
                        'monto'              => $monto,
                        'origen_pago'        => 'gaveta',
                        'turno_caja_id'      => $turnoId,
                        'movimiento_caja_id' => $movimientoCajaId,
                        'nro_comprobante'    => $request->nro_comprobante ? trim($request->nro_comprobante) : null,
                        'observacion'        => $request->observacion ? trim($request->observacion) : null,
                        'user_id'            => Auth::id() ?? 1,
                    ]);

                    // Vincular de vuelta en movimientos_caja
                    $movCaja->update(['gasto_id' => $gasto->id]);

                } elseif ($request->origen_pago === 'caja_chica') {
                    $saldoActual = CajaChicaMovimiento::saldoActual();
                    if ($saldoActual < $monto) {
                        $sim = Configuracion::monedaSimbolo();
                        throw new \Exception('Saldo insuficiente en Caja Chica. Saldo disponible actual: ' . $sim . ' ' . number_format($saldoActual, 2) . '. Realiza una reposición antes de registrar este gasto.');
                    }

                    // 1. Crear el Gasto unificado
                    $gasto = Gasto::create([
                        'fecha'              => $fecha,
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

                    // 2. Registrar el egreso en Caja Chica
                    $nuevoSaldo = round($saldoActual - $monto, 2);
                    CajaChicaMovimiento::create([
                        'fecha'            => $fecha,
                        'tipo'             => 'gasto',
                        'monto'            => $monto,
                        'saldo_resultante' => $nuevoSaldo,
                        'motivo'           => 'Gasto Operativo: ' . trim($request->descripcion),
                        'gasto_id'         => $gasto->id,
                        'comprobante'      => $request->nro_comprobante ? trim($request->nro_comprobante) : null,
                        'user_id'          => Auth::id() ?? 1,
                    ]);

                } else {
                    // banco_qr o gerencia (no tocan gaveta ni caja chica)
                    Gasto::create([
                        'fecha'              => $fecha,
                        'categoria_gasto_id' => $request->categoria_gasto_id,
                        'descripcion'        => trim($request->descripcion),
                        'monto'              => $monto,
                        'origen_pago'        => $request->origen_pago,
                        'turno_caja_id'      => null,
                        'movimiento_caja_id' => null,
                        'nro_comprobante'    => $request->nro_comprobante ? trim($request->nro_comprobante) : null,
                        'observacion'        => $request->observacion ? trim($request->observacion) : null,
                        'user_id'            => Auth::id() ?? 1,
                    ]);
                }
            });

            $sim = Configuracion::monedaSimbolo();
            return back()->with('success', "¡Gasto operativo de {$sim} " . number_format($monto, 2) . ' registrado con éxito!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Eliminación de un gasto con validación estricta de integridad y reversión.
     */
    public function destroy(string $id)
    {
        $gasto = Gasto::findOrFail($id);

        // 1. Validar si el mes cuenta con cierre oficial congelado
        $mesGasto = Carbon::parse($gasto->fecha)->format('Y-m');
        $cierreMes = CierreMes::where('mes', $mesGasto)->where('estado', 'Cerrado')->first();
        if ($cierreMes) {
            return back()->with('error', "No se puede eliminar este gasto porque pertenece al mes {$mesGasto}, el cual cuenta con un Cierre de Mes Oficial congelado.");
        }

        // 2. Validar si pertenece a un turno de gaveta cerrado
        if ($gasto->turno_caja_id) {
            $turno = ArqueoCaja::find($gasto->turno_caja_id);
            if ($turno && $turno->estado === 'cerrado') {
                return back()->with('error', "No se puede eliminar este gasto porque pertenece al Turno de Caja #{$turno->id}, el cual ya se encuentra cerrado.");
            }
        }

        try {
            DB::transaction(function () use ($gasto) {
                // Si el origen fue gaveta y el turno sigue abierto
                if ($gasto->origen_pago === 'gaveta') {
                    if ($gasto->movimiento_caja_id) {
                        $mov = MovimientoCaja::find($gasto->movimiento_caja_id);
                        if ($mov) {
                            $mov->delete();
                        }
                    }
                    if ($gasto->turno_caja_id) {
                        $turno = ArqueoCaja::find($gasto->turno_caja_id);
                        if ($turno && $turno->estado === 'abierto') {
                            $turno->decrement('total_egresos', $gasto->monto);
                        }
                    }
                } elseif ($gasto->origen_pago === 'caja_chica') {
                    // Reversión auditable en Caja Chica para no romper la cadena de saldos
                    $saldoPrevio = CajaChicaMovimiento::saldoActual();
                    $nuevoSaldo = round($saldoPrevio + floatval($gasto->monto), 2);

                    CajaChicaMovimiento::create([
                        'fecha'            => now(),
                        'tipo'             => 'reversion_gasto',
                        'monto'            => $gasto->monto,
                        'saldo_resultante' => $nuevoSaldo,
                        'motivo'           => "Reversión por anulación del gasto #{$gasto->id}: {$gasto->descripcion}",
                        'gasto_id'         => $gasto->id,
                        'comprobante'      => $gasto->nro_comprobante,
                        'user_id'          => Auth::id() ?? 1,
                    ]);
                }

                $gasto->delete();
            });

            return back()->with('success', 'Gasto operativo eliminado y saldo revertido correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al intentar eliminar el gasto: ' . $e->getMessage());
        }
    }

    /**
     * Exportación de gastos a CSV / Excel con compatibilidad UTF-8.
     */
    public function exportarExcel(Request $request)
    {
        $fechaDesde = $request->filled('fecha_desde') ? Carbon::parse($request->fecha_desde)->startOfDay() : Carbon::now()->startOfMonth();
        $fechaHasta = $request->filled('fecha_hasta') ? Carbon::parse($request->fecha_hasta)->endOfDay() : Carbon::now()->endOfMonth();

        $query = Gasto::with(['categoria', 'usuario', 'turnoCaja'])
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta]);

        if ($request->filled('categoria_id')) {
            $query->where('categoria_gasto_id', $request->categoria_id);
        }

        if ($request->filled('origen_pago')) {
            $query->where('origen_pago', $request->origen_pago);
        }

        if ($request->filled('buscar')) {
            $term = trim($request->buscar);
            $query->where(function ($q) use ($term) {
                $q->where('descripcion', 'LIKE', "%{$term}%")
                  ->orWhere('nro_comprobante', 'LIKE', "%{$term}%");
            });
        }

        $gastos = $query->orderBy('fecha', 'desc')->get();

        $filename = 'Gastos_Operativos_' . $fechaDesde->format('Ymd') . '_' . $fechaHasta->format('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $sim = Configuracion::monedaSimbolo();
        $callback = function () use ($gastos, $sim) {
            $file = fopen('php://output', 'w');
            // BOM UTF-8 para apertura correcta en Microsoft Excel
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID',
                'Fecha y Hora',
                'Categoría',
                'Descripción',
                "Monto ({$sim})",
                'Origen de Pago',
                'Turno Gaveta #',
                'N° Comprobante',
                'Observaciones',
                'Usuario Registrador',
            ], ';');

            foreach ($gastos as $g) {
                $origenTexto = match($g->origen_pago) {
                    'gaveta'     => 'Gaveta (Efectivo Turno)',
                    'caja_chica' => 'Caja Chica',
                    'banco_qr'   => 'Banco / QR',
                    'gerencia'   => 'Aporte Directo de Gerencia',
                    default      => $g->origen_pago,
                };

                fputcsv($file, [
                    $g->id,
                    $g->fecha ? $g->fecha->format('d/m/Y H:i') : '',
                    $g->categoria ? $g->categoria->nombre : 'Sin categoría',
                    $g->descripcion,
                    number_format($g->monto, 2, '.', ''),
                    $origenTexto,
                    $g->turno_caja_id ?? 'N/A',
                    $g->nro_comprobante ?? '',
                    $g->observacion ?? '',
                    $g->usuario ? $g->usuario->name : 'Sistema',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
