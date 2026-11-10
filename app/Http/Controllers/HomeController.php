<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // ============================================================
        // 1. FILTRO DE MES (Por defecto: mes actual, ej: '2026-11')
        // ============================================================
        $mesSeleccionado = $request->input('mes', Carbon::now()->format('Y-m'));
        
        try {
            $fechaMes = Carbon::parse($mesSeleccionado . '-01');
        } catch (\Throwable $e) {
            $mesSeleccionado = Carbon::now()->format('Y-m');
            $fechaMes = Carbon::parse($mesSeleccionado . '-01');
        }

        $inicioMes = $fechaMes->copy()->startOfMonth();
        $finMes    = $fechaMes->copy()->endOfMonth();
        $anio      = $fechaMes->format('Y');

        $mesesEspanol = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];
        $nombreMesActual = $mesesEspanol[$fechaMes->format('m')] ?? 'Mes';

        $esMesActual = ($mesSeleccionado === Carbon::now()->format('Y-m'));
        $mesAnterior = $fechaMes->copy()->subMonth()->format('Y-m');
        $mesSiguiente = $fechaMes->copy()->addMonth()->format('Y-m');

        // ============================================================
        // 2. MÉTRICAS CLAVE (KPIs REALES)
        // ============================================================
        // Ventas del mes seleccionado (solo ventas activas no anuladas)
        $ventasMes = Venta::where('estado', '!=', 'Anulada')
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->sum('total');

        $conteoVentasMes = Venta::where('estado', '!=', 'Anulada')
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->count();

        // Ventas del mes anterior para calcular variación %
        $inicioMesAnt = $fechaMes->copy()->subMonth()->startOfMonth();
        $finMesAnt    = $fechaMes->copy()->subMonth()->endOfMonth();
        $ventasMesAnt = Venta::where('estado', '!=', 'Anulada')
            ->whereBetween('fecha', [$inicioMesAnt, $finMesAnt])
            ->sum('total');

        $variacionVentas = 0;
        if ($ventasMesAnt > 0) {
            $variacionVentas = round((($ventasMes - $ventasMesAnt) / $ventasMesAnt) * 100, 1);
        }

        // Margen Estimado del mes:
        $gananciaReal = DB::table('detalle_ventas')
            ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id')
            ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id')
            ->where('ventas.estado', '!=', 'Anulada')
            ->whereBetween('ventas.fecha', [$inicioMes, $finMes])
            ->sum(DB::raw('detalle_ventas.subtotal - (detalle_ventas.cantidad * COALESCE(productos.costo, 0))'));

        // Si los costos están en 0 en catálogo, aplicamos margen comercial estimado 35%
        $margenEstimado = ($gananciaReal > 0 && floatval($gananciaReal) != floatval($ventasMes)) 
            ? $gananciaReal 
            : ($ventasMes * 0.35);

        // Ventas de hoy (si es mes actual) o promedio diario del mes seleccionado
        $ventasHoy = Venta::where('estado', '!=', 'Anulada')
            ->whereDate('fecha', Carbon::today())
            ->sum('total');

        $diasEnMes = $fechaMes->daysInMonth;
        $diasTranscurridos = $esMesActual ? max(1, intval(Carbon::now()->format('d'))) : $diasEnMes;
        $promedioDiario = $diasTranscurridos > 0 ? ($ventasMes / $diasTranscurridos) : 0;

        // Catálogo de productos
        $totalProductos = Producto::count();

        // Cuentas por cobrar / Créditos pendientes
        $totalPorCobrar = Venta::where('estado', '!=', 'Anulada')
            ->where('saldo_pendiente', '>', 0)
            ->sum('saldo_pendiente');
        $conteoPorCobrar = Venta::where('estado', '!=', 'Anulada')
            ->where('saldo_pendiente', '>', 0)
            ->count();

        // ============================================================
        // 3. GRÁFICO 1: VENTAS MENSUALES DEL AÑO (REALES DESDE BD)
        // ============================================================
        $mesesLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $ventasPorMesAnio = [];
        for ($m = 1; $m <= 12; $m++) {
            $totalM = Venta::where('estado', '!=', 'Anulada')
                ->whereYear('fecha', $anio)
                ->whereMonth('fecha', $m)
                ->sum('total');
            $ventasPorMesAnio[] = round(floatval($totalM), 2);
        }

        // ============================================================
        // 4. GRÁFICO 2: TOP PRODUCTOS VENDIDOS (TOP 5 Y TOP 10)
        // ============================================================
        $topProductos = DB::table('detalle_ventas')
            ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id')
            ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id')
            ->where('ventas.estado', '!=', 'Anulada')
            ->whereBetween('ventas.fecha', [$inicioMes, $finMes])
            ->select(
                'productos.id',
                'productos.nombre',
                'productos.codigo',
                DB::raw('SUM(detalle_ventas.cantidad) as total_cantidad'),
                DB::raw('SUM(detalle_ventas.subtotal) as total_monto')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.codigo')
            ->orderBy('total_cantidad', 'desc')
            ->limit(10)
            ->get();

        // Si en el mes filtrado no hay ventas registradas todavía, tomamos el catálogo general de ventas
        if ($topProductos->isEmpty()) {
            $topProductos = DB::table('detalle_ventas')
                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id')
                ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id')
                ->where('ventas.estado', '!=', 'Anulada')
                ->select(
                    'productos.id',
                    'productos.nombre',
                    'productos.codigo',
                    DB::raw('SUM(detalle_ventas.cantidad) as total_cantidad'),
                    DB::raw('SUM(detalle_ventas.subtotal) as total_monto')
                )
                ->groupBy('productos.id', 'productos.nombre', 'productos.codigo')
                ->orderBy('total_cantidad', 'desc')
                ->limit(10)
                ->get();
        }

        $top5Labels  = $topProductos->take(5)->pluck('nombre')->toArray();
        $top5Cant    = $topProductos->take(5)->pluck('total_cantidad')->map(fn($v) => floatval($v))->toArray();
        $top5Monto   = $topProductos->take(5)->pluck('total_monto')->map(fn($v) => floatval($v))->toArray();

        $top10Labels = $topProductos->pluck('nombre')->toArray();
        $top10Cant   = $topProductos->pluck('total_cantidad')->map(fn($v) => floatval($v))->toArray();
        $top10Monto  = $topProductos->pluck('total_monto')->map(fn($v) => floatval($v))->toArray();

        // ============================================================
        // 5. GRÁFICO 3: FLUJO DE VENTAS DIARIAS (FILTROS 7, 15, 30 DÍAS Y MES)
        // ============================================================
        $rangosDiarios = [
            '7'   => $this->obtenerVentasDias(7),
            '15'  => $this->obtenerVentasDias(15),
            '30'  => $this->obtenerVentasDias(30),
            'mes' => $this->obtenerVentasMesCompleto($fechaMes),
        ];

        // ============================================================
        // 5. TOP 10 CLIENTES MÁS REGULARES (EMBUDO DE VENTAS)
        // ============================================================
        $topClientes = DB::table('ventas')
            ->leftJoin('clientes', 'ventas.id_cliente', '=', 'clientes.id')
            ->where('ventas.estado', '!=', 'Anulada')
            ->whereBetween('ventas.fecha', [$inicioMes, $finMes])
            ->whereNotNull('ventas.cliente')
            ->where('ventas.cliente', '!=', '')
            ->select(
                DB::raw('COALESCE(clientes.nombre, ventas.cliente) as nombre_cliente'),
                DB::raw('COUNT(ventas.id) as total_compras'),
                DB::raw('SUM(ventas.total) as total_monto')
            )
            ->groupBy('nombre_cliente')
            ->orderBy('total_monto', 'desc')
            ->limit(10)
            ->get();

        if ($topClientes->isEmpty()) {
            $topClientes = DB::table('ventas')
                ->leftJoin('clientes', 'ventas.id_cliente', '=', 'clientes.id')
                ->where('ventas.estado', '!=', 'Anulada')
                ->whereNotNull('ventas.cliente')
                ->where('ventas.cliente', '!=', '')
                ->select(
                    DB::raw('COALESCE(clientes.nombre, ventas.cliente) as nombre_cliente'),
                    DB::raw('COUNT(ventas.id) as total_compras'),
                    DB::raw('SUM(ventas.total) as total_monto')
                )
                ->groupBy('nombre_cliente')
                ->orderBy('total_monto', 'desc')
                ->limit(10)
                ->get();
        }

        $maxMontoCliente = $topClientes->max('total_monto') ? floatval($topClientes->max('total_monto')) : 1.0;
        if ($maxMontoCliente <= 0) $maxMontoCliente = 1.0;
        $totalMontoTopClientes = floatval($topClientes->sum('total_monto'));

        // ============================================================
        // 6. MÉTRICAS COMPLEMENTARIAS DE VALOR (ALERTAS & OPERACIONES)
        // ============================================================
        // A) Alertas de stock bajo / agotándose
        $productosBajoStock = Producto::where(function($q) {
                $q->whereColumn('stock', '<=', 'stock_minimo')
                  ->orWhere('stock', '<=', 5);
            })
            ->orderBy('stock', 'asc')
            ->limit(6)
            ->get();

        $conteoBajoStock = Producto::where(function($q) {
                $q->whereColumn('stock', '<=', 'stock_minimo')
                  ->orWhere('stock', '<=', 5);
            })->count();

        // B) Últimas 6 transacciones registradas en el sistema
        $ultimasVentas = Venta::with(['usuario'])
            ->orderBy('fecha', 'desc')
            ->limit(6)
            ->get();

        return view('inicio.index', compact(
            'mesSeleccionado',
            'fechaMes',
            'nombreMesActual',
            'mesesEspanol',
            'anio',
            'esMesActual',
            'mesAnterior',
            'mesSiguiente',
            'ventasMes',
            'conteoVentasMes',
            'variacionVentas',
            'margenEstimado',
            'ventasHoy',
            'promedioDiario',
            'totalProductos',
            'totalPorCobrar',
            'conteoPorCobrar',
            'mesesLabels',
            'ventasPorMesAnio',
            'top5Labels',
            'top5Cant',
            'top5Monto',
            'top10Labels',
            'top10Cant',
            'top10Monto',
            'topClientes',
            'maxMontoCliente',
            'totalMontoTopClientes',
            'rangosDiarios',
            'productosBajoStock',
            'conteoBajoStock',
            'ultimasVentas'
        ));
    }

    /**
     * Calcula ventas reales de los últimos $dias días.
     */
    private function obtenerVentasDias(int $dias): array
    {
        $hoy = Carbon::today();
        $labels = [];
        $valores = [];

        for ($i = $dias - 1; $i >= 0; $i--) {
            $dia = $hoy->copy()->subDays($i);
            $fechaStr = $dia->format('Y-m-d');
            $labels[] = $dia->format('d/m');

            $totalDia = Venta::where('estado', '!=', 'Anulada')
                ->whereDate('fecha', $fechaStr)
                ->sum('total');

            $valores[] = round(floatval($totalDia), 2);
        }

        return ['labels' => $labels, 'valores' => $valores];
    }

    /**
     * Calcula ventas reales de cada día del mes completo seleccionado (1 al 28/30/31).
     */
    private function obtenerVentasMesCompleto(Carbon $fechaMes): array
    {
        $diasEnMes = $fechaMes->daysInMonth;
        $labels = [];
        $valores = [];

        for ($d = 1; $d <= $diasEnMes; $d++) {
            $dia = $fechaMes->copy()->day($d);
            $fechaStr = $dia->format('Y-m-d');
            $labels[] = str_pad((string)$d, 2, '0', STR_PAD_LEFT);

            $totalDia = Venta::where('estado', '!=', 'Anulada')
                ->whereDate('fecha', $fechaStr)
                ->sum('total');

            $valores[] = round(floatval($totalDia), 2);
        }

        return ['labels' => $labels, 'valores' => $valores];
    }
}
