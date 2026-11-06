<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;
use App\Models\Unidad;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $marcas = Marca::orderBy('nombre')->get();
        $unidades = Unidad::orderBy('nombre')->get();
        return view('configuracion.index', compact('marcas', 'unidades'));
    }

    // --- MÉTODOS PARA MARCAS ---
    public function storeMarca(Request $request) {
        $marca = new Marca();
        $marca->nombre = strtoupper($request->nombre); // Lo guardamos en mayúsculas
        $marca->save();
        return back()->with('success', 'Marca agregada exitosamente.');
    }

    public function updateMarca(Request $request, $id) {
        $marca = Marca::findOrFail($id);
        $marca->nombre = strtoupper($request->nombre);
        $marca->save();
        return back()->with('success', 'Marca actualizada exitosamente.');
    }

    // --- MÉTODOS PARA UNIDADES ---
    public function storeUnidad(Request $request) {
        $unidad = new Unidad();
        $unidad->nombre = $request->nombre;
        $unidad->save();
        return back()->with('success', 'Unidad de medida agregada.');
    }

    public function updateUnidad(Request $request, $id) {
        $unidad = Unidad::findOrFail($id);
        $unidad->nombre = $request->nombre;
        $unidad->save();
        return back()->with('success', 'Unidad de medida actualizada.');
    }

    // --- MÉTODO PARA SUBIR NUEVO QR ---
    public function updateQr(Request $request) {
        $request->validate([
            'qr_image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Máximo 2MB
        ]);
        
        // Sobrescribimos el archivo exacto que usa el Punto de Venta
        $imageName = 'qr_banco.jpeg'; 
        $request->qr_image->move(public_path('img'), $imageName);
        
        return back()->with('success', 'Código QR de pagos actualizado correctamente.');
    }
}