<?php

namespace App\Console\Commands;

use App\Services\TipoCambioService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ActualizarTipoCambio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tipo-cambio:actualizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el tipo de cambio oficial BOB/PEN desde la API de Ragnar Capital';

    /**
     * Execute the console command.
     */
    public function handle(TipoCambioService $service)
    {
        $this->info("Iniciando consulta de tipo de cambio en Ragnar Capital...");

        try {
            $registro = $service->actualizarDesdeApi();

            if ($registro) {
                $this->info("✓ Tipo de cambio actualizado con éxito:");
                $this->line("  - Venta  (BOB -> PEN): 1 Sol = {$registro->tasa_venta} Bs.");
                $this->line("  - Compra (PEN -> BOB): 1 Sol = {$registro->tasa_compra} Bs.");
                $this->line("  - Fuente: {$registro->fuente}");
                $this->line("  - Fecha/Hora: {$registro->consultado_en}");
                return Command::SUCCESS;
            } else {
                $this->warn("⚠ No se pudo obtener respuesta de la API. Se conserva el último registro disponible.");
                return Command::SUCCESS;
            }
        } catch (\Throwable $e) {
            Log::error("Error en comando tipo-cambio:actualizar: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->error("Error al actualizar tipo de cambio: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
