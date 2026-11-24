<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\User;
use App\Models\Producto;
use App\Models\CierreMesCheck;
use App\Models\CierreMes;
use App\Models\Marca;
use App\Models\Categoria;
use App\Models\Gasto;
use App\Models\MovimientoCaja;
use App\Models\ArqueoCaja;
use App\Models\CajaChicaMovimiento;
use App\Exports\VentasExport;
use App\Exports\CierreMesExport;
use App\Exports\ProductosExport;
use App\Exports\FinanzasExport;
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

        $gastosMes = (float) Gasto::whereBetween('fecha', [$inicioMes, $finMes])->sum('monto');
        $retirosBancoMes = (float) MovimientoCaja::where('tipo', 'egreso_retiro')
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->where(function ($q) {
                $q->whereNull('motivo')
                  ->orWhere('motivo', 'NOT LIKE', 'Traslado a Caja Chica:%');
            })
            ->sum('monto');

        return view('reportes.hub', compact(
            'ventasMes',
            'totalRecaudadoMes',
            'totalProductos',
            'checksMes',
            'porcentajeCierre',
            'mesActual',
            'gastosMes',
            'retirosBancoMes'
        ));
    }

    // ============================================================
    // 2. REPORTE DE VENTAS DETALLADO (Efectivo / QR / Por Pagar)
    // ============================================================
    public function ventas(Request $request)
    {
        $periodo = $request->input('periodo', 'hoy');
        [$fechaDesde, $fechaHasta, $periodoLabel] = $this->getRango($request, $periodo);

        $query = Venta::with(['usuario', 'detalles.producto', 'pagos'])
            ->where('estado', '!=', 'Anulada')
            ->whereBetween('fecha', [$fechaDesde->startOfDay(), $fechaHasta->copy()->endOfDay()]);

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('cajero')) {
            $query->where('id_usuario', $request->cajero);
        }

        if ($request->filled('buscar')) {
            $palabras = array_filter(explode(' ', trim($request->buscar)));
            $query->where(function ($q) use ($palabras) {
                foreach ($palabras as $p) {
                    $q->where(function($sub) use ($p) {
                        $sub->where('cliente', 'LIKE', "%{$p}%")
                            ->orWhere('numero_nota', 'LIKE', "%{$p}%");
                    });
                }
            });
        }

        $ventas = $query->orderBy('fecha', 'desc')->get();

        $totales = $this->calcularTotalesVentas($ventas);

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

        $query = Venta::with(['usuario', 'detalles.producto', 'pagos'])
            ->where('estado', '!=', 'Anulada')
            ->whereBetween('fecha', [$fechaDesde->startOfDay(), $fechaHasta->copy()->endOfDay()]);

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('cajero')) {
            $query->where('id_usuario', $request->cajero);
        }

        if ($request->filled('buscar')) {
            $palabras = array_filter(explode(' ', trim($request->buscar)));
            $query->where(function ($q) use ($palabras) {
                foreach ($palabras as $p) {
                    $q->where(function($sub) use ($p) {
                        $sub->where('cliente', 'LIKE', "%{$p}%")
                            ->orWhere('numero_nota', 'LIKE', "%{$p}%");
                    });
                }
            });
        }

        $ventas = $query->orderBy('fecha', 'desc')->get();

        $totales = $this->calcularTotalesVentas($ventas);

        $filtros = [
            'periodo_label' => $periodoLabel,
            'metodo_pago'   => $request->metodo_pago,
        ];

        $nombreArchivo = 'Reporte_Ventas_' . $fechaDesde->format('Ymd') . '_' . $fechaHasta->format('Ymd') . '.xlsx';

        return Excel::download(new VentasExport($ventas, $filtros, $totales), $nombreArchivo);
    }

    /**
     * Calcula los totales discriminados de ventas con precisión contable de cobros reales.
     */
    private function calcularTotalesVentas($ventas): array
    {
        $totalEfectivo = 0;
        $totalQr = 0;
        $totalPorPagarPendiente = 0;

        $efectivoDirecto = 0;
        $efectivoCredito = 0;
        $qrDirecto = 0;
        $qrCredito = 0;

        $cantEfectivoDirecto = 0;
        $cantEfectivoCredito = 0;
        $cantQrDirecto = 0;
        $cantQrCredito = 0;

        $ventasConEfectivoCount = 0;
        $ventasConQrCount = 0;
        $ventasConDeudaCount = 0;

        foreach ($ventas as $v) {
            $cobrado = $v->monto_pagado !== null ? floatval($v->monto_pagado) : floatval($v->total);
            $pendiente = floatval($v->saldo_pendiente ?? 0);
            $totalPorPagarPendiente += $pendiente;

            $efectivoVenta = 0;
            $qrVenta = 0;

            if ($v->metodo_pago === 'Efectivo') {
                $efectivoVenta = $cobrado;
                $efectivoDirecto += $cobrado;
                $cantEfectivoDirecto++;
            } elseif ($v->metodo_pago === 'QR') {
                $qrVenta = $cobrado;
                $qrDirecto += $cobrado;
                $cantQrDirecto++;
            } elseif ($v->metodo_pago === 'Por Pagar') {
                // Revisar si tiene cobros en pagos_ventas
                $pagos = $v->relationLoaded('pagos') ? $v->pagos : $v->pagos()->get();
                $pagadoEnPagos = 0;

                if ($pagos->isNotEmpty()) {
                    foreach ($pagos as $p) {
                        $montoPago = floatval($p->monto);
                        $pagadoEnPagos += $montoPago;
                        if (strtolower($p->metodo_pago) === 'qr') {
                            $qrVenta += $montoPago;
                        } else {
                            $efectivoVenta += $montoPago;
                        }
                    }
                }

                // Si cobrado excede lo registrado en pagos_ventas (ej: adelanto inicial en el POS)
                $diferencia = $cobrado - $pagadoEnPagos;
                if ($diferencia > 0.009) {
                    if (strtolower($v->metodo_adelanto ?? '') === 'qr') {
                        $qrVenta += $diferencia;
                    } else {
                        $efectivoVenta += $diferencia;
                    }
                }

                if ($efectivoVenta > 0) {
                    $efectivoCredito += $efectivoVenta;
                    $cantEfectivoCredito++;
                }
                if ($qrVenta > 0) {
                    $qrCredito += $qrVenta;
                    $cantQrCredito++;
                }
            }

            $totalEfectivo += $efectivoVenta;
            $totalQr += $qrVenta;

            if ($efectivoVenta > 0) {
                $ventasConEfectivoCount++;
            }
            if ($qrVenta > 0) {
                $ventasConQrCount++;
            }
            if ($pendiente > 0) {
                $ventasConDeudaCount++;
            }
        }

        return [
            'efectivo'                 => $totalEfectivo,
            'qr'                       => $totalQr,
            'por_pagar'                => $totalPorPagarPendiente,
            'general'                  => $ventas->sum('total'),
            'recaudado'                => $totalEfectivo + $totalQr,
            'cantidad'                 => $ventas->count(),
            'productos'                => $ventas->flatMap->detalles->sum('cantidad'),
            'con_efectivo'             => $ventasConEfectivoCount,
            'con_qr'                   => $ventasConQrCount,
            'por_pagar_con_deuda'      => $ventasConDeudaCount,
            'efectivo_directo'         => $efectivoDirecto,
            'efectivo_credito'         => $efectivoCredito,
            'cant_efectivo_directo'    => $cantEfectivoDirecto,
            'cant_efectivo_credito'    => $cantEfectivoCredito,
            'qr_directo'               => $qrDirecto,
            'qr_credito'               => $qrCredito,
            'cant_qr_directo'          => $cantQrDirecto,
            'cant_qr_credito'          => $cantQrCredito,
        ];
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
                'total_productos'         => $datos['totalProductos'],
                'total_ventas'            => $datos['totalVentas'],
                'total_stock'             => $datos['totalEnTienda'],
                'verificados_count'       => $datos['verificadosCount'],
                'porcentaje'              => $datos['porcentaje'],
                'total_ingresos_ventas'   => $datos['ingresosVentas'],
                'total_costo_ventas'      => $datos['costoVentas'],
                'total_gastos_operativos' => $datos['gastosOperativos'],
                'utilidad_neta'           => $datos['utilidadNeta'],
                'id_usuario'              => Auth::id() ?? 1,
                'fecha_cierre'            => now(),
                'notas'                   => $request->notas,
                'snapshot_items'          => $snapshot,
                'estado'                  => 'Cerrado',
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
        $userId = Auth::id();
        $now    = now();

        if ($marcar) {
            $productIds = Producto::pluck('id');
            $rows = [];
            foreach ($productIds as $id) {
                $rows[] = [
                    'mes'                => $mes,
                    'id_producto'        => $id,
                    'verificado'         => true,
                    'id_usuario'         => $userId,
                    'fecha_verificacion' => $now,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                CierreMesCheck::upsert(
                    $chunk,
                    ['mes', 'id_producto'],
                    ['verificado', 'id_usuario', 'fecha_verificacion', 'updated_at']
                );
            }

            $total       = count($productIds);
            $verificados = $total;
            $porcentaje  = 100;
        } else {
            CierreMesCheck::where('mes', $mes)->update([
                'verificado'         => false,
                'id_usuario'         => $userId,
                'fecha_verificacion' => null,
                'updated_at'         => $now,
            ]);

            $total       = Producto::count();
            $verificados = 0;
            $porcentaje  = 0;
        }

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

        $productos = Producto::with(['categoria', 'marca', 'unidad'])->orderBy('nombre', 'asc')->get();

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

        // Métricas financieras del período (Estado de Resultados)
        $cierreOficial = CierreMes::where('mes', $mes)->where('estado', 'Cerrado')->first();

        if ($cierreOficial && floatval($cierreOficial->total_ingresos_ventas) > 0) {
            $ingresosVentas   = floatval($cierreOficial->total_ingresos_ventas);
            $costoVentas      = floatval($cierreOficial->total_costo_ventas);
            $gastosOperativos = floatval($cierreOficial->total_gastos_operativos);
            $utilidadBruta    = round($ingresosVentas - $costoVentas, 2);
            $utilidadNeta     = floatval($cierreOficial->utilidad_neta);
        } else {
            $ingresosVentas = (float) Venta::where('estado', '!=', 'Anulada')
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->sum('total');

            $costoReal = (float) DB::table('detalle_ventas')
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id')
                ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id')
                ->where('ventas.estado', '!=', 'Anulada')
                ->whereBetween('ventas.fecha', [$fechaInicio, $fechaFin])
                ->sum(DB::raw('detalle_ventas.cantidad * COALESCE(productos.costo, 0)'));

            $costoVentas = ($costoReal > 0 && $costoReal != $ingresosVentas)
                ? round($costoReal, 2)
                : round($ingresosVentas * 0.65, 2);

            $gastosOperativos = (float) Gasto::whereBetween('fecha', [$fechaInicio, $fechaFin])->sum('monto');
            $utilidadBruta    = round($ingresosVentas - $costoVentas, 2);
            $utilidadNeta     = round($utilidadBruta - $gastosOperativos, 2);
        }

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
            'ingresosVentas'        => $ingresosVentas,
            'costoVentas'           => $costoVentas,
            'gastosOperativos'      => $gastosOperativos,
            'utilidadBruta'         => $utilidadBruta,
            'utilidadNeta'          => $utilidadNeta,
            'inicioMes'             => $fechaInicio,
            'finMes'                => $fechaFin,
        ];
    }

    // ============================================================
    // 4. REPORTE GENERAL DE PRODUCTOS Y STOCK FÍSICO
    // ============================================================
    public function productos(Request $request)
    {
        $query = Producto::with(['marca', 'unidad', 'unidadSecundaria', 'categoria']);

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $palabras = array_filter(explode(' ', $buscar));
            $query->where(function($q) use ($palabras) {
                foreach ($palabras as $p) {
                    $q->where(function($sub) use ($p) {
                        $sub->where('nombre', 'LIKE', "%{$p}%")
                            ->orWhere('codigo', 'LIKE', "%{$p}%")
                            ->orWhere('descripcion', 'LIKE', "%{$p}%")
                            ->orWhereHas('marca', function($qm) use ($p) {
                                $qm->where('nombre', 'LIKE', "%{$p}%");
                            });
                    });
                }
            });
        }

        if ($request->filled('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }

        if ($request->filled('marca')) {
            $query->where('id_marca', $request->marca);
        }

        if ($request->filled('estado_stock')) {
            $estado = $request->estado_stock;
            if ($estado === 'con_stock') {
                $query->where('stock', '>', 0);
            } elseif ($estado === 'agotado') {
                $query->where('stock', '<=', 0);
            } elseif ($estado === 'critico') {
                $query->whereRaw('stock <= IF(stock_minimo > 0, stock_minimo, 5)');
            } elseif ($estado === 'alerta') {
                $query->whereRaw('stock > IF(stock_minimo > 0, stock_minimo, 5) AND stock <= IF(stock_minimo > 0, stock_minimo * 2, 10)');
            } elseif ($estado === 'optimo') {
                $query->whereRaw('stock > IF(stock_minimo > 0, stock_minimo * 2, 10)');
            }
        }

        $productos = $query->orderBy('nombre', 'asc')->get();

        $totalProductos = $productos->count();
        $totalStock     = $productos->sum('stock');
        $totalValor     = $productos->sum(function($p) { return ($p->stock ?? 0) * ($p->precio_venta ?? 0); });
        $totalCosto     = $productos->sum(function($p) { return ($p->stock ?? 0) * ($p->costo ?? 0); });
        $totalCriticos  = $productos->filter(function($p) {
            $min = is_numeric($p->stock_minimo) && $p->stock_minimo > 0 ? (int)$p->stock_minimo : 5;
            return ($p->stock ?? 0) <= $min;
        })->count();
        $totalAgotados  = $productos->filter(function($p) { return ($p->stock ?? 0) <= 0; })->count();

        $categorias = Categoria::orderBy('nombre')->get();
        $marcas     = Marca::orderBy('nombre')->get();

        return view('reportes.productos', compact(
            'productos', 'totalProductos', 'totalStock', 'totalValor', 'totalCosto',
            'totalCriticos', 'totalAgotados', 'categorias', 'marcas'
        ));
    }

    public function exportarProductosExcel(Request $request)
    {
        $query = Producto::with(['marca', 'unidad', 'unidadSecundaria', 'categoria']);

        $filtrosDesc = [];
        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $palabras = array_filter(explode(' ', $buscar));
            $query->where(function($q) use ($palabras) {
                foreach ($palabras as $p) {
                    $q->where(function($sub) use ($p) {
                        $sub->where('nombre', 'LIKE', "%{$p}%")
                            ->orWhere('codigo', 'LIKE', "%{$p}%")
                            ->orWhereHas('marca', function($qm) use ($p) {
                                $qm->where('nombre', 'LIKE', "%{$p}%");
                            });
                    });
                }
            });
            $filtrosDesc[] = "Búsqueda: '{$buscar}'";
        }

        if ($request->filled('categoria')) {
            $query->where('id_categoria', $request->categoria);
            $cat = Categoria::find($request->categoria);
            if ($cat) $filtrosDesc[] = "Categoría: {$cat->nombre}";
        }

        if ($request->filled('marca')) {
            $query->where('id_marca', $request->marca);
            $m = Marca::find($request->marca);
            if ($m) $filtrosDesc[] = "Marca: {$m->nombre}";
        }

        if ($request->filled('estado_stock')) {
            $estado = $request->estado_stock;
            if ($estado === 'con_stock') {
                $query->where('stock', '>', 0);
                $filtrosDesc[] = "Solo con Stock (>0)";
            } elseif ($estado === 'agotado') {
                $query->where('stock', '<=', 0);
                $filtrosDesc[] = "Solo Agotados (0)";
            } elseif ($estado === 'critico') {
                $query->whereRaw('stock <= IF(stock_minimo > 0, stock_minimo, 5)');
                $filtrosDesc[] = "Solo Críticos (≤ Mínimo)";
            } elseif ($estado === 'alerta') {
                $query->whereRaw('stock > IF(stock_minimo > 0, stock_minimo, 5) AND stock <= IF(stock_minimo > 0, stock_minimo * 2, 10)');
                $filtrosDesc[] = "Por Agotarse";
            } elseif ($estado === 'optimo') {
                $query->whereRaw('stock > IF(stock_minimo > 0, stock_minimo * 2, 10)');
                $filtrosDesc[] = "Stock Óptimo";
            }
        }

        $productos = $query->orderBy('nombre', 'asc')->get();

        $totales = [
            'total_productos' => $productos->count(),
            'total_stock'     => $productos->sum('stock'),
            'total_valor'     => $productos->sum(function($p) { return ($p->stock ?? 0) * ($p->precio_venta ?? 0); }),
            'total_costo'     => $productos->sum(function($p) { return ($p->stock ?? 0) * ($p->costo ?? 0); }),
        ];

        $filtros = [
            'descripcion' => !empty($filtrosDesc) ? implode(' | ', $filtrosDesc) : 'Todo el Catálogo Completo'
        ];

        $nombreArchivo = 'Reporte_Inventario_Productos_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new ProductosExport($productos, $totales, $filtros), $nombreArchivo);
    }

    // ============================================================
    // 5. REPORTE DE CAJA Y FINANZAS (Rentabilidad, Torta de Gastos y Retiros a Banco)
    // ============================================================
    public function finanzas(Request $request)
    {
        $datos = $this->obtenerDatosFinanzas($request);
        return view('reportes.finanzas', $datos);
    }

    public function exportarFinanzasExcel(Request $request)
    {
        $datos = $this->obtenerDatosFinanzas($request);
        $nombreArchivo = 'Reporte_Caja_Finanzas_' . $datos['fechaDesde']->format('Ymd') . '_' . $datos['fechaHasta']->format('Ymd') . '.xlsx';
        return Excel::download(new FinanzasExport($datos), $nombreArchivo);
    }

    private function obtenerDatosFinanzas(Request $request): array
    {
        $periodo = $request->input('periodo', 'mes');
        [$fechaDesde, $fechaHasta, $periodoLabel] = $this->getRango($request, $periodo);

        $inicio = $fechaDesde->copy()->startOfDay();
        $fin    = $fechaHasta->copy()->endOfDay();

        // 1. Ingresos Reales del Período (sin costos CMV ficticios mientras los productos tengan costo 0)
        $ventasPeriodo = Venta::with(['pagos'])
            ->where('estado', '!=', 'Anulada')
            ->whereBetween('fecha', [$inicio, $fin])
            ->get();

        $totalesVentas    = $this->calcularTotalesVentas($ventasPeriodo);
        $ventasBrutas     = round((float) $totalesVentas['general'], 2);
        $conteoVentas     = (int) $totalesVentas['cantidad'];
        $ingresosEfectivo = round((float) $totalesVentas['efectivo'], 2);
        $ingresosQr       = round((float) $totalesVentas['qr'], 2);
        $ingresosCobrados = round((float) $totalesVentas['recaudado'], 2);
        $saldoPorCobrar   = round((float) $totalesVentas['por_pagar'], 2);

        // 2. Gastos Operativos del Período + Agrupación por Categoría (Top / Torta)
        $gastos = Gasto::with(['categoria', 'usuario', 'turnoCaja'])
            ->whereBetween('fecha', [$inicio, $fin])
            ->orderByDesc('fecha')
            ->get();

        $totalGastos  = round((float) $gastos->sum('monto'), 2);
        $conteoGastos = $gastos->count();

        $flujoNetoOperativo = round($ingresosCobrados - $totalGastos, 2);
        $pctLibreGastos     = $ingresosCobrados > 0 ? round(($flujoNetoOperativo / $ingresosCobrados) * 100, 1) : 0;

        $gastosPorCategoria = Gasto::join('categorias_gasto', 'gastos.categoria_gasto_id', '=', 'categorias_gasto.id')
            ->whereBetween('gastos.fecha', [$inicio, $fin])
            ->select(
                'categorias_gasto.id',
                'categorias_gasto.nombre',
                'categorias_gasto.icono',
                'categorias_gasto.color',
                DB::raw('SUM(gastos.monto) as total_monto'),
                DB::raw('COUNT(gastos.id) as total_cantidad')
            )
            ->groupBy('categorias_gasto.id', 'categorias_gasto.nombre', 'categorias_gasto.icono', 'categorias_gasto.color')
            ->orderByDesc('total_monto')
            ->get();

        // Asignar paleta vibrante y distinta por porción (evitando grises idénticos)
        $paletaVibrante = ['#2563eb', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#14b8a6'];
        $coloresUsados = [];
        foreach ($gastosPorCategoria as $idx => $cat) {
            $colorBase = strtolower(trim((string) $cat->color));
            $esGris = in_array($colorBase, ['', '#64748b', '#6b7280', '#94a3b8', '#475569']);
            if ($esGris || in_array($colorBase, $coloresUsados)) {
                $colorAsignado = $paletaVibrante[$idx % count($paletaVibrante)];
                foreach ($paletaVibrante as $pColor) {
                    if (!in_array($pColor, $coloresUsados)) {
                        $colorAsignado = $pColor;
                        break;
                    }
                }
            } else {
                $colorAsignado = $colorBase;
            }
            $coloresUsados[] = $colorAsignado;
            $cat->chart_color = $colorAsignado;
        }

        $topCategoriaGasto = $gastosPorCategoria->first();
        $chartLabels       = $gastosPorCategoria->pluck('nombre')->toArray();
        $chartMontos       = $gastosPorCategoria->pluck('total_monto')->map(fn($v) => round(floatval($v), 2))->toArray();
        $chartColores      = $gastosPorCategoria->pluck('chart_color')->toArray();

        $topGastosIndividuales = $gastos->sortByDesc('monto')->take(5)->values();

        // Desglose de Gastos por Origen de Fondos
        $gastosDesdeGaveta    = round((float) $gastos->where('origen_pago', 'gaveta')->sum('monto'), 2);
        $gastosDesdeCajaChica = round((float) $gastos->where('origen_pago', 'caja_chica')->sum('monto'), 2);
        $gastosDesdeBancoQr   = round((float) $gastos->where('origen_pago', 'banco_qr')->sum('monto'), 2);
        $gastosDesdeGerencia  = round((float) $gastos->where('origen_pago', 'gerencia')->sum('monto'), 2);
        $gastosOtrasFuentes   = round($totalGastos - $gastosDesdeGaveta, 2);

        // 3. Métrica de Salidas de Gaveta y Retiros / Depósitos al Banco
        $retirosTodos = MovimientoCaja::with(['usuario', 'arqueo'])
            ->where('tipo', 'egreso_retiro')
            ->whereBetween('fecha', [$inicio, $fin])
            ->orderByDesc('fecha')
            ->get();

        $retirosBanco = $retirosTodos->filter(function ($m) {
            return !str_starts_with((string) ($m->motivo ?? ''), 'Traslado a Caja Chica:');
        })->values();

        $trasladosCajaChica = $retirosTodos->filter(function ($m) {
            return str_starts_with((string) ($m->motivo ?? ''), 'Traslado a Caja Chica:');
        })->values();

        $totalRetirosBanco       = round((float) $retirosBanco->sum('monto'), 2);
        $conteoRetirosBanco      = $retirosBanco->count();
        $promedioRetiroBanco     = $conteoRetirosBanco > 0 ? round($totalRetirosBanco / $conteoRetirosBanco, 2) : 0;
        $mayorRetiroBanco        = $retirosBanco->sortByDesc('monto')->first();
        $totalTrasladosCajaChica = round((float) $trasladosCajaChica->sum('monto'), 2);

        $retirosBancoPorMetodo = [
            'efectivo' => round((float) $retirosBanco->where('metodo_pago', 'Efectivo')->sum('monto'), 2),
            'qr'       => round((float) $retirosBanco->where('metodo_pago', '!=', 'Efectivo')->sum('monto'), 2),
        ];

        $totalSalidoGaveta    = round($totalRetirosBanco + $totalTrasladosCajaChica + $gastosDesdeGaveta, 2);
        $saldoCajaChicaActual = round((float) CajaChicaMovimiento::saldoActual(), 2);

        // 4. Turnos de Gaveta del Período (para auditoría rápida de cierres)
        $turnosPeriodo = ArqueoCaja::with(['usuario', 'usuarioCierre'])
            ->whereBetween('fecha_apertura', [$inicio, $fin])
            ->orderByDesc('fecha_apertura')
            ->get();

        $turnosCerradosCount  = $turnosPeriodo->where('estado', 'cerrado')->count();
        $totalContadoTurnos   = round((float) $turnosPeriodo->where('estado', 'cerrado')->sum('monto_fisico_declarado'), 2);
        $diferenciaNetaTurnos = round((float) $turnosPeriodo->where('estado', 'cerrado')->sum('diferencia'), 2);

        return compact(
            'periodo',
            'periodoLabel',
            'fechaDesde',
            'fechaHasta',
            'ventasBrutas',
            'conteoVentas',
            'ingresosEfectivo',
            'ingresosQr',
            'ingresosCobrados',
            'saldoPorCobrar',
            'totalGastos',
            'conteoGastos',
            'flujoNetoOperativo',
            'pctLibreGastos',
            'gastos',
            'gastosPorCategoria',
            'topCategoriaGasto',
            'topGastosIndividuales',
            'chartLabels',
            'chartMontos',
            'chartColores',
            'gastosDesdeGaveta',
            'gastosDesdeCajaChica',
            'gastosDesdeBancoQr',
            'gastosDesdeGerencia',
            'gastosOtrasFuentes',
            'retirosBanco',
            'trasladosCajaChica',
            'totalRetirosBanco',
            'conteoRetirosBanco',
            'promedioRetiroBanco',
            'mayorRetiroBanco',
            'totalTrasladosCajaChica',
            'retirosBancoPorMetodo',
            'totalSalidoGaveta',
            'saldoCajaChicaActual',
            'turnosPeriodo',
            'turnosCerradosCount',
            'totalContadoTurnos',
            'diferenciaNetaTurnos'
        );
    }
}
