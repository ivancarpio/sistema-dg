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

        $verTodo = $request->input('ver') === 'todos' || ($request->has('fecha') && empty($request->input('fecha')));
        // Por defecto, si no se solicita ver todo y no se especifica fecha, se filtra por HOY
        $fechaFiltro = $verTodo ? null : $request->input('fecha', Carbon::now()->toDateString());

        if (!empty($fechaFiltro)) {
            $query->whereDate('fecha', $fechaFiltro);
            $subtituloPeriodo = ($fechaFiltro === Carbon::now()->toDateString())
                ? 'movimientos de hoy (' . Carbon::parse($fechaFiltro)->format('d/m/Y') . ')'
                : 'movimientos del ' . Carbon::parse($fechaFiltro)->format('d/m/Y');
        } else {
            $subtituloPeriodo = 'todo el historial acumulado';
        }

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where(function($q) use ($buscar) {
                $q->where('motivo', 'LIKE', "%{$buscar}%")
                  ->orWhere('comentario', 'LIKE', "%{$buscar}%")
                  ->orWhereHas('producto', function($qp) use ($buscar) {
                      $qp->where('nombre', 'LIKE', "%{$buscar}%")
                         ->orWhere('codigo', 'LIKE', "%{$buscar}%");
                  })
                  ->orWhereHas('usuario', function($qu) use ($buscar) {
                      $qu->where('name', 'LIKE', "%{$buscar}%");
                  });
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
        $productos = Producto::with(['unidad', 'unidadSecundaria'])->orderBy('nombre', 'asc')->get();

        return view('inventario.index', compact('movimientos', 'productos', 'fechaFiltro', 'verTodo', 'subtituloPeriodo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:productos,id',
            'tipo'        => 'required',
            'motivo'      => 'required|min:3',
        ], [
            'id_producto.required' => 'Debes seleccionar un producto.',
            'motivo.required'      => 'El motivo es obligatorio. Describe brevemente el motivo del ajuste.',
            'motivo.min'           => 'El motivo debe tener al menos 3 caracteres.',
        ]);

        $producto = Producto::with(['unidad', 'unidadSecundaria'])->findOrFail($request->id_producto);
        $stockAnterior = intval($producto->stock ?? 0);
        $esEntrada = in_array(strtoupper(trim($request->tipo)), ['ENTRADA', 'AGREGAR']);

        $modoIngreso = $request->input('modo_ingreso', 'piezas');
        $unidadBaseNombre = optional($producto->unidad)->nombre ?? 'Pieza';
        
        if ($modoIngreso === 'empaque') {
            $cantEmpaques = max(1, intval($request->input('cantidad_empaques', 1)));
            $factor = max(1, floatval($request->input('factor_conversion_empaque', 1)));
            $cantidadFisica = intval(ceil($cantEmpaques * $factor));
            $nombreEmpaque = trim($request->input('nombre_empaque', '')) ?: (optional($producto->unidadSecundaria)->nombre ?? 'Paquete');
            
            $detalleEmpaque = "[{$cantEmpaques} {$nombreEmpaque}(s) x {$factor} u. = {$cantidadFisica} {$unidadBaseNombre}] ";
        } else {
            $cantidadFisica = max(1, intval($request->input('cantidad', 1)));
            $detalleEmpaque = '';
        }

        if ($esEntrada) {
            $tipoNormalizado = 'Entrada';
            $stockNuevo = $stockAnterior + $cantidadFisica;
        } else {
            $tipoNormalizado = 'Salida';
            $stockNuevo = max(0, $stockAnterior - $cantidadFisica);
        }

        $producto->stock = $stockNuevo;
        $producto->save();

        // 1. Guardar en el historial con trazabilidad completa (Kardex)
        $movimiento = new Movimiento();
        $movimiento->id_producto    = $producto->id;
        $movimiento->tipo           = $tipoNormalizado;
        $movimiento->cantidad       = $cantidadFisica;
        $movimiento->stock_anterior = $stockAnterior;
        $movimiento->stock_nuevo    = $stockNuevo;
        $movimiento->motivo         = $detalleEmpaque . $request->motivo;
        $movimiento->fecha          = now();
        $movimiento->id_usuario     = Auth::id() ?? 1;
        $movimiento->save();

        $signo = $esEntrada ? '+' : '-';
        return redirect('/inventario')->with('success', "Ajuste registrado con éxito: {$signo}{$cantidadFisica} {$unidadBaseNombre}(s) actualizadas en tienda.");
    }
}