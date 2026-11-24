<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('categorias_gasto')) {
            Schema::create('categorias_gasto', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100);
                $table->string('icono', 50)->nullable()->default('bi-tag');
                $table->string('color', 20)->nullable()->default('#0d6efd');
                $table->text('descripcion')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });

            // Semillas iniciales por defecto
            $defaults = [
                ['nombre' => 'Combustible', 'icono' => 'bi-fuel-pump', 'color' => '#f59e0b', 'descripcion' => 'Gasolina y diésel para transporte de mercadería'],
                ['nombre' => 'Envíos a Clientes / Delivery', 'icono' => 'bi-truck', 'color' => '#06b6d4', 'descripcion' => 'Fletes, encomiendas y entregas'],
                ['nombre' => 'Servicios Básicos', 'icono' => 'bi-lightning-charge', 'color' => '#ef4444', 'descripcion' => 'Luz, agua, internet, telefonía'],
                ['nombre' => 'Insumos y Embalaje', 'icono' => 'bi-box-seam', 'color' => '#8b5cf6', 'descripcion' => 'Cintas, bolsas, cartones, papelería'],
                ['nombre' => 'Mantenimiento', 'icono' => 'bi-wrench-adjustable', 'color' => '#64748b', 'descripcion' => 'Reparación de vehículos, herramientas o tienda'],
                ['nombre' => 'Alimentación / Viáticos', 'icono' => 'bi-cup-hot', 'color' => '#10b981', 'descripcion' => 'Refrigerios de personal y viáticos autorizados'],
                ['nombre' => 'Otros', 'icono' => 'bi-three-dots', 'color' => '#6b7280', 'descripcion' => 'Gastos varios menores no categorizados'],
            ];

            $now = now();
            foreach ($defaults as $item) {
                DB::table('categorias_gasto')->insert([
                    'nombre'      => $item['nombre'],
                    'icono'       => $item['icono'],
                    'color'       => $item['color'],
                    'descripcion' => $item['descripcion'],
                    'activo'      => true,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_gasto');
    }
};
