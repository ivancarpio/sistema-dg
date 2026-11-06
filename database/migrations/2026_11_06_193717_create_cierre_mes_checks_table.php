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
        Schema::create('cierre_mes_checks', function (Blueprint $table) {
            $table->id();
            $table->string('mes', 7); // Formato 'YYYY-MM', ej: '2026-11'
            $table->unsignedInteger('id_producto');
            $table->boolean('verificado')->default(true);
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->timestamp('fecha_verificacion')->nullable();
            $table->timestamps();

            $table->unique(['mes', 'id_producto']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cierre_mes_checks');
    }
};
