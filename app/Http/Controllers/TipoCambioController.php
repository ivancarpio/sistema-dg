<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\TipoCambio;
use App\Services\TipoCambioService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TipoCambioController extends Controller
{
    protected TipoCambioService $tipoCambioService;

    public function __construct(TipoCambioService $tipoCambioService)
    {
        $this->tipoCambioService = $tipoCambioService;
    }

    /**
     * Devuelve la tasa actual en JSON
     */
    public function actual(): JsonResponse
    {
        $actual = $this->tipoCambioService->actual();

        return response()->json([
            'success' => true,
            'tasa_venta' => (float)$actual->tasa_venta,
            'tasa_compra' => (float)$actual->tasa_compra,
            'consultado_en' => Carbon::parse($actual->consultado_en)->format('d/m/Y H:i'),
            'hace_cuanto' => Carbon::parse($actual->consultado_en)->diffForHumans(),
            'fuente' => $actual->fuente,
        ]);
    }

    /**
     * Fuerza la sincronización con la API de Ragnar Capital
     */
    public function actualizarAjax(Request $request): JsonResponse
    {
        try {
            $registro = $this->tipoCambioService->actualizarDesdeApi(auth()->id());

            if (!$registro) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo sincronizar con Ragnar Capital. Se mantiene la última cotización guardada.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tipo de cambio sincronizado en tiempo real con Ragnar Capital.',
                'tasa_venta' => (float)$registro->tasa_venta,
                'tasa_compra' => (float)$registro->tasa_compra,
                'consultado_en' => Carbon::parse($registro->consultado_en)->format('d/m/Y H:i:s'),
                'hace_cuanto' => 'Hace un momento',
                'fuente' => $registro->fuente,
            ]);
        } catch (\Throwable $e) {
            Log::error("Error actualizando tipo de cambio via AJAX: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al conectar con la API: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Convierte montos en tiempo real entre Soles y Bolivianos
     */
    public function convertirAjax(Request $request): JsonResponse
    {
        $request->validate([
            'monto' => 'required|numeric|min:0',
            'direccion' => 'required|in:pen_a_bob,bob_a_pen',
            'tipo' => 'nullable|in:venta,compra',
        ]);

        $monto = (float)$request->input('monto');
        $direccion = $request->input('direccion');
        $tipo = $request->input('tipo', 'venta');

        if ($direccion === 'pen_a_bob') {
            $resultado = $this->tipoCambioService->convertirPenABob($monto, $tipo);
        } else {
            $resultado = $this->tipoCambioService->convertirBobAPen($monto, $tipo);
        }

        $actual = $this->tipoCambioService->actual();

        return response()->json([
            'success' => true,
            'monto_origen' => $monto,
            'monto_convertido' => $resultado,
            'tasa_aplicada' => $tipo === 'compra' ? (float)$actual->tasa_compra : (float)$actual->tasa_venta,
            'tipo' => $tipo,
            'direccion' => $direccion,
        ]);
    }
}
