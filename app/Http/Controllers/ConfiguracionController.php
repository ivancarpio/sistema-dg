<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;
use App\Models\Unidad;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Configuracion;

class ConfiguracionController extends Controller
{
    public function index()
    {
        if (!auth()->check() || !auth()->user()->tienePermiso('configuracion')) {
            return redirect('/pos')->with('error', 'No tienes permiso para acceder a la configuración del sistema.');
        }

        $categorias = Categoria::withCount('productos')->orderBy('nombre')->get();
        $marcas = Marca::withCount('productos')->orderBy('nombre')->get();
        $unidades = Unidad::withCount('productos')->orderBy('nombre')->get();
        $productos = Producto::with('categoria')->orderBy('nombre', 'asc')->get();
        
        $configuraciones = Configuracion::all()->pluck('valor', 'clave')->toArray();

        return view('configuracion.index', compact('marcas', 'unidades', 'categorias', 'configuraciones', 'productos'));
    }

    // ==========================================
    // MÉTODOS PARA CATEGORÍAS
    // ==========================================
    public function asignarCategoriaMasiva(Request $request)
    {
        $request->validate([
            'productos_ids'   => 'required|array|min:1',
            'productos_ids.*' => 'exists:productos,id',
        ], [
            'productos_ids.required' => 'Debes marcar al menos un producto para aplicar los cambios.',
            'productos_ids.min'      => 'Debes marcar al menos un producto.',
        ]);

        $accion = $request->input('accion', 'asignar');
        $idCategoria = $request->input('id_categoria');

        // Si se eligió quitar categoría o dejar sin categoría
        if ($accion === 'quitar' || $idCategoria === 'sin_categoria' || $idCategoria === '0') {
            $cantidad = Producto::whereIn('id', $request->productos_ids)->update([
                'id_categoria' => null
            ]);
            return back()->with('success', "¡Operación completada! Se desvincularon {$cantidad} producto(s) de su categoría (quedaron como 'Sin Categoría').");
        }

        // Si es asignación a categoría específica
        $request->validate([
            'id_categoria' => 'required|exists:categorias,id',
        ], [
            'id_categoria.required' => 'Debes seleccionar la categoría de destino.',
        ]);

        $categoria = Categoria::findOrFail($idCategoria);
        $cantidad = Producto::whereIn('id', $request->productos_ids)->update([
            'id_categoria' => $categoria->id
        ]);

        return back()->with('success', "¡Asignación exitosa! Se vincularon {$cantidad} producto(s) a la categoría '{$categoria->nombre}'.");
    }
    public function storeCategoria(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $categoria = new Categoria();
        $categoria->nombre = trim(strtoupper($request->nombre));
        $categoria->descripcion = $request->descripcion ?? null;
        $categoria->save();

        return back()->with('success', "Categoría '{$categoria->nombre}' agregada exitosamente.");
    }

    public function updateCategoria(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $categoria = Categoria::findOrFail($id);
        $categoria->nombre = trim(strtoupper($request->nombre));
        if ($request->has('descripcion')) {
            $categoria->descripcion = $request->descripcion;
        }
        $categoria->save();

        return back()->with('success', "Categoría '{$categoria->nombre}' actualizada exitosamente.");
    }

    public function destroyCategoria($id)
    {
        $categoria = Categoria::findOrFail($id);
        $conteo = Producto::where('id_categoria', $id)->count();

        if ($conteo > 0) {
            return back()->withErrors([
                "No se puede eliminar la categoría '{$categoria->nombre}' porque tiene {$conteo} producto(s) asignado(s) en el inventario. Reasigna o elimina los productos antes de borrarla."
            ]);
        }

        $nombre = $categoria->nombre;
        $categoria->delete();

        return back()->with('success', "Categoría '{$nombre}' eliminada correctamente.");
    }

    // ==========================================
    // MÉTODOS PARA MARCAS
    // ==========================================
    public function storeMarca(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $marca = new Marca();
        $marca->nombre = trim(strtoupper($request->nombre));
        $marca->save();

        return back()->with('success', "Marca '{$marca->nombre}' agregada exitosamente.");
    }

    public function updateMarca(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $marca = Marca::findOrFail($id);
        $marca->nombre = trim(strtoupper($request->nombre));
        $marca->save();

        return back()->with('success', "Marca '{$marca->nombre}' actualizada exitosamente.");
    }

    public function destroyMarca($id)
    {
        $marca = Marca::findOrFail($id);
        $conteo = Producto::where('id_marca', $id)->count();

        if ($conteo > 0) {
            return back()->withErrors([
                "No se puede eliminar la marca '{$marca->nombre}' porque está asociada a {$conteo} producto(s) del catálogo."
            ]);
        }

        $nombre = $marca->nombre;
        $marca->delete();

        return back()->with('success', "Marca '{$nombre}' eliminada correctamente.");
    }

    // ==========================================
    // MÉTODOS PARA UNIDADES
    // ==========================================
    public function storeUnidad(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $unidad = new Unidad();
        $unidad->nombre = trim($request->nombre);
        $unidad->save();

        return back()->with('success', "Unidad de medida '{$unidad->nombre}' agregada exitosamente.");
    }

    public function updateUnidad(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $unidad = Unidad::findOrFail($id);
        $unidad->nombre = trim($request->nombre);
        $unidad->save();

        return back()->with('success', "Unidad de medida '{$unidad->nombre}' actualizada exitosamente.");
    }

    public function destroyUnidad($id)
    {
        $unidad = Unidad::findOrFail($id);
        $conteo = Producto::where('id_unidad', $id)->count();

        if ($conteo > 0) {
            return back()->withErrors([
                "No se puede eliminar la unidad '{$unidad->nombre}' porque está asignada a {$conteo} producto(s)."
            ]);
        }

        $nombre = $unidad->nombre;
        $unidad->delete();

        return back()->with('success', "Unidad de medida '{$nombre}' eliminada correctamente.");
    }

    // ==========================================
    // AJUSTES GENERALES Y CONFIGURACIÓN QR
    // ==========================================
    public function updateAjustes(Request $request)
    {
        // Switch de QR
        $mostrarModalQr = $request->has('mostrar_modal_qr') ? '1' : '0';
        Configuracion::set('mostrar_modal_qr', $mostrarModalQr, 'Mostrar modal QR en POS');

        // Datos del negocio
        if ($request->has('nombre_empresa')) {
            Configuracion::set('nombre_empresa', $request->input('nombre_empresa'));
        }
        if ($request->has('nit_empresa')) {
            Configuracion::set('nit_empresa', $request->input('nit_empresa'));
        }
        if ($request->has('telefono_empresa')) {
            Configuracion::set('telefono_empresa', $request->input('telefono_empresa'));
        }
        if ($request->has('direccion_empresa')) {
            Configuracion::set('direccion_empresa', $request->input('direccion_empresa'));
        }
        if ($request->has('pie_ticket')) {
            Configuracion::set('pie_ticket', $request->input('pie_ticket'));
        }
        if ($request->has('stock_minimo_defecto')) {
            Configuracion::set('stock_minimo_defecto', $request->input('stock_minimo_defecto', '5'));
        }

        return back()->with('success', 'Ajustes del sistema actualizados con éxito.');
    }

    public function toggleQrModal(Request $request)
    {
        $estadoActual = Configuracion::get('mostrar_modal_qr', '1');
        $nuevoEstado = $estadoActual === '1' ? '0' : '1';
        Configuracion::set('mostrar_modal_qr', $nuevoEstado);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'nuevo_estado' => $nuevoEstado,
                'mensaje' => $nuevoEstado === '1' ? 'Modal QR activado en POS.' : 'Modal QR desactivado en POS.'
            ]);
        }

        return back()->with('success', $nuevoEstado === '1' 
            ? 'Modal QR activado: se mostrará la ventana emergente con QR al cobrar en POS.' 
            : 'Modal QR desactivado: la venta con QR se registrará sin abrir la ventana emergente.');
    }

    public function updateQr(Request $request)
    {
        $request->validate([
            'qr_image' => 'required|image|mimes:jpeg,png,jpg|max:4096', // Máximo 4MB
        ]);

        $imageName = 'qr_banco.jpeg';
        $request->qr_image->move(public_path('img'), $imageName);

        return back()->with('success', 'Imagen del Código QR de pagos actualizada correctamente.');
    }

    public function updateLogo(Request $request)
    {
        if (!auth()->check() || !auth()->user()->tienePermiso('configuracion')) {
            return redirect('/pos')->with('error', 'Acceso denegado.');
        }

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'logo.required' => 'Debes seleccionar un archivo de imagen.',
            'logo.image'    => 'El archivo debe ser una imagen válida.',
            'logo.mimes'    => 'Solo se permiten imágenes en formato PNG, JPG, JPEG o WEBP.',
            'logo.max'      => 'La imagen no debe pesar más de 5 MB.',
        ]);

        $file = $request->file('logo');
        $imgDir = public_path('img');
        if (!file_exists($imgDir)) {
            mkdir($imgDir, 0755, true);
        }

        $logoPngPath   = $imgDir . DIRECTORY_SEPARATOR . 'logo.png';
        $logoWhitePath = $imgDir . DIRECTORY_SEPARATOR . 'logo_white.png';

        $procesadoConGd = false;

        if (extension_loaded('gd')) {
            try {
                $imgInfo = @getimagesize($file->getRealPath());
                if ($imgInfo && isset($imgInfo['mime'])) {
                    $mime = $imgInfo['mime'];
                    $src = null;

                    if ($mime === 'image/jpeg') {
                        $src = @imagecreatefromjpeg($file->getRealPath());
                    } elseif ($mime === 'image/png') {
                        $src = @imagecreatefrompng($file->getRealPath());
                    } elseif ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
                        $src = @imagecreatefromwebp($file->getRealPath());
                    }

                    if ($src) {
                        $w = imagesx($src);
                        $h = imagesy($src);

                        // 1. Guardar logo.png con canal alfa transparente preservado
                        $canvasAlpha = imagecreatetruecolor($w, $h);
                        imagealphablending($canvasAlpha, false);
                        imagesavealpha($canvasAlpha, true);
                        $transparent = imagecolorallocatealpha($canvasAlpha, 255, 255, 255, 127);
                        imagefilledrectangle($canvasAlpha, 0, 0, $w, $h, $transparent);
                        imagealphablending($canvasAlpha, true);
                        imagecopy($canvasAlpha, $src, 0, 0, 0, 0, $w, $h);
                        imagepng($canvasAlpha, $logoPngPath, 6);
                        imagedestroy($canvasAlpha);

                        // 2. Guardar logo_white.png con fondo blanco puro para DomPDF / Notas de Venta
                        $canvasWhite = imagecreatetruecolor($w, $h);
                        $white = imagecolorallocate($canvasWhite, 255, 255, 255);
                        imagefilledrectangle($canvasWhite, 0, 0, $w, $h, $white);
                        imagealphablending($canvasWhite, true);
                        imagecopy($canvasWhite, $src, 0, 0, 0, 0, $w, $h);
                        imagepng($canvasWhite, $logoWhitePath, 6);
                        imagedestroy($canvasWhite);

                        imagedestroy($src);
                        $procesadoConGd = true;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback abajo si GD fallara por alguna razón
                $procesadoConGd = false;
            }
        }

        // Fallback estándar si GD no procesó la imagen
        if (!$procesadoConGd) {
            $file->move($imgDir, 'logo.png');
            copy($logoPngPath, $logoWhitePath);
        }

        // Actualizar versión de caché para forzar refresco inmediato en los navegadores
        Configuracion::set('logo_version', time(), 'Versión de caché del logo');

        return back()->with('success', '¡Logo oficial actualizado con éxito! Se aplicará automáticamente en notas de venta, login, barra lateral y en todo el sistema.');
    }
}
