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
        ini_set('memory_limit', '512M');
        $productos = Producto::with([
            'marca:id,nombre', 
            'unidad:id,nombre', 
            'unidadSecundaria:id,nombre', 
            'categoria:id,nombre'
        ])->orderBy('nombre', 'asc')->get();

        $marcas = Marca::all();
        $unidades = Unidad::all();
        $categorias = Categoria::orderBy('nombre')->get();

        // Estadísticas clave calculadas directamente en MySQL (ultrarrápido, ~2ms y 0 saturación de RAM)
        $stats = Producto::selectRaw("
            COUNT(*) as total_productos,
            COALESCE(SUM(stock), 0) as total_stock,
            COALESCE(SUM(CASE WHEN COALESCE(stock, 0) <= CASE WHEN COALESCE(stock_minimo, 0) > 0 THEN stock_minimo ELSE 5 END THEN 1 ELSE 0 END), 0) as criticos,
            COALESCE(SUM(CASE WHEN COALESCE(stock, 0) > CASE WHEN COALESCE(stock_minimo, 0) > 0 THEN stock_minimo ELSE 5 END 
                               AND COALESCE(stock, 0) <= GREATEST(CASE WHEN COALESCE(stock_minimo, 0) > 0 THEN stock_minimo ELSE 5 END * 2, CASE WHEN COALESCE(stock_minimo, 0) > 0 THEN stock_minimo ELSE 5 END + 5) THEN 1 ELSE 0 END), 0) as alerta,
            COALESCE(SUM(CASE WHEN COALESCE(stock, 0) <= 0 THEN 1 ELSE 0 END), 0) as agotados,
            COALESCE(SUM(COALESCE(stock, 0) * COALESCE(precio_venta, 0)), 0) as valor_inventario
        ")->first();

        $totalProductos = $stats->total_productos ?? 0;
        $totalStockUnidades = $stats->total_stock ?? 0;
        $productosCriticos = $stats->criticos ?? 0;
        $productosAlerta = $stats->alerta ?? 0;
        $productosAgotados = $stats->agotados ?? 0;
        $productosStockBajo = $productosCriticos;
        $valorInventario = $stats->valor_inventario ?? 0;

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
            'codigo'                  => 'required|unique:productos,codigo',
            'nombre'                  => 'required',
            'precio_venta'            => 'required|numeric|min:0',
            'precio_cliente'          => 'nullable|numeric|min:0',
            'costo'                   => 'nullable|numeric|min:0',
            'id_unidad_secundaria'    => 'nullable|exists:unidades,id',
            'factor_conversion'       => 'nullable|numeric|min:0.01',
            'precio_venta_secundario' => 'nullable|numeric|min:0',
            'precio_cliente_secundario' => 'nullable|numeric|min:0',
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
        
        // Unidad secundaria / empaque
        if ($request->filled('id_unidad_secundaria')) {
            $producto->id_unidad_secundaria = intval($request->id_unidad_secundaria);
            $producto->factor_conversion = ($request->filled('factor_conversion') && is_numeric($request->factor_conversion) && floatval($request->factor_conversion) > 0)
                ? floatval($request->factor_conversion)
                : 1.00;

            $pvs = ($request->filled('precio_venta_secundario') && is_numeric($request->precio_venta_secundario)) ? floatval($request->precio_venta_secundario) : null;
            $pcs = ($request->filled('precio_cliente_secundario') && is_numeric($request->precio_cliente_secundario)) ? floatval($request->precio_cliente_secundario) : null;

            if (($pvs === null || $pvs <= 0) && ($pcs !== null && $pcs > 0)) {
                $pvs = $pcs;
            } elseif (($pcs === null || $pcs <= 0) && ($pvs !== null && $pvs > 0)) {
                $pcs = $pvs;
            }

            $producto->precio_venta_secundario = $pvs;
            $producto->precio_cliente_secundario = $pcs;
        } else {
            $producto->id_unidad_secundaria = null;
            $producto->factor_conversion = 1.00;
            $producto->precio_venta_secundario = null;
            $producto->precio_cliente_secundario = null;
        }
        
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
            'codigo'                  => 'required|unique:productos,codigo,' . $id,
            'nombre'                  => 'required',
            'precio_venta'            => 'required|numeric|min:0',
            'precio_cliente'          => 'nullable|numeric|min:0',
            'costo'                   => 'nullable|numeric|min:0',
            'id_unidad_secundaria'    => 'nullable|exists:unidades,id',
            'factor_conversion'       => 'nullable|numeric|min:0.01',
            'precio_venta_secundario' => 'nullable|numeric|min:0',
            'precio_cliente_secundario' => 'nullable|numeric|min:0',
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
        
        // Unidad secundaria / empaque
        if ($request->filled('id_unidad_secundaria')) {
            $producto->id_unidad_secundaria = intval($request->id_unidad_secundaria);
            $producto->factor_conversion = ($request->filled('factor_conversion') && is_numeric($request->factor_conversion) && floatval($request->factor_conversion) > 0)
                ? floatval($request->factor_conversion)
                : 1.00;

            $pvs = ($request->filled('precio_venta_secundario') && is_numeric($request->precio_venta_secundario)) ? floatval($request->precio_venta_secundario) : null;
            $pcs = ($request->filled('precio_cliente_secundario') && is_numeric($request->precio_cliente_secundario)) ? floatval($request->precio_cliente_secundario) : null;

            if (($pvs === null || $pvs <= 0) && ($pcs !== null && $pcs > 0)) {
                $pvs = $pcs;
            } elseif (($pcs === null || $pcs <= 0) && ($pvs !== null && $pvs > 0)) {
                $pcs = $pvs;
            }

            $producto->precio_venta_secundario = $pvs;
            $producto->precio_cliente_secundario = $pcs;
        } else {
            $producto->id_unidad_secundaria = null;
            $producto->factor_conversion = 1.00;
            $producto->precio_venta_secundario = null;
            $producto->precio_cliente_secundario = null;
        }
        
        $producto->stock_minimo = $request->stock_minimo ?? 0;

        $producto->save();

        if ($request->ajax() || $request->wantsJson()) {
            $producto->load(['categoria:id,nombre', 'marca:id,nombre', 'unidad:id,nombre', 'unidadSecundaria:id,nombre']);
            return response()->json([
                'success' => true,
                'message' => '¡Producto "' . $producto->nombre . '" actualizado correctamente!',
                'producto' => [
                    'id'                        => $producto->id,
                    'nombre'                    => $producto->nombre,
                    'codigo'                    => $producto->codigo,
                    'descripcion'               => $producto->descripcion ?? '',
                    'precio_venta'              => floatval($producto->precio_venta ?? 0),
                    'precio_cliente'            => $producto->precio_cliente !== null ? floatval($producto->precio_cliente) : null,
                    'costo'                     => floatval($producto->costo ?? 0),
                    'stock'                     => intval($producto->stock ?? 0),
                    'stock_minimo'              => intval($producto->stock_minimo ?? 0),
                    'id_categoria'              => $producto->id_categoria,
                    'categoria_nombre'          => optional($producto->categoria)->nombre,
                    'id_marca'                  => $producto->id_marca,
                    'marca_nombre'              => optional($producto->marca)->nombre,
                    'id_unidad'                 => $producto->id_unidad,
                    'unidad_nombre'             => optional($producto->unidad)->nombre,
                    'id_unidad_secundaria'      => $producto->id_unidad_secundaria,
                    'unidad_secundaria_nombre'  => optional($producto->unidadSecundaria)->nombre,
                    'factor_conversion'         => $producto->factor_conversion ? floatval($producto->factor_conversion) : 1,
                    'precio_venta_secundario'   => $producto->precio_venta_secundario !== null ? floatval($producto->precio_venta_secundario) : null,
                    'precio_cliente_secundario' => $producto->precio_cliente_secundario !== null ? floatval($producto->precio_cliente_secundario) : null,
                ]
            ]);
        }

        return back()->with('success', '¡Producto actualizado correctamente!');
    }

    // 4. Eliminar el producto (El Basurero)
    public function destroy(Request $request, $id)
    {
        try {
            $producto = Producto::findOrFail($id);

            // Verificar si tiene ventas registradas en detalle_ventas
            $tieneVentas = \Illuminate\Support\Facades\DB::table('detalle_ventas')
                ->where('id_producto', $id)
                ->exists();

            if ($tieneVentas) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No puedes eliminar este producto porque ya tiene notas de venta asociadas.'
                    ], 422);
                }
                return back()->withErrors('No puedes eliminar este producto porque ya tiene notas de venta asociadas.');
            }

            // Si tiene movimientos de inventario o checks de cierre, los limpiamos para permitir eliminar el producto
            \Illuminate\Support\Facades\DB::table('movimientos')->where('id_producto', $id)->delete();
            \Illuminate\Support\Facades\DB::table('cierre_mes_checks')->where('id_producto', $id)->delete();

            $producto->delete();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Producto eliminado del catálogo con éxito!',
                    'id'      => (int)$id
                ]);
            }

            return back()->with('success', '¡Producto eliminado del catálogo con éxito!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el producto: ' . $e->getMessage()
                ], 422);
            }
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