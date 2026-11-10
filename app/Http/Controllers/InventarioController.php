<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movimiento;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
    public function index()
    {
        $movimientos = Movimiento::with(['producto', 'usuario'])->orderBy('id', 'desc')->get(); 
        $productos = Producto::orderBy('nombre', 'asc')->get();
        return view('inventario.index', compact('movimientos', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_producto' => 'required',
            'tipo'        => 'required',
            'cantidad'    => 'required|numeric|min:1',
            'motivo'      => 'required|min:3',
        ], [
            'motivo.required' => 'El motivo es obligatorio. Describí brevemente el motivo del ajuste.',
            'motivo.min'      => 'El motivo debe tener al menos 3 caracteres.',
        ]);

        $producto = Producto::findOrFail($request->id_producto);
        $stockAnterior = intval($producto->stock ?? 0);
        $cantidad = intval($request->cantidad);
        $esEntrada = in_array(strtoupper(trim($request->tipo)), ['ENTRADA', 'AGREGAR']);

        if ($esEntrada) {
            $tipoNormalizado = 'Entrada';
            $stockNuevo = $stockAnterior + $cantidad;
        } else {
            $tipoNormalizado = 'Salida';
            $stockNuevo = max(0, $stockAnterior - $cantidad);
        }

        $producto->stock = $stockNuevo;
        $producto->save();

        // 1. Guardar en el historial con trazabilidad completa (Kardex)
        $movimiento = new Movimiento();
        $movimiento->id_producto    = $producto->id;
        $movimiento->tipo           = $tipoNormalizado;
        $movimiento->cantidad       = $cantidad;
        $movimiento->stock_anterior = $stockAnterior;
        $movimiento->stock_nuevo    = $stockNuevo;
        $movimiento->motivo         = $request->motivo;
        $movimiento->fecha          = now();
        $movimiento->id_usuario     = Auth::id() ?? 1;
        $movimiento->save();

        return redirect('/inventario')->with('success', 'Ajuste de inventario registrado y stock actualizado con éxito.');
    }
}