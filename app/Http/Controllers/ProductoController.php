<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Marca;
use App\Models\Unidad;
use App\Models\Categoria;
use App\Services\CodigoProductoService;

class ProductoController extends Controller
{
    // 1. Mostrar la pantalla
    public function index()
    {
        $productos = Producto::with(['marca', 'unidad', 'categoria'])->orderBy('id', 'desc')->get();
        $marcas = Marca::all();
        $unidades = Unidad::all();
        $categorias = Categoria::orderBy('nombre')->get();

        // Estadísticas clave para las tarjetas superiores del catálogo
        $totalProductos = $productos->count();
        $totalStockUnidades = $productos->sum('stock');

        // Semáforo inteligente de existencias
        $productosCriticos = $productos->filter(function($p) {
            $min = is_numeric($p->stock_minimo) && $p->stock_minimo > 0 ? intval($p->stock_minimo) : 5;
            return ($p->stock ?? 0) <= $min;
        })->count();

        $productosAlerta = $productos->filter(function($p) {
            $min = is_numeric($p->stock_minimo) && $p->stock_minimo > 0 ? intval($p->stock_minimo) : 5;
            $umbral = max($min * 2, $min + 5);
            $stk = $p->stock ?? 0;
            return $stk > $min && $stk <= $umbral;
        })->count();

        $productosAgotados = $productos->filter(function($p) {
            return ($p->stock ?? 0) <= 0;
        })->count();

        $productosStockBajo = $productosCriticos;

        $valorInventario = $productos->sum(function($p) {
            return ($p->stock ?? 0) * ($p->precio_venta ?? 0);
        });

        return view('productos.index', compact(
            'productos', 'marcas', 'unidades', 'categorias',
            'totalProductos', 'totalStockUnidades', 'productosCriticos', 'productosAlerta',
            'productosAgotados', 'productosStockBajo', 'valorInventario'
        ));
    }

    
    // 2. Guardar el nuevo producto
    public function store(Request $request)
    {
        // Si no se proporcionó código, auto-generar con el estándar
        if (empty($request->codigo)) {
            $request->merge(['codigo' => CodigoProductoService::generarCodigo($request->nombre ?? '')]);
        }

        // Validamos que el código no se repita en la base de datos
        $request->validate([
            'codigo' => 'required|unique:productos,codigo',
            'nombre' => 'required',
            'precio_venta' => 'required|numeric|min:0',
            'precio_cliente' => 'nullable|numeric|min:0',
            'costo' => 'nullable|numeric|min:0',
        ]);

        $producto = new Producto();
        $producto->codigo = $request->codigo;
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->id_marca = $request->id_marca;
        $producto->id_categoria = $request->id_categoria;
        $producto->id_unidad = $request->id_unidad;
        $producto->costo = $request->costo ?? 0;
        $producto->precio_venta = $request->precio_venta;
        $producto->precio_cliente = ($request->filled('precio_cliente') && is_numeric($request->precio_cliente)) ? floatval($request->precio_cliente) : null;
        $producto->stock = $request->stock_inicial ?? 0;
        $producto->stock_minimo = $request->stock_minimo ?? 0;
        
        // Lo guardamos apagando timestamps por si tu tabla no tiene created_at
        $producto->timestamps = false; 
        $producto->save();

        return back()->with('success', '¡Producto registrado en el catálogo con éxito!');
    }

    // 3. Actualizar el producto (El Lápiz)
    public function update(Request $request, $id)
    {   
        // Si el código viene vacío, auto-generar
        if (empty($request->codigo)) {
            $request->merge(['codigo' => CodigoProductoService::generarCodigo($request->nombre ?? '', (int)$id)]);
        }

        // Validamos asegurándonos de ignorar el código del producto actual para que no dé falso error de "código repetido"
        $request->validate([
            'codigo' => 'required|unique:productos,codigo,' . $id,
            'nombre' => 'required',
            'precio_venta' => 'required|numeric|min:0',
            'precio_cliente' => 'nullable|numeric|min:0',
            'costo' => 'nullable|numeric|min:0',
        ]);

        $producto = Producto::findOrFail($id);
        
        $producto->codigo = $request->codigo;
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->id_marca = $request->id_marca;
        $producto->id_categoria = $request->id_categoria;
        $producto->id_unidad = $request->id_unidad;
        $producto->costo = $request->costo ?? 0;
        $producto->precio_venta = $request->precio_venta;
        $producto->precio_cliente = ($request->filled('precio_cliente') && is_numeric($request->precio_cliente)) ? floatval($request->precio_cliente) : null;
        $producto->stock_minimo = $request->stock_minimo ?? 0;

        $producto->save();

        return back()->with('success', '¡Producto actualizado correctamente!');
    }

    // 4. Eliminar el producto (El Basurero)
    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);

            // Verificar si tiene ventas registradas en detalle_ventas
            $tieneVentas = \Illuminate\Support\Facades\DB::table('detalle_ventas')
                ->where('id_producto', $id)
                ->exists();

            if ($tieneVentas) {
                return back()->withErrors('No puedes eliminar este producto porque ya tiene notas de venta asociadas.');
            }

            // Si tiene movimientos de inventario o checks de cierre, los limpiamos para permitir eliminar el producto
            \Illuminate\Support\Facades\DB::table('movimientos')->where('id_producto', $id)->delete();
            \Illuminate\Support\Facades\DB::table('cierre_mes_checks')->where('id_producto', $id)->delete();

            $producto->delete();
            return back()->with('success', '¡Producto eliminado del catálogo con éxito!');
        } catch (\Exception $e) {
            return back()->withErrors('Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    // 5. Endpoint AJAX para auto-generar código estándar ferretero
    public function generarCodigoAjax(Request $request)
    {
        $nombre = $request->query('nombre', '');
        $id = $request->query('id') ? (int)$request->query('id') : null;
        $codigo = CodigoProductoService::generarCodigo($nombre, $id);
        return response()->json(['codigo' => $codigo]);
    }

    public function storeCategoria(Request $request)
    {
        $categoria = new Categoria();
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->save();

        return back()->with('success', 'Categoría creada exitosamente.');
    }
}