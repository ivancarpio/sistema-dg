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
        Schema::create('pagos_ventas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_venta');
            $table->unsignedBigInteger('id_cliente')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->decimal('monto', 10, 2);
            $table->string('metodo_pago', 30)->default('Efectivo'); // 'Efectivo', 'QR'
            $table->string('observacion', 255)->nullable();
            $table->dateTime('fecha');
            $table->timestamps();

            $table->index('id_venta');
            $table->index('id_cliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_ventas');
    }
};
