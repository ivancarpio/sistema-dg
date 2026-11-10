<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarDatosSistema extends Command
{
    protected $signature = 'sistema:limpiar-datos
                            {--solo-ventas : Solo limpia ventas y detalle_ventas}
                            {--solo-inventario : Solo limpia movimientos de inventario}
                            {--solo-arqueos : Solo limpia arqueos}
                            {--forzar : No pide confirmación (usar con precaución)}';

    protected $description = 'Limpia datos operativos del sistema: ventas, movimientos de inventario, arqueos y checks de cierre. NO elimina productos, usuarios ni configuración.';

    public function handle(): int
    {
        $this->newLine();
        $this->line("╔══════════════════════════════════════════════╗");
        $this->line("║   LIMPIEZA DE DATOS OPERATIVOS - SISTEMA DG  ║");
        $this->line("╚══════════════════════════════════════════════╝");
        $this->newLine();

        $this->comment("⚠️  Este comando eliminará:");
        $this->line("   • Historial de Ventas (ventas + detalle_ventas)");
        $this->line("   • Movimientos de Inventario (historial de ajustes)");
        $this->line("   • Arqueos de Caja (arqueos + arqueo_venta)");
        $this->line("   • Checks de Cierre de Mes");
        $this->newLine();
        $this->comment("✅  NO se eliminará:");
        $this->line("   • Productos (con sus precios, códigos y stock actual)");
        $this->line("   • Usuarios");
        $this->line("   • Categorías, Marcas, Unidades");
        $this->line("   • Configuración del sistema");
        $this->newLine();

        if (!$this->option('forzar')) {
            if (!$this->confirm('¿Estás seguro de que deseas continuar? Esta acción NO se puede deshacer.', false)) {
                $this->warn("Operación cancelada.");
                return self::SUCCESS;
            }

            $confirmacion = $this->ask('Escribe CONFIRMAR para proceder');
            if ($confirmacion !== 'CONFIRMAR') {
                $this->warn("Texto incorrecto. Operación cancelada.");
                return self::SUCCESS;
            }
        }

        $this->newLine();
        $this->info("Iniciando limpieza...");
        $this->newLine();

        $soloVentas     = $this->option('solo-ventas');
        $soloInventario = $this->option('solo-inventario');
        $soloArqueos    = $this->option('solo-arqueos');
        $limpiarTodo    = !$soloVentas && !$soloInventario && !$soloArqueos;

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // 1. ARQUEOS
            if ($limpiarTodo || $soloArqueos) {
                $this->line("  🗑  Limpiando Arqueos de Caja...");
                $arqueos = DB::table('arqueos')->count();
                $arqueoVentas = DB::table('arqueo_venta')->count();

                DB::table('arqueo_venta')->truncate();
                DB::table('arqueos')->truncate();

                if (DB::getSchemaBuilder()->hasColumn('ventas', 'arqueo_id')) {
                    DB::table('ventas')->update(['arqueo_id' => null]);
                }

                $this->line("     → Eliminados: $arqueos arqueos, $arqueoVentas relaciones");
            }

            // 2. VENTAS
            if ($limpiarTodo || $soloVentas) {
                $this->line("  🗑  Limpiando Historial de Ventas...");
                $ventas = DB::table('ventas')->count();
                $detalles = DB::table('detalle_ventas')->count();

                DB::table('detalle_ventas')->truncate();
                DB::table('ventas')->truncate();

                $this->line("     → Eliminadas: $ventas ventas, $detalles líneas de detalle");
            }

            // 3. MOVIMIENTOS DE INVENTARIO
            if ($limpiarTodo || $soloInventario) {
                $this->line("  🗑  Limpiando Movimientos de Inventario...");
                $movimientos = DB::table('movimientos')->count();
                DB::table('movimientos')->truncate();

                $this->line("     → Eliminados: $movimientos movimientos");
            }

            // 4. CHECKS DE CIERRE DE MES
            if ($limpiarTodo) {
                $this->line("  🗑  Limpiando Checks de Cierre de Mes...");
                if (DB::getSchemaBuilder()->hasTable('cierre_mes_checks')) {
                    $checks = DB::table('cierre_mes_checks')->count();
                    DB::table('cierre_mes_checks')->truncate();
                    $this->line("     → Eliminados: $checks checks");
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->error("Error durante la limpieza: " . $e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();
        $this->info("════════════════════════════════════════════");
        $this->info("  ✅ LIMPIEZA COMPLETADA EXITOSAMENTE");
        $this->info("════════════════════════════════════════════");
        $this->line("  • Historial de ventas: 0 registros");
        $this->line("  • Movimientos de inventario: 0 registros");
        $this->line("  • Arqueos de caja: 0 registros");
        $this->line("  • Dashboard y Reportes: en 0 Bs.");
        $this->line("  • Catálogo de productos: intacto");
        $this->newLine();

        return self::SUCCESS;
    }
}
