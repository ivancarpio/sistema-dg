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
            if (!Schema::hasColumn('productos', 'id_unidad_secundaria')) {
                $table->integer('id_unidad_secundaria')->nullable()->after('id_unidad');
                $table->foreign('id_unidad_secundaria')->references('id')->on('unidades')->nullOnDelete();
            }
            if (!Schema::hasColumn('productos', 'factor_conversion')) {
                $table->decimal('factor_conversion', 10, 2)->nullable()->default(1.00)->after('id_unidad_secundaria');
            }
            if (!Schema::hasColumn('productos', 'precio_venta_secundario')) {
                $table->decimal('precio_venta_secundario', 10, 2)->nullable()->default(null)->after('precio_cliente');
            }
            if (!Schema::hasColumn('productos', 'precio_cliente_secundario')) {
                $table->decimal('precio_cliente_secundario', 10, 2)->nullable()->default(null)->after('precio_venta_secundario');
            }
        });

        Schema::table('detalle_ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('detalle_ventas', 'unidad_medida')) {
                $table->string('unidad_medida', 50)->nullable()->after('id_producto');
            }
            if (!Schema::hasColumn('detalle_ventas', 'factor_conversion')) {
                $table->decimal('factor_conversion', 10, 2)->default(1.00)->after('unidad_medida');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasColumn('productos', 'id_unidad_secundaria')) {
                $table->dropForeign(['id_unidad_secundaria']);
                $table->dropColumn('id_unidad_secundaria');
            }
            if (Schema::hasColumn('productos', 'factor_conversion')) {
                $table->dropColumn('factor_conversion');
            }
            if (Schema::hasColumn('productos', 'precio_venta_secundario')) {
                $table->dropColumn('precio_venta_secundario');
            }
            if (Schema::hasColumn('productos', 'precio_cliente_secundario')) {
                $table->dropColumn('precio_cliente_secundario');
            }
        });

        Schema::table('detalle_ventas', function (Blueprint $table) {
            if (Schema::hasColumn('detalle_ventas', 'unidad_medida')) {
                $table->dropColumn('unidad_medida');
            }
            if (Schema::hasColumn('detalle_ventas', 'factor_conversion')) {
                $table->dropColumn('factor_conversion');
            }
        });
    }
};
