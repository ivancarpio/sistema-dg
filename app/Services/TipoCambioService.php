<?php

namespace App\Services;

use App\Models\TipoCambio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TipoCambioService
{
    const API_URL = 'https://odoo.ragnarcapital.pe/v1/api/ragnar/exchange';

    /**
     * Obtiene el tipo de cambio más reciente en base de datos.
     * Si no existe ninguno, intenta consultar la API o devuelve valores de contingencia.
     */
    public function actual(string $origen = 'BOB', string $destino = 'PEN'): TipoCambio
    {
        $ultimo = TipoCambio::where('moneda_origen', $origen)
            ->where('moneda_destino', $destino)
            ->orderBy('consultado_en', 'desc')
            ->first();

        if ($ultimo) {
            return $ultimo;
        }

        // Si la tabla está vacía, intentamos sincronizar
        try {
            $nuevo = $this->actualizarDesdeApi();
            if ($nuevo) {
                return $nuevo;
            }
        } catch (\Throwable $e) {
            Log::warning("TipoCambioService: No se pudo obtener tasa inicial de API: " . $e->getMessage());
        }

        // Si falló y no hay nada en BD, creamos un registro inicial de contingencia
        return TipoCambio::create([
            'moneda_origen' => $origen,
            'moneda_destino' => $destino,
            'tasa_venta' => 3.6000,
            'tasa_compra' => 3.3700,
            'fuente' => 'manual',
            'consultado_en' => Carbon::now(),
            'observacion' => 'Tasa inicial de contingencia del sistema',
        ]);
    }

    /**
     * Consulta la API externa de Ragnar Capital para Venta y Compra.
     * 
     * @return array{tasa_venta: float, tasa_compra: float}|null
     */
    public function consultarApi(string $origen = 'BOB', string $destino = 'PEN'): ?array
    {
        try {
            // 1. Consulta Tasa VENTA (BOB -> PEN)
            $responseVenta = Http::withoutVerifying()
                ->timeout(10)
                ->retry(2, 500)
                ->withHeaders([
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json',
                ])
                ->post(self::API_URL, [
                    'jsonrpc' => '2.0',
                    'id' => 'venta',
                    'params' => [
                        'tipo_cambio' => 'venta',
                        'monto' => 1,
                        'moneda_origen' => $origen,
                        'moneda_destino' => $destino,
                        'pais_destino' => 'Perú',
                    ],
                ]);

            if (!$responseVenta->successful()) {
                Log::error("TipoCambioService: HTTP error en API Venta: " . $responseVenta->status());
                return null;
            }

            $dataVenta = $responseVenta->json();
            $resultVenta = $dataVenta['result'] ?? null;

            if (!$resultVenta || empty($resultVenta['estado']) || !isset($resultVenta['factor_cambio'])) {
                Log::error("TipoCambioService: Respuesta inválida en API Venta", ['data' => $dataVenta]);
                return null;
            }

            $factorVenta = (float) $resultVenta['factor_cambio'];
            if ($factorVenta <= 0) {
                Log::error("TipoCambioService: Factor venta menor o igual a 0: " . $factorVenta);
                return null;
            }

            // 2. Consulta Tasa COMPRA (PEN -> BOB)
            $factorCompra = null;
            try {
                $responseCompra = Http::withoutVerifying()
                    ->timeout(10)
                    ->retry(2, 500)
                    ->withHeaders([
                        'Content-Type' => 'application/json; charset=utf-8',
                        'Accept' => 'application/json',
                    ])
                    ->post(self::API_URL, [
                        'jsonrpc' => '2.0',
                        'id' => 'compra',
                        'params' => [
                            'tipo_cambio' => 'compra',
                            'monto' => 1,
                            'moneda_origen' => $destino,
                            'moneda_destino' => $origen,
                            'pais_destino' => 'Perú',
                        ],
                    ]);

                if ($responseCompra->successful()) {
                    $dataCompra = $responseCompra->json();
                    $resultCompra = $dataCompra['result'] ?? null;
                    if ($resultCompra && !empty($resultCompra['estado']) && isset($resultCompra['factor_cambio'])) {
                        $fCompra = (float) $resultCompra['factor_cambio'];
                        if ($fCompra > 0) {
                            $factorCompra = $fCompra;
                        }
                    }
                }
            } catch (\Throwable $eCompra) {
                Log::warning("TipoCambioService: Advertencia al obtener tasa compra: " . $eCompra->getMessage());
            }

            return [
                'tasa_venta' => $factorVenta,
                'tasa_compra' => $factorCompra ?? $factorVenta,
            ];
        } catch (\Throwable $e) {
            Log::error("TipoCambioService: Excepción consultando API Ragnar: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualiza la tasa desde la API de Ragnar Capital registrándola en la BD.
     * Aplica lógica de deduplicación (si la tasa de hoy no varió, actualiza timestamp).
     */
    public function actualizarDesdeApi(?int $userId = null, string $origen = 'BOB', string $destino = 'PEN'): ?TipoCambio
    {
        $tasas = $this->consultarApi($origen, $destino);

        if (!$tasas || empty($tasas['tasa_venta'])) {
            Log::warning("TipoCambioService: No se pudieron obtener tasas de la API. Se mantiene el último registro existente.");
            return TipoCambio::where('moneda_origen', $origen)
                ->where('moneda_destino', $destino)
                ->orderBy('consultado_en', 'desc')
                ->first();
        }

        $hoy = Carbon::today();
        $ultimoRegistro = TipoCambio::where('moneda_origen', $origen)
            ->where('moneda_destino', $destino)
            ->orderBy('consultado_en', 'desc')
            ->first();

        // Si ya hay un registro de hoy y las tasas son idénticas, actualizamos consultado_en
        if ($ultimoRegistro && 
            Carbon::parse($ultimoRegistro->consultado_en)->isSameDay($hoy) &&
            abs((float)$ultimoRegistro->tasa_venta - (float)$tasas['tasa_venta']) < 0.0001 &&
            abs((float)$ultimoRegistro->tasa_compra - (float)$tasas['tasa_compra']) < 0.0001) {
            
            $ultimoRegistro->update([
                'consultado_en' => Carbon::now(),
            ]);

            return $ultimoRegistro;
        }

        // Crear un nuevo registro histórico
        return TipoCambio::create([
            'moneda_origen' => $origen,
            'moneda_destino' => $destino,
            'tasa_venta' => $tasas['tasa_venta'],
            'tasa_compra' => $tasas['tasa_compra'],
            'fuente' => 'ragnar_api',
            'consultado_en' => Carbon::now(),
            'user_id' => $userId,
            'observacion' => 'Actualización automática Ragnar Capital',
        ]);
    }

    /**
     * Convierte de Soles (PEN) a Bolivianos (BOB)
     * Ej: S/ 100.00 con factor 3.60 -> Bs. 360.00
     */
    public function convertirPenABob(float $montoPen, string $tipo = 'venta'): float
    {
        $actual = $this->actual();
        $tasa = ($tipo === 'compra' && $actual->tasa_compra) ? (float)$actual->tasa_compra : (float)$actual->tasa_venta;

        return round($montoPen * $tasa, 2);
    }

    /**
     * Convierte de Bolivianos (BOB) a Soles (PEN)
     * Ej: Bs. 360.00 con factor 3.60 -> S/ 100.00
     */
    public function convertirBobAPen(float $montoBob, string $tipo = 'venta'): float
    {
        $actual = $this->actual();
        $tasa = ($tipo === 'compra' && $actual->tasa_compra) ? (float)$actual->tasa_compra : (float)$actual->tasa_venta;

        if ($tasa <= 0) {
            return 0.00;
        }

        return round($montoBob / $tasa, 2);
    }
}
