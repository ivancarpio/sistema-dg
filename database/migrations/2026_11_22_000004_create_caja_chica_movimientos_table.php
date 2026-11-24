<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('caja_chica_movimientos')) {
            Schema::create('caja_chica_movimientos', function (Blueprint $table) {
                $table->id();
                $table->dateTime('fecha');
                $table->enum('tipo', ['apertura_o_reposicion', 'gasto', 'reversion_gasto', 'ajuste_ingreso', 'ajuste_egreso']);
                $table->decimal('monto', 12, 2);
                $table->decimal('saldo_resultante', 12, 2);
                $table->string('motivo', 255);
                $table->unsignedBigInteger('gasto_id')->nullable();
                $table->unsignedBigInteger('turno_caja_id')->nullable();
                $table->unsignedBigInteger('movimiento_caja_id')->nullable();
                $table->string('comprobante', 100)->nullable();
                $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
                $table->timestamps();

                $table->foreign('gasto_id')->references('id')->on('gastos')->onDelete('set null');
                $table->foreign('turno_caja_id')->references('id')->on('arqueos_caja')->onDelete('set null');
                $table->foreign('movimiento_caja_id')->references('id')->on('movimientos_caja')->onDelete('set null');

                $table->index(['fecha', 'tipo']);
                $table->index('gasto_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_chica_movimientos');
    }
};
