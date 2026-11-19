<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movimiento;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Movimiento::with(['producto', 'usuario']);

        $verTodo = $request->input('ver') === 'todos';
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        // Compatibilidad con filtro de fecha única
        if (!$verTodo && !$fechaDesde && !$fechaHasta && $request->filled('fecha')) {
            $fechaDesde = $request->input('fecha');
            $fechaHasta = $request->input('fecha');
        }

        // Por defecto, si no se solicita ver todo y no se especifican fechas, filtrar por HOY
        if (!$verTodo && !$fechaDesde && !$fechaHasta) {
            $fechaDesde = Carbon::now()->toDateString();
            $fechaHasta = Carbon::now()->toDateString();
        }

        if (!$verTodo && ($fechaDesde || $fechaHasta)) {
            if ($fechaDesde && $fechaHasta) {
                if ($fechaDesde === $fechaHasta) {
                    $query->whereDate('fecha', $fechaDesde);
                    $subtituloPeriodo = ($fechaDesde === Carbon::now()->toDateString())
                        ? 'movimientos de hoy (' . Carbon::parse($fechaDesde)->format('d/m/Y') . ')'
                        : 'movimientos del ' . Carbon::parse($fechaDesde)->format('d/m/Y');
                } else {
                    $query->whereBetween('fecha', [$fechaDesde . ' 00:00:00', $fechaHasta . ' 23:59:59']);
                    $subtituloPeriodo = 'movimientos del ' . Carbon::parse($fechaDesde)->format('d/m/Y') . ' al ' . Carbon::parse($fechaHasta)->format('d/m/Y');
                }
            } elseif ($fechaDesde) {
                $query->where('fecha', '>=', $fechaDesde . ' 00:00:00');
                $subtituloPeriodo = 'movimientos desde el ' . Carbon::parse($fechaDesde)->format('d/m/Y');
            } elseif ($fechaHasta) {
                $query->where('fecha', '<=', $fechaHasta . ' 23:59:59');
                $subtituloPeriodo = 'movimientos hasta el ' . Carbon::parse($fechaHasta)->format('d/m/Y');
            }
        } else {
            $subtituloPeriodo = 'todo el historial acumulado';
            $fechaDesde = null;
            $fechaHasta = null;
        }

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $palabras = array_filter(explode(' ', $buscar));
            $query->where(function($q) use ($palabras) {
                foreach ($palabras as $palabra) {
                    $q->where(function($sub) use ($palabra) {
                        $sub->where('motivo', 'LIKE', "%{$palabra}%")
                            ->orWhere('comentario', 'LIKE', "%{$palabra}%")
                            ->orWhereHas('producto', function($qp) use ($palabra) {
                                $qp->where('nombre', 'LIKE', "%{$palabra}%")
                                   ->orWhere('codigo', 'LIKE', "%{$palabra}%")
                                   ->orWhereHas('marca', function($qm) use ($palabra) {
                                       $qm->where('nombre', 'LIKE', "%{$palabra}%");
                                   });
                            })
                            ->orWhereHas('usuario', function($qu) use ($palabra) {
                                $qu->where('name', 'LIKE', "%{$palabra}%");
                            });
                    });
                }
            });
        }

        if ($request->filled('tipo')) {
            $t = strtolower(trim($request->tipo));
            if ($t === 'devolucion') {
                $query->where(function($q) {
                    $q->whereIn(DB::raw('LOWER(tipo)'), ['devolución', 'devolucion'])
                      ->orWhere('motivo', 'LIKE', '%devolución%')
                      ->orWhere('motivo', 'LIKE', '%devolucion%')
                      ->orWhere('motivo', 'LIKE', '%anulación%')
                      ->orWhere('motivo', 'LIKE', '%anulacion%');
                });
            } elseif ($t === 'entrada') {
                $query->whereIn(DB::raw('LOWER(tipo)'), ['entrada', 'agregar'])
                      ->where('motivo', 'NOT LIKE', '%devolución%')
                      ->where('motivo', 'NOT LIKE', '%devolucion%')
                      ->where('motivo', 'NOT LIKE', '%anulación%')
                      ->where('motivo', 'NOT LIKE', '%anulacion%');
            } elseif ($t === 'salida') {
                $query->where(DB::raw('LOWER(tipo)'), 'salida')
                      ->where('motivo', 'NOT LIKE', '%devolución%')
                      ->where('motivo', 'NOT LIKE', '%devolucion%')
                      ->where('motivo', 'NOT LIKE', '%anulación%')
                      ->where('motivo', 'NOT LIKE', '%anulacion%');
            }
        }

        $movimientos = $query->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get(); 
        $productos = Producto::with(['marca', 'unidad', 'unidadSecundaria'])->orderBy('nombre', 'asc')->get();
        $fechaFiltro = $fechaDesde;

        return view('inventario.index', compact('movimientos', 'productos', 'fechaDesde', 'fechaHasta', 'verTodo', 'subtituloPeriodo', 'fechaFiltro'));
    }

    public function store(Request $request)
    {
        // 1. Obtener los IDs de productos seleccionados (soporta selección múltiple o individual)
        $productosIds = $request->input('productos_ids', []);
        if (empty($productosIds) && $request->filled('id_producto')) {
            $productosIds = [$request->input('id_producto')];
        }

        // Si se envió un lote JSON (compatibilidad previa)
        if ($request->has('modo_lote') || $request->has('lote_items')) {
            return $this->storeLote($request);
        }

        if (empty($productosIds) || count($productosIds) === 0) {
            return back()->with('error', 'Debes marcar al menos un producto de la lista para registrar el ajuste.');
        }

        $request->validate([
            'tipo'   => 'required',
            'motivo' => 'required|min:3',
        ], [
            'motivo.required' => 'El motivo es obligatorio. Describe brevemente el motivo del ajuste.',
            'motivo.min'      => 'El motivo debe tener al menos 3 caracteres.',
        ]);

        $tipo = strtoupper(trim($request->tipo));
        $esEntrada = in_array($tipo, ['ENTRADA', 'AGREGAR']);
        $tipoNormalizado = $esEntrada ? 'Entrada' : 'Salida';
        $motivoTexto = trim($request->motivo);
        $usuarioId = Auth::id() ?? 1;

        $cantidadesInput = $request->input('cantidades', []);
        $modosInput = $request->input('modos_ingreso', []);
        $cantidadGeneral = intval($request->input('cantidad_general', 0));
        $modoGeneral = $request->input('modo_ingreso', 'piezas');

        DB::beginTransaction();
        try {
            $totalActualizados = 0;
            $totalFisicas = 0;
            $primerProducto = null;

            foreach ($productosIds as $idProd) {
                $idProdInt = intval($idProd);
                $producto = Producto::with(['marca', 'unidad', 'unidadSecundaria'])->find($idProdInt);
                if (!$producto) continue;

                if (!$primerProducto) {
                    $primerProducto = $producto;
                }

                $stockAnterior = intval($producto->stock ?? 0);
                $unidadBaseNombre = optional($producto->unidad)->nombre ?? 'Pieza';
                
                // Cantidad específica para este producto si fue editada en la fila, o la cantidad general
                $cant = (isset($cantidadesInput[$idProdInt]) && intval($cantidadesInput[$idProdInt]) > 0)
                    ? intval($cantidadesInput[$idProdInt])
                    : ($cantidadGeneral > 0 ? $cantidadGeneral : 0);

                if ($cant <= 0) {
                    return back()->with('error', "La cantidad a ajustar para '{$producto->nombre}' debe ser mayor a 0.");
                }

                $modo = isset($modosInput[$idProdInt]) ? $modosInput[$idProdInt] : $modoGeneral;

                if ($modo === 'empaque' && $producto->tieneUnidadSecundaria()) {
                    $factor = max(1, floatval($producto->factor_conversion ?? 1));
                    $cantidadFisica = intval(ceil($cant * $factor));
                    $nombreEmpaque = optional($producto->unidadSecundaria)->nombre ?? 'Paquete';
                    $detalleEmpaque = "[{$cant} {$nombreEmpaque}(s) x {$factor} u. = {$cantidadFisica} {$unidadBaseNombre}] ";
                } else {
                    $cantidadFisica = $cant;
                    $detalleEmpaque = count($productosIds) > 1 ? "[{$cantidadFisica} {$unidadBaseNombre}] " : '';
                }

                if ($esEntrada) {
                    $stockNuevo = $stockAnterior + $cantidadFisica;
                } else {
                    $stockNuevo = max(0, $stockAnterior - $cantidadFisica);
                }

                $producto->stock = $stockNuevo;
                $producto->save();

                // Auditoría Kardex individual para este producto
                $movimiento = new Movimiento();
                $movimiento->id_producto    = $producto->id;
                $movimiento->tipo           = $tipoNormalizado;
                $movimiento->cantidad       = $cantidadFisica;
                $movimiento->stock_anterior = $stockAnterior;
                $movimiento->stock_nuevo    = $stockNuevo;
                $movimiento->motivo         = $detalleEmpaque . $motivoTexto;
                $movimiento->fecha          = now();
                $movimiento->id_usuario     = $usuarioId;
                $movimiento->save();

                $totalActualizados++;
                $totalFisicas += $cantidadFisica;
            }

            DB::commit();

            $signo = $esEntrada ? '+' : '-';
            if ($totalActualizados === 1 && $primerProducto) {
                $marcaNombre = optional($primerProducto->marca)->nombre;
                $txtMarca = $marcaNombre ? " [{$marcaNombre}]" : "";
                $nombreUnidadFinal = optional($primerProducto->unidad)->nombre ?? 'unidades';
                return redirect('/inventario')->with('success', "Ajuste registrado con éxito para {$primerProducto->nombre}{$txtMarca}: {$signo}{$totalFisicas} {$nombreUnidadFinal} actualizadas en tienda.");
            } else {
                return redirect('/inventario')->with('success', "¡Ajuste registrado con éxito! Se actualizaron {$totalActualizados} productos ({$signo}{$totalFisicas} unidades físicas en total) en tienda.");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el ajuste de inventario: ' . $e->getMessage());
        }
    }

    /**
     * Registra un ingreso múltiple (lote masivo de mercadería) en una sola transacción segura.
     */
    public function storeLote(Request $request)
    {
        $rawItems = $request->input('lote_items');
        if (is_string($rawItems)) {
            $items = json_decode($rawItems, true) ?? [];
        } elseif (is_array($rawItems)) {
            $items = $rawItems;
        } else {
            $items = [];
        }

        if (empty($items) || count($items) === 0) {
            return back()->with('error', 'Debes agregar al menos un producto a la lista del lote antes de guardar.');
        }

        $request->validate([
            'motivo_lote' => 'required|min:3',
        ], [
            'motivo_lote.required' => 'El motivo general del ingreso múltiple es obligatorio.',
            'motivo_lote.min'      => 'El motivo debe tener al menos 3 caracteres.',
        ]);

        $tipo = strtoupper(trim($request->input('tipo_lote', 'ENTRADA')));
        $esEntrada = in_array($tipo, ['ENTRADA', 'AGREGAR']);
        $tipoNormalizado = $esEntrada ? 'Entrada' : 'Salida';
        $motivoGeneral = trim($request->input('motivo_lote'));
        $usuarioId = Auth::id() ?? 1;

        DB::beginTransaction();
        try {
            $totalActualizados = 0;
            $totalUnidadesFisicas = 0;

            foreach ($items as $item) {
                $idProducto = intval($item['id_producto'] ?? $item['id'] ?? 0);
                if ($idProducto <= 0) continue;

                $producto = Producto::with(['marca', 'unidad', 'unidadSecundaria'])->find($idProducto);
                if (!$producto) continue;

                $stockAnterior = intval($producto->stock ?? 0);
                $modo = $item['modo_ingreso'] ?? 'piezas';
                $unidadBaseNombre = optional($producto->unidad)->nombre ?? 'Pieza';

                if ($modo === 'empaque') {
                    $cantEmpaques = max(1, intval($item['cantidad_empaques'] ?? 1));
                    $factor = max(1, floatval($item['factor_conversion'] ?? ($producto->factor_conversion ?? 1)));
                    $cantidadFisica = intval(ceil($cantEmpaques * $factor));
                    $nombreEmpaque = trim($item['nombre_empaque'] ?? '') ?: (optional($producto->unidadSecundaria)->nombre ?? 'Paquete');
                    $detalleEmpaque = "[Lote: {$cantEmpaques} {$nombreEmpaque}(s) x {$factor} u. = {$cantidadFisica} {$unidadBaseNombre}] ";
                } else {
                    $cantidadFisica = max(1, intval($item['cantidad'] ?? 1));
                    $detalleEmpaque = "[Lote: {$cantidadFisica} {$unidadBaseNombre}] ";
                }

                if ($esEntrada) {
                    $stockNuevo = $stockAnterior + $cantidadFisica;
                } else {
                    $stockNuevo = max(0, $stockAnterior - $cantidadFisica);
                }

                $producto->stock = $stockNuevo;
                $producto->save();

                // Auditoría Kardex individual para este producto
                $movimiento = new Movimiento();
                $movimiento->id_producto    = $producto->id;
                $movimiento->tipo           = $tipoNormalizado;
                $movimiento->cantidad       = $cantidadFisica;
                $movimiento->stock_anterior = $stockAnterior;
                $movimiento->stock_nuevo    = $stockNuevo;
                $movimiento->motivo         = $detalleEmpaque . $motivoGeneral;
                $movimiento->fecha          = now();
                $movimiento->id_usuario     = $usuarioId;
                $movimiento->save();

                $totalActualizados++;
                $totalUnidadesFisicas += $cantidadFisica;
            }

            DB::commit();

            $signo = $esEntrada ? '+' : '-';
            return redirect('/inventario')->with('success', "¡Ingreso múltiple registrado con éxito! Se procesaron {$totalActualizados} productos ({$signo}{$totalUnidadesFisicas} unidades físicas en total) en tienda.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el ingreso múltiple: ' . $e->getMessage());
        }
    }
}