<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Movimiento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    // 1. Mostrar el historial de ventas
    public function index(Request $request)
    {
        // 1. Empezamos la consulta sin ordenarla todavía
        $query = Venta::with('usuario');

        // --- FILTROS ---
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('cliente', 'LIKE', "%{$buscar}%")
                  ->orWhere('numero_nota', 'LIKE', "%{$buscar}%");
            });
        }
        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }
        if ($request->filled('cajero')) {
            $query->where('id_usuario', $request->cajero);
        }
        if ($request->has('notas_fisicas')) {
            $query->where('numero_nota', 'NOT LIKE', 'SYS-%');
        }
        // NUEVO FILTRO: Estado (Completada o Anulada)
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // --- ORDENAMIENTO (SORTING) ---
        // Recibimos qué columna quieren ordenar y en qué dirección (ascendente/descendente)
        $sortBy = $request->input('sort_by', 'fecha'); // Por defecto ordena por fecha
        $sortOrder = $request->input('sort_order', 'desc'); // Por defecto las más nuevas primero
        
        // Bloqueo de seguridad para que solo puedan ordenar por estas 4 columnas
        $allowedSorts = ['numero_nota', 'fecha', 'cliente', 'total'];
        
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('fecha', 'desc');
        }

        $ventas = $query->get();

        return view('ventas.index', compact('ventas'));
    }

    // 2. Generar el PDF (Ya lo tenías, lo mantenemos intacto)
    public function pdf($id)
    {
        $venta = Venta::with('detalles.producto')->findOrFail($id);
        $pdf = Pdf::loadView('ventas.pdf', compact('venta'));
        return $pdf->stream('Nota_Venta_' . $venta->numero_nota . '.pdf');
    }

    public function anular($id)
    {
        $venta = Venta::with('detalles')->findOrFail($id);

        if ($venta->estado == 'Anulada') {
            return back()->with('error', 'Esta venta ya fue anulada anteriormente.');
        }

        DB::beginTransaction();
        try {
            // 1. Recorrer los productos de esta venta
            foreach ($venta->detalles as $detalle) {
                // Buscamos el producto exacto en la base de datos
                $producto = Producto::find($detalle->id_producto ?? $detalle->producto_id); 
                
                if ($producto) {
                    // A. Devolvemos el stock físico al módulo de Productos
                    $producto->stock += $detalle->cantidad; 
                    $producto->save();
                    
                    // B. Registramos la auditoría en el módulo de Inventario
                    $movimiento = new Movimiento();
                    $movimiento->id_producto = $producto->id; // Cambia a producto_id si tu BD lo requiere
                    $movimiento->tipo = 'Agregar';
                    $movimiento->cantidad = $detalle->cantidad;
                    $movimiento->motivo = 'Devolución por Anulación de Venta #' . $venta->numero_nota;
                    $movimiento->id_usuario = Auth::id() ?? 1;
                    $movimiento->fecha = now();
                    $movimiento->save();
                }
            }

            // 2. Marcar la venta como anulada
            $venta->estado = 'Anulada';
            $venta->save();

            DB::commit();
            return back()->with('success', 'Venta anulada correctamente. El stock ha sido devuelto y registrado en el Inventario.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Si algo falla, Laravel cancelará todo para no arruinar tu base de datos
            return back()->with('error', 'Error crítico al anular: ' . $e->getMessage());
        }
    }
}