<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('movimientos_caja')) {
            Schema::create('movimientos_caja', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('arqueo_caja_id'); // Sesión activa de caja
                $table->unsignedBigInteger('id_usuario');     // Usuario que autoriza/realiza
                $table->integer('id_venta')->nullable();      // Coincide con ventas.id (int)
                
                $table->enum('tipo', ['ingreso_extra', 'egreso_gasto', 'egreso_devolucion']);
                $table->string('metodo_pago', 30)->default('Efectivo');
                $table->decimal('monto', 12, 2);
                $table->text('motivo');
                $table->dateTime('fecha');
                $table->timestamps();

                $table->foreign('arqueo_caja_id')->references('id')->on('arqueos_caja')->onDelete('restrict');
                $table->foreign('id_usuario')->references('id')->on('users')->onDelete('restrict');
                $table->foreign('id_venta')->references('id')->on('ventas')->onDelete('set null');
                
                $table->index(['arqueo_caja_id', 'tipo']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};
