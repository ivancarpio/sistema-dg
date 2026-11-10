<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cierres_mes', function (Blueprint $table) {
            $table->id();
            $table->string('mes', 7); // e.g. '2026-11'
            $table->integer('total_productos')->default(0);
            $table->integer('total_ventas')->default(0);
            $table->integer('total_stock')->default(0);
            $table->integer('verificados_count')->default(0);
            $table->integer('porcentaje')->default(0);
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_cierre')->nullable();
            $table->text('notas')->nullable();
            $table->longText('snapshot_items')->nullable(); // JSON con las filas de productos al cerrar
            $table->string('estado', 20)->default('Cerrado'); // 'Cerrado', 'Reabierto'
            $table->timestamps();

            $table->unique('mes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierres_mes');
    }
};
