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
        ]);

        // 1. Guardar en el historial
        $movimiento = new Movimiento();
        $movimiento->id_producto = $request->id_producto;
        $movimiento->tipo        = $request->tipo;
        $movimiento->cantidad    = $request->cantidad;
        $movimiento->motivo      = $request->motivo ?? 'Ajuste manual de inventario';
        $movimiento->id_usuario = Auth::id() ?? 1; // Asigna el usuario logueado (o usuario 1 por defecto)
        $movimiento->save();

        // 2. Sumar o restar el stock al Producto
        $producto = Producto::find($request->id_producto);
        if ($request->tipo == 'ENTRADA') {
            $producto->stock += $request->cantidad;
        } else {
            $producto->stock -= $request->cantidad;
        }
        $producto->save();

        return redirect('/inventario')->with('success', 'Ajuste de inventario registrado y stock actualizado con éxito.');
    }
}