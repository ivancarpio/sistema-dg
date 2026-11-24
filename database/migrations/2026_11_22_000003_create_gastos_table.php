<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('gastos')) {
            Schema::create('gastos', function (Blueprint $table) {
                $table->id();
                $table->dateTime('fecha');
                $table->foreignId('categoria_gasto_id')->constrained('categorias_gasto')->onDelete('restrict');
                $table->string('descripcion', 255);
                $table->decimal('monto', 12, 2);
                $table->enum('origen_pago', ['gaveta', 'caja_chica', 'banco_qr', 'gerencia']);
                $table->unsignedBigInteger('turno_caja_id')->nullable();
                $table->unsignedBigInteger('movimiento_caja_id')->nullable();
                $table->string('nro_comprobante', 100)->nullable();
                $table->text('observacion')->nullable();
                $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
                $table->timestamps();

                $table->foreign('turno_caja_id')->references('id')->on('arqueos_caja')->onDelete('set null');
                $table->foreign('movimiento_caja_id')->references('id')->on('movimientos_caja')->onDelete('set null');

                $table->index(['fecha', 'origen_pago']);
                $table->index('categoria_gasto_id');
                $table->index('turno_caja_id');
                $table->index('movimiento_caja_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
