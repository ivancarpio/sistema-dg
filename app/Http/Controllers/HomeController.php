<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // 1. DATOS REALES (Calculados desde tu base de datos)
        // Ventas de este mes
        $ventasMes = Venta::whereMonth('fecha', date('m'))
                          ->whereYear('fecha', date('Y'))
                          ->sum('total');
                          
        // Total de productos registrados en el catálogo
        $totalProductos = Producto::count();
        
        // Ventas del día de hoy
        $ventasHoy = Venta::whereDate('fecha', date('Y-m-d'))->sum('total');

        return view('inicio.index', compact('ventasMes', 'totalProductos', 'ventasHoy'));
    }
}