<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\User;
use App\Models\Producto;
use App\Models\CierreMesCheck;
use App\Models\CierreMes;
use App\Exports\VentasExport;
use App\Exports\CierreMesExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReporteController extends Controller
{
    // ============================================================
    // 1. HUB / CATÁLOGO PRINCIPAL DE REPORTES (Vista tipo SaaS)
    // ============================================================
    public function hub()
    {
        $mesActual = Carbon::now()->format('Y-m');
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes    = Carbon::now()->endOfMonth();

        $ventasMes = Venta::where('estado', '!=', 'Anulada')
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->count();

        $totalRecaudadoMes = Venta::where('estado', '!=', 'Anulada')
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->sum(DB::raw('COALESCE(monto_pagado, total)'));

        $totalProductos = Producto::count();

        $checksMes = CierreMesCheck::where('mes', $mesActual)
            ->where('verificado', true)
            ->count();

        $porcentajeCierre = $totalProductos > 0 ? round(($checksMes / $totalProductos) * 100) : 0;

        return view('reportes.hub', compact(
            'ventasMes',
            'totalRecaudadoMes',
            'totalProductos',
            'checksMes',
            'porcentajeCierre',
            'mesActual'
        ));
    }

    // ============================================================
    // 2. REPORTE DE VENTAS DETALLADO (Efectivo / QR / Por Pagar)
    // ============================================================
    public function ventas(Request $request)
    {
        $periodo = $request->input('periodo', 'hoy');
        [$fechaDesde, $fechaHasta, $periodoLabel] = $this->getRango($request, $periodo);

        $query = Venta::with(['usuario', 'detalles.producto'])
            ->where('estado', '!=', 'Anulada')
            ->whereBetween('fecha', [$fechaDesde->startOfDay(), $fechaHasta->copy()->endOfDay()]);

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('cajero')) {
            $query->where('id_usuario', $request->cajero);
        }

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('cliente', 'LIKE', "%{$b}%")
                  ->orWhere('numero_nota', 'LIKE', "%{$b}%");
            });
        }

        $ventas = $query->orderBy('fecha', 'desc')->get();

        // Totales discriminados
        $totalEfectivo = 0;
        $totalQr = 0;
        $totalPorPagarPendiente = 0;

        foreach ($ventas as $v) {
            $cobrado = $v->monto_pagado !== null ? floatval($v->monto_pagado) : floatval($v->total);
            $pendiente = floatval($v->saldo_pendiente ?? 0);

            if ($v->metodo_pago === 'Efectivo') {
                $totalEfectivo += $cobrado;
            } elseif ($v->metodo_pago === 'QR') {
                $totalQr += $cobrado;
            } elseif ($v->metodo_pago === 'Por Pagar') {
                $totalPorPagarPendiente += $pendiente;
                if ($v->metodo_adelanto === 'QR') {
                    $totalQr += $cobrado;
                } else {
                    $totalEfectivo += $cobrado;
                }
            }
        }

        $totales = [
            'efectivo'   => $totalEfectivo,
            'qr'         => $totalQr,
            'por_pagar'  => $totalPorPagarPendiente,
            'general'    => $ventas->sum('total'),
            'recaudado'  => $totalEfectivo + $totalQr,
            'cantidad'   => $ventas->count(),
            'productos'  => $ventas->flatMap->detalles->sum('cantidad'),
        ];

        $usuarios = User::all();

        return view('reportes.index', compact(
            'ventas', 'totales', 'usuarios',
            'periodo', 'periodoLabel',
            'fechaDesde', 'fechaHasta'
        ));
    }

    // Exportar Ventas a Excel
    public function exportarVentasExcel(Request $request)
    {
        $periodo = $request->input('periodo', 'hoy');
        [$fechaDesde, $fechaHasta, $periodoLabel] = $this->getRango($request, $periodo);

        $query = Venta::with(['usuario', 'detalles.producto'])
            ->where('estado', '!=', 'Anulada')
            ->whereBetween('fecha', [$fechaDesde->startOfDay(), $fechaHasta->copy()->endOfDay()]);

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('cajero')) {
            $query->where('id_usuario', $request->cajero);
        }

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('cliente', 'LIKE', "%{$b}%")
                  ->orWhere('numero_nota', 'LIKE', "%{$b}%");
            });
        }

        $ventas = $query->orderBy('fecha', 'desc')->get();

        // Totales discriminados para el Excel
        $totalEfectivo = 0;
        $totalQr = 0;
        $totalPorPagar = 0;

        foreach ($ventas as $v) {
            $cobrado = $v->monto_pagado !== null ? floatval($v->monto_pagado) : floatval($v->total);
            if ($v->metodo_pago === 'Efectivo') {
                $totalEfectivo += $cobrado;
            } elseif ($v->metodo_pago === 'QR') {
                $totalQr += $cobrado;
            } elseif ($v->metodo_pago === 'Por Pagar') {
                $totalPorPagar += floatval($v->saldo_pendiente ?? 0);
                if ($v->metodo_adelanto === 'QR') {
                    $totalQr += $cobrado;
                } else {
                    $totalEfectivo += $cobrado;
                }
            }
        }

        $totales = [
            'efectivo'  => $totalEfectivo,
            'qr'        => $totalQr,
            'por_pagar' => $totalPorPagar,
            'general'   => $ventas->sum('total'),
        ];

        $filtros = [
            'periodo_label' => $periodoLabel,
            'metodo_pago'   => $request->metodo_pago,
        ];

        $nombreArchivo = 'Reporte_Ventas_' . $fechaDesde->format('Ymd') . '_' . $fechaHasta->format('Ymd') . '.xlsx';

        return Excel::download(new VentasExport($ventas, $filtros, $totales), $nombreArchivo);
    }

    // ============================================================
    // 3. REPORTE DE CIERRE DE MES (Interactivo)
    // ============================================================
    public function cierreMes(Request $request)
    {
        $mes = $request->input('mes', Carbon::now()->format('Y-m'));
        $datos = $this->obtenerDatosCierreMes($mes);

        $categorias = DB::table('categorias')->orderBy('nombre')->get();

        // Buscar si este mes ya tiene un Cierre Oficial registrado
        $cierreActual = CierreMes::with('usuario')->where('mes', $mes)->first();

        // Obtener historial completo de cierres de mes
        $historialCierres = CierreMes::with('usuario')->orderBy('mes', 'desc')->get();

        return view('reportes.cierre_mes', array_merge($datos, [
            'mesSeleccionado'  => $mes,
            'categorias'       => $categorias,
            'cierreActual'     => $cierreActual,
            'historialCierres' => $historialCierres,
        ]));
    }

    // ============================================================
    // GUARDAR CIERRE OFICIAL DE MES (SNAPSHOT PERMANENTE)
    // ============================================================
    public function storeCierreMes(Request $request)
    {
        $request->validate([
            'mes'   => 'required|string|regex:/^\d{4}-\d{2}$/',
            'notas' => 'nullable|string|max:1000',
        ]);

        $mes = $request->mes;
        $datos = $this->obtenerDatosCierreMes($mes);

        $snapshot = array_map(function($f) {
            return [
                'id'                => $f['id'],
                'codigo'            => $f['codigo'],
                'nombre'            => $f['nombre'],
                'cantidad_anterior' => $f['cantidad_anterior'],
                'ventas'            => $f['ventas'],
                'total_tienda'      => $f['total_tienda'],
                'verificado'        => $f['verificado'],
            ];
        }, $datos['items']);

        CierreMes::updateOrCreate(
            ['mes' => $mes],
            [
                'total_productos'   => $datos['totalProductos'],
                'total_ventas'      => $datos['totalVentas'],
                'total_stock'       => $datos['totalEnTienda'],
                'verificados_count' => $datos['verificadosCount'],
                'porcentaje'        => $datos['porcentaje'],
                'id_usuario'        => Auth::id() ?? 1,
                'fecha_cierre'      => now(),
                'notas'             => $request->notas,
                'snapshot_items'    => $snapshot,
                'estado'            => 'Cerrado',
            ]
        );

        $nombreMes = Carbon::parse($mes . '-01')->translatedFormat('F Y');
        return redirect('/reportes/cierre-mes?mes=' . $mes)
            ->with('success', "¡El mes {$nombreMes} ha sido cerrado oficialmente con éxito! El cuadre y las cifras han quedado archivados.");
    }

    // ============================================================
    // REABRIR MES CERRADO (SOLO ADMINISTRADOR)
    // ============================================================
    public function reabrirCierreMes(Request $request)
    {
        $request->validate([
            'mes' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        if (Auth::user() && Auth::user()->rol !== 'admin') {
            return back()->with('error', 'Solo los administradores tienen permiso para reabrir un mes cerrado.');
        }

        $cierre = CierreMes::where('mes', $request->mes)->first();
        if ($cierre) {
            $cierre->delete();
        }

        $nombreMes = Carbon::parse($request->mes . '-01')->translatedFormat('F Y');
        return redirect('/reportes/cierre-mes?mes=' . $request->mes)
            ->with('info', "El mes {$nombreMes} ha sido reabierto para edición y nuevos cuadres de stock.");
    }

    public function toggleCheckCierreMes(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|integer',
            'mes'         => 'required|string',
        ]);

        $check = CierreMesCheck::firstOrNew([
            'id_producto' => $request->id_producto,
            'mes'         => $request->mes,
        ]);

        $check->verificado   = !$check->verificado;
        $check->id_usuario   = Auth::id();
        $check->save();

        $totalProductos = Producto::count();
        $verificados    = CierreMesCheck::where('mes', $request->mes)->where('verificado', true)->count();
        $porcentaje     = $totalProductos > 0 ? round(($verificados / $totalProductos) * 100) : 0;

        return response()->json([
            'success'     => true,
            'verificado'  => $check->verificado,
            'verificados' => $verificados,
            'totalChecks' => $verificados,
            'pendientes'  => max(0, $totalProductos - $verificados),
            'total'       => $totalProductos,
            'porcentaje'  => $porcentaje,
        ]);
    }

    public function checkAllCierreMes(Request $request)
    {
        $request->validate([
            'mes'    => 'required|string',
            'accion' => 'required|in:marcar,desmarcar',
        ]);

        $mes    = $request->mes;
        $marcar = $request->accion === 'marcar';

        $productos = Producto::all();
        foreach ($productos as $p) {
            CierreMesCheck::updateOrCreate(
                ['id_producto' => $p->id, 'mes' => $mes],
                ['verificado' => $marcar, 'id_usuario' => Auth::id()]
            );
        }

        $verificados = $marcar ? $productos->count() : 0;
        $total       = $productos->count();
        $porcentaje  = $marcar ? 100 : 0;

        return response()->json([
            'success'     => true,
            'verificados' => $verificados,
            'totalChecks' => $verificados,
            'pendientes'  => max(0, $total - $verificados),
            'total'       => $total,
            'porcentaje'  => $porcentaje,
        ]);
    }

    public function exportarCierreMesExcel(Request $request)
    {
        $mes = $request->input('mes', Carbon::now()->format('Y-m'));
        $datos = $this->obtenerDatosCierreMes($mes);

        $meta = [
            'mes'           => $mes,
            'anio'          => Carbon::parse($mes . '-01')->format('Y'),
            'periodo_label' => Carbon::parse($mes . '-01')->translatedFormat('F Y'),
            'empresa'       => 'DISTRIBUIDORA GUADALUPE',
        ];

        $nombreArchivo = 'Cierre_Mes_' . str_replace('-', '_', $mes) . '.xlsx';
        return Excel::download(new CierreMesExport($datos['filas'], $meta), $nombreArchivo);
    }

    private function getRango(Request $request, string $periodo): array
    {
        switch ($periodo) {
            case 'semana':
                return [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                    'Esta Semana (' . Carbon::now()->startOfWeek()->format('d/m') . ' al ' . Carbon::now()->endOfWeek()->format('d/m/Y') . ')'
                ];
            case 'mes':
                return [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth(),
                    'Este Mes (' . Carbon::now()->translatedFormat('F Y') . ')'
                ];
            case 'personalizado':
                $desde = $request->filled('fecha_desde') ? Carbon::parse($request->fecha_desde) : Carbon::today();
                $hasta = $request->filled('fecha_hasta') ? Carbon::parse($request->fecha_hasta) : Carbon::today();
                return [
                    $desde,
                    $hasta,
                    'Del ' . $desde->format('d/m/Y') . ' al ' . $hasta->format('d/m/Y')
                ];
            case 'hoy':
            default:
                return [
                    Carbon::today(),
                    Carbon::today(),
                    'Hoy (' . Carbon::today()->format('d/m/Y') . ')'
                ];
        }
    }

    private function obtenerDatosCierreMes(string $mes): array
    {
        $fechaInicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        $fechaFin    = Carbon::createFromFormat('Y-m', $mes)->endOfMonth();

        $productos = Producto::with(['categoria', 'marca', 'unidad'])->orderBy('id')->get();

        $ventasPorProducto = DB::table('detalle_ventas')
            ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id')
            ->where('ventas.estado', '!=', 'Anulada')
            ->whereBetween('ventas.fecha', [$fechaInicio, $fechaFin])
            ->groupBy('detalle_ventas.id_producto')
            ->select('detalle_ventas.id_producto', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'))
            ->pluck('total_vendido', 'detalle_ventas.id_producto');

        $checks = CierreMesCheck::where('mes', $mes)->pluck('verificado', 'id_producto');

        $filas = [];
        $totalCantidadAnterior = 0;
        $totalVentas = 0;
        $totalEnTienda = 0;
        $verificadosCount = 0;

        foreach ($productos as $p) {
            $ventasMes = (int) ($ventasPorProducto[$p->id] ?? 0);
            $totalTienda = (int) $p->stock;
            $cantidadAnterior = $totalTienda + $ventasMes;

            $verificado = (bool) ($checks[$p->id] ?? false);
            if ($verificado) $verificadosCount++;

            $totalCantidadAnterior += $cantidadAnterior;
            $totalVentas += $ventasMes;
            $totalEnTienda += $totalTienda;

            $filas[] = [
                'producto'          => $p,
                'id'                => $p->id,
                'codigo'            => $p->codigo,
                'nombre'            => $p->nombre,
                'cantidad_anterior' => $cantidadAnterior,
                'ventas'            => $ventasMes,
                'venta'             => $ventasMes,
                'total_tienda'      => $totalTienda,
                'verificado'        => $verificado,
            ];
        }

        $totalProductos = count($productos);
        $porcentaje = $totalProductos > 0 ? round(($verificadosCount / $totalProductos) * 100) : 0;
        $pendientesCount = max(0, $totalProductos - $verificadosCount);

        return [
            'items'                 => $filas,
            'filas'                 => $filas,
            'totalProductos'        => $totalProductos,
            'verificadosCount'      => $verificadosCount,
            'pendientesCount'       => $pendientesCount,
            'porcentaje'            => $porcentaje,
            'porcentajeVerificado'  => $porcentaje,
            'totalCantidadAnterior' => $totalCantidadAnterior,
            'totalStockAnterior'    => $totalCantidadAnterior,
            'totalVentas'           => $totalVentas,
            'totalVendidos'         => $totalVentas,
            'totalEnTienda'         => $totalEnTienda,
            'totalStockTienda'      => $totalEnTienda,
            'inicioMes'             => $fechaInicio,
            'finMes'                => $fechaFin,
        ];
    }
}
