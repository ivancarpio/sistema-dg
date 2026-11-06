<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\User;
use App\Models\Producto;
use App\Models\CierreMesCheck;
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
        // Estadísticas rápidas para las tarjetas
        $mesActual = Carbon::now()->format('Y-m');
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes    = Carbon::now()->endOfMonth();

        $ventasMes = Venta::where('estado', '!=', 'Anulada')
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->count();

        $totalRecaudadoMes = Venta::where('estado', '!=', 'Anulada')
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->sum('total');

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
    // 2. REPORTE DE VENTAS DETALLADO (Efectivo / QR / Desglose)
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

        $totales = [
            'efectivo'  => $ventas->where('metodo_pago', 'Efectivo')->sum('total'),
            'qr'        => $ventas->where('metodo_pago', 'QR')->sum('total'),
            'general'   => $ventas->sum('total'),
            'cantidad'  => $ventas->count(),
            'productos' => $ventas->flatMap->detalles->sum('cantidad'),
        ];

        $usuarios = User::all();

        return view('reportes.index', compact(
            'ventas', 'totales', 'usuarios',
            'periodo', 'periodoLabel',
            'fechaDesde', 'fechaHasta'
        ));
    }

    // ============================================================
    // 3. EXPORTAR REPORTE DE VENTAS A EXCEL
    // ============================================================
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

        $totales = [
            'efectivo' => $ventas->where('metodo_pago', 'Efectivo')->sum('total'),
            'qr'       => $ventas->where('metodo_pago', 'QR')->sum('total'),
            'general'  => $ventas->sum('total'),
        ];

        $filtros = [
            'periodo_label' => $periodoLabel,
            'metodo_pago'   => $request->metodo_pago,
        ];

        $nombreArchivo = 'Reporte_Ventas_' . now()->format('d-m-Y') . '.xlsx';

        return Excel::download(
            new VentasExport($ventas, $filtros, $totales),
            $nombreArchivo
        );
    }

    // ============================================================
    // 4. REPORTE DE CIERRE DE MES (CUADRE DE STOCK FÍSICO)
    // ============================================================
    public function cierreMes(Request $request)
    {
        // Mes seleccionado (por defecto mes actual 'YYYY-MM')
        $mes = $request->input('mes', Carbon::now()->format('Y-m'));

        $dataCierre = $this->obtenerDatosCierreMes($mes);

        return view('reportes.cierre_mes', array_merge($dataCierre, [
            'mesSeleccionado' => $mes,
        ]));
    }

    // ============================================================
    // 5. TOGGLE CHECK INTERACTIVO (AJAX)
    // ============================================================
    public function toggleCheckCierreMes(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|integer|exists:productos,id',
            'mes'         => 'required|string',
        ]);

        $idProducto = $request->id_producto;
        $mes        = $request->mes;

        $check = CierreMesCheck::where('mes', $mes)
            ->where('id_producto', $idProducto)
            ->first();

        if ($check) {
            $check->verificado = !$check->verificado;
            $check->fecha_verificacion = $check->verificado ? now() : null;
            $check->id_usuario = Auth::id();
            $check->save();
            $estado = $check->verificado;
        } else {
            CierreMesCheck::create([
                'mes'                => $mes,
                'id_producto'        => $idProducto,
                'verificado'         => true,
                'id_usuario'         => Auth::id(),
                'fecha_verificacion' => now(),
            ]);
            $estado = true;
        }

        // Totales actualizados
        $totalProductos = Producto::count();
        $totalChecks    = CierreMesCheck::where('mes', $mes)->where('verificado', true)->count();
        $porcentaje     = $totalProductos > 0 ? round(($totalChecks / $totalProductos) * 100) : 0;

        return response()->json([
            'success'     => true,
            'verificado'  => $estado,
            'totalChecks' => $totalChecks,
            'pendientes'  => max(0, $totalProductos - $totalChecks),
            'porcentaje'  => $porcentaje,
        ]);
    }

    // ============================================================
    // 6. MARCAR / DESMARCAR TODOS (AJAX)
    // ============================================================
    public function checkAllCierreMes(Request $request)
    {
        $request->validate([
            'mes'    => 'required|string',
            'accion' => 'required|in:marcar,desmarcar',
        ]);

        $mes    = $request->mes;
        $accion = $request->accion;

        $productos = Producto::pluck('id');

        if ($accion === 'marcar') {
            foreach ($productos as $idProd) {
                CierreMesCheck::updateOrCreate(
                    ['mes' => $mes, 'id_producto' => $idProd],
                    [
                        'verificado'         => true,
                        'id_usuario'         => Auth::id(),
                        'fecha_verificacion' => now(),
                    ]
                );
            }
        } else {
            CierreMesCheck::where('mes', $mes)->update([
                'verificado' => false,
                'fecha_verificacion' => null,
            ]);
        }

        $totalProductos = $productos->count();
        $totalChecks    = CierreMesCheck::where('mes', $mes)->where('verificado', true)->count();
        $porcentaje     = $totalProductos > 0 ? round(($totalChecks / $totalProductos) * 100) : 0;

        return response()->json([
            'success'     => true,
            'totalChecks' => $totalChecks,
            'pendientes'  => max(0, $totalProductos - $totalChecks),
            'porcentaje'  => $porcentaje,
        ]);
    }

    // ============================================================
    // 7. EXPORTAR CIERRE DE MES A EXCEL
    // ============================================================
    public function exportarCierreMesExcel(Request $request)
    {
        $mes = $request->input('mes', Carbon::now()->format('Y-m'));
        $dataCierre = $this->obtenerDatosCierreMes($mes);

        $carbonFecha = Carbon::parse($mes . '-01');
        $anio        = $carbonFecha->format('Y');
        $nombreMes   = $carbonFecha->translatedFormat('F');

        $meta = [
            'empresa'       => $request->input('empresa', 'KRONA DISTRIBUIDORA GUADALUPE'),
            'anio'          => $anio,
            'gerente'       => $request->input('gerente', 'LUIS ANTENOR CALDERON RODRIGUEZ'),
            'encargado'     => $request->input('encargado', 'AJAEL'),
            'encargada_inv' => $request->input('encargada_inv', 'CIELO MADELEN CALDERON POMA'),
        ];

        $nombreArchivo = 'Cierre_Mes_' . ucfirst($nombreMes) . '_' . $anio . '.xlsx';

        return Excel::download(
            new CierreMesExport($dataCierre['items'], $meta),
            $nombreArchivo
        );
    }

    // ============================================================
    // HELPER: Cálculo centralizado de datos de Cierre de Mes
    // ============================================================
    private function obtenerDatosCierreMes(string $mes): array
    {
        $inicioMes = Carbon::parse($mes . '-01')->startOfMonth();
        $finMes    = Carbon::parse($mes . '-01')->endOfMonth();

        // 1. Obtener suma de ventas por producto en ese mes
        $ventasPorProducto = DB::table('detalle_ventas')
            ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id')
            ->where('ventas.estado', '!=', 'Anulada')
            ->whereBetween('ventas.fecha', [$inicioMes, $finMes])
            ->groupBy('detalle_ventas.id_producto')
            ->select('detalle_ventas.id_producto', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'))
            ->pluck('total_vendido', 'id_producto');

        // 2. Obtener checks de verificación guardados para este mes
        $checks = CierreMesCheck::where('mes', $mes)
            ->where('verificado', true)
            ->pluck('verificado', 'id_producto');

        // 3. Obtener todos los productos
        $productos = Producto::orderBy('id', 'asc')->get();

        $items = [];
        $totalVendidos = 0;
        $totalStockTienda = 0;
        $totalStockAnterior = 0;
        $verificadosCount = 0;

        foreach ($productos as $prod) {
            $venta = (int) ($ventasPorProducto[$prod->id] ?? 0);
            $totalTienda = (int) $prod->stock;
            // La fórmula solicitada: CANTIDAD (anterior) - VENTA = TOTAL EN TIENDA
            // Por tanto: CANTIDAD = TOTAL EN TIENDA + VENTA
            $cantidadAnterior = $totalTienda + $venta;
            $estaVerificado = isset($checks[$prod->id]);

            if ($estaVerificado) {
                $verificadosCount++;
            }

            $totalVendidos      += $venta;
            $totalStockTienda   += $totalTienda;
            $totalStockAnterior += $cantidadAnterior;

            $items[] = [
                'id'                => $prod->id,
                'codigo'            => $prod->codigo ?? (string)$prod->id,
                'nombre'            => $prod->nombre,
                'cantidad_anterior' => $cantidadAnterior,
                'venta'             => $venta,
                'total_tienda'      => $totalTienda,
                'verificado'        => $estaVerificado,
            ];
        }

        $totalProductos = count($items);
        $pendientesCount = max(0, $totalProductos - $verificadosCount);
        $porcentajeVerificado = $totalProductos > 0 ? round(($verificadosCount / $totalProductos) * 100) : 0;

        return [
            'items'                 => $items,
            'totalProductos'        => $totalProductos,
            'totalVendidos'         => $totalVendidos,
            'totalStockTienda'      => $totalStockTienda,
            'totalStockAnterior'    => $totalStockAnterior,
            'verificadosCount'      => $verificadosCount,
            'pendientesCount'       => $pendientesCount,
            'porcentajeVerificado'  => $porcentajeVerificado,
            'inicioMes'             => $inicioMes,
            'finMes'                => $finMes,
        ];
    }

    // ============================================================
    // HELPER: Calcular rango de fechas para reporte de ventas
    // ============================================================
    private function getRango(Request $request, string $periodo): array
    {
        switch ($periodo) {
            case 'semana':
                $desde = Carbon::now()->startOfWeek();
                $hasta = Carbon::now()->endOfWeek();
                $label = 'Esta Semana (' . $desde->format('d/m') . ' - ' . $hasta->format('d/m/Y') . ')';
                break;
            case 'mes':
                $desde = Carbon::now()->startOfMonth();
                $hasta = Carbon::now()->endOfMonth();
                $label = 'Este Mes (' . $desde->translatedFormat('F Y') . ')';
                break;
            case 'personalizado':
                $desde = $request->filled('fecha_desde')
                    ? Carbon::parse($request->fecha_desde)
                    : Carbon::now()->startOfMonth();
                $hasta = $request->filled('fecha_hasta')
                    ? Carbon::parse($request->fecha_hasta)
                    : Carbon::now();
                $label = $desde->format('d/m/Y') . ' al ' . $hasta->format('d/m/Y');
                break;
            case 'hoy':
            default:
                $desde = Carbon::today();
                $hasta = Carbon::today();
                $label = 'Hoy (' . Carbon::today()->format('d/m/Y') . ')';
                break;
        }

        return [$desde, $hasta, $label];
    }
}
