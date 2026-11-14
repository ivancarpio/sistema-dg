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
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio_cliente', 10, 2)->nullable()->default(null)->after('precio_venta');
        });

        if (Schema::hasTable('ventas') && !Schema::hasColumn('ventas', 'tipo_tarifa')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->string('tipo_tarifa', 50)->default('Ocasional')->after('metodo_pago');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('precio_cliente');
        });

        if (Schema::hasTable('ventas') && Schema::hasColumn('ventas', 'tipo_tarifa')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->dropColumn('tipo_tarifa');
            });
        }
    }
};
