<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de Arqueos de Caja.
     * Cada arqueo agrupa un conjunto de ventas y registra los totales por método de pago.
     */
    public function up(): void
    {
        Schema::create('arqueos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');       // Quién creó el arqueo
            $table->decimal('total_efectivo', 12, 2)->default(0); // Suma de ventas en efectivo
            $table->decimal('total_qr', 12, 2)->default(0);       // Suma de ventas en QR
            $table->decimal('total_general', 12, 2)->default(0);   // Efectivo + QR
            $table->integer('cantidad_ventas')->default(0);         // Número de ventas incluidas
            $table->text('observaciones')->nullable();              // Notas opcionales del cajero
            $table->timestamps();                                   // created_at / updated_at

            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('cascade');
        });

        // Tabla pivote: qué ventas pertenecen a qué arqueo
        Schema::create('arqueo_venta', function (Blueprint $table) {
            $table->unsignedBigInteger('arqueo_id');
            $table->unsignedBigInteger('venta_id');
            $table->primary(['arqueo_id', 'venta_id']);
            $table->foreign('arqueo_id')->references('id')->on('arqueos')->onDelete('cascade');
        });

        // Agregar columna arqueo_id a ventas para saber si ya fueron arqueadas
        Schema::table('ventas', function (Blueprint $table) {
            $table->unsignedBigInteger('arqueo_id')->nullable()->after('metodo_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('arqueo_id');
        });
        Schema::dropIfExists('arqueo_venta');
        Schema::dropIfExists('arqueos');
    }
};
