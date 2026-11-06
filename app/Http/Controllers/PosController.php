<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Movimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['marca', 'unidad'])->where('stock', '>', 0)->get();
        return view('pos.index', compact('productos'));
    }

    public function cobrar(Request $request)
    {
        if (!$request->has('productos') || empty($request->productos)) {
            return back()->with('error', 'El carrito está vacío.');
        }

        DB::beginTransaction();

        try {
            // A. Registrar la Venta
            $nota_manual = $request->input('numero_nota');

            if (!empty($nota_manual)) {
                // Guardamos exactamente el número que escribió el cajero, sin prefijos
                $numero_final = $nota_manual; 
            } else {
                // Si lo dejó en blanco, autogeneramos
                $ultimo_id = DB::table('ventas')->max('id') + 1;
                $numero_final = 'SYS-' . str_pad($ultimo_id, 6, '0', STR_PAD_LEFT);
            }

            $venta = new Venta();
            $venta->cliente = $request->cliente;
            $venta->metodo_pago = $request->metodo_pago ?? 'Efectivo';
            $venta->id_usuario = Auth::id() ?? 1;
            $venta->fecha = now();
            $venta->total = $request->total_venta;
            $venta->numero_nota = $numero_final; // <-- Aquí guardamos nuestro nuevo número
            $venta->save();

            // B. Recorrer los productos del carrito
            foreach ($request->productos as $item) {
                
                // 1. Guardar el Detalle de la Venta
                $detalle = new DetalleVenta();
                $detalle->id_venta = $venta->id;
                $detalle->id_producto = $item['id'];
                $detalle->cantidad = $item['cantidad'];
                $detalle->precio_unitario = $item['precio'];
                $detalle->subtotal = $item['cantidad'] * $item['precio'];
                $detalle->save();

                // 2. Restar el Stock del Producto
                $producto = Producto::find($item['id']);
                $producto->stock -= $item['cantidad'];
                $producto->save();

                // 3. Registrar el Movimiento en la Bitácora de Inventario
                $movimiento = new Movimiento(); // <- PRIMERO SE CREA EL OBJETO
                $movimiento->id_producto = $item['id'];
                $movimiento->id_usuario = Auth::id() ?? 1; // <- AQUÍ SE ASIGNA EL USUARIO
                $movimiento->tipo = 'SALIDA';
                $movimiento->cantidad = $item['cantidad'];
                $movimiento->motivo = 'Venta #' . $venta->id; 
                $movimiento->fecha = now();
                $movimiento->save();
            }

            DB::commit();
            return redirect('/pos')->with('success_venta_id', $venta->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error en BD: ' . $e->getMessage());
        }
    }
}