<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('arqueos_caja')) {
            Schema::create('arqueos_caja', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_usuario'); // Cajero responsable
                $table->dateTime('fecha_apertura');
                $table->dateTime('fecha_cierre')->nullable();
                
                // Fondos iniciales en gaveta
                $table->decimal('monto_inicial', 12, 2)->default(0.00);
                
                // Totales calculados por el sistema durante la sesión
                $table->decimal('total_efectivo_sistema', 12, 2)->default(0.00);
                $table->decimal('total_qr_sistema', 12, 2)->default(0.00);
                $table->decimal('total_transferencia_sistema', 12, 2)->default(0.00);
                $table->decimal('total_egresos', 12, 2)->default(0.00); // Gastos y Devoluciones
                
                // Declaración física al momento del cierre
                $table->decimal('monto_fisico_declarado', 12, 2)->nullable();
                $table->decimal('diferencia', 12, 2)->nullable(); // Declarado - Esperado en caja
                
                // Estado e inmutabilidad
                $table->enum('estado', ['abierto', 'cerrado'])->default('abierto');
                $table->text('observaciones')->nullable();
                
                $table->timestamps();

                $table->foreign('id_usuario')->references('id')->on('users')->onDelete('restrict');
                $table->index(['estado', 'fecha_apertura']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('arqueos_caja');
    }
};
