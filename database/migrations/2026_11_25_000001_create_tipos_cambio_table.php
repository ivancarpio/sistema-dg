<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tipos_cambio', function (Blueprint $table) {
            $table->id();
            $table->string('moneda_origen', 10)->default('BOB');
            $table->string('moneda_destino', 10)->default('PEN');
            $table->decimal('tasa_venta', 10, 4); // Factor para venta (BOB -> PEN / Sol a Bs)
            $table->decimal('tasa_compra', 10, 4)->nullable(); // Factor para compra
            $table->string('fuente', 50)->default('ragnar_api'); // 'ragnar_api', 'manual'
            $table->dateTime('consultado_en');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observacion')->nullable();
            $table->timestamps();

            // Índices para consultas rápidas del último tipo de cambio
            $table->index(['moneda_origen', 'moneda_destino', 'consultado_en']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_cambio');
    }
};
