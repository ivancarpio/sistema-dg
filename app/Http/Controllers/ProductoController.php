<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Marca;
use App\Models\Unidad;
use App\Models\Categoria;

class ProductoController extends Controller
{
    // 1. Mostrar la pantalla
    public function index()
    {
        $productos = Producto::with(['marca', 'unidad', 'categoria'])->orderBy('id', 'desc')->get();
        $marcas = Marca::all();
        $unidades = Unidad::all();
        $categorias = Categoria::orderBy('nombre')->get(); // NUEVO

        return view('productos.index', compact('productos', 'marcas', 'unidades', 'categorias'));
    }

    
    // 2. Guardar el nuevo producto
    public function store(Request $request)
    {

        if (auth()->user()->rol !== 'admin') {
            return back()->withErrors(['No tienes permisos de administrador para realizar esta acción.']);
        }

        // Validamos que el código no se repita en la base de datos
        $request->validate([
            'codigo' => 'required|unique:productos,codigo',
            'nombre' => 'required',
            'precio_venta' => 'required|numeric|min:0'
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
        $producto->stock = 0; 
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

        if (auth()->user()->rol !== 'admin') {
            return back()->withErrors(['No tienes permisos de administrador para realizar esta acción.']);
        }
        
        // Validamos asegurándonos de ignorar el código del producto actual para que no dé falso error de "código repetido"
        $request->validate([
            'codigo' => 'required|unique:productos,codigo,' . $id,
            'nombre' => 'required',
            'precio_venta' => 'required|numeric|min:0'
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
        $producto->stock_minimo = $request->stock_minimo ?? 0;

        $producto->save();

        return back()->with('success', '¡Producto actualizado correctamente!');
    }

    // 4. Eliminar el producto (El Basurero)
    public function destroy($id)
    {

        if (auth()->user()->rol !== 'admin') {
            return back()->withErrors(['No tienes permisos de administrador para realizar esta acción.']);
        }

        try {
            $producto = Producto::findOrFail($id);
            $producto->delete();
            return back()->with('success', '¡Producto eliminado del sistema con éxito!');
        } catch (\Exception $e) {
            // Si el producto ya tiene ventas o historial de inventario, la BD bloqueará el borrado para proteger tu contabilidad.
            return back()->withErrors('No puedes eliminar este producto porque ya tiene movimientos de inventario o ventas asociadas.');
        }
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