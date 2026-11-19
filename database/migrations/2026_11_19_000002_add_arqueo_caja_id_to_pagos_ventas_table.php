<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos_ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('pagos_ventas', 'arqueo_caja_id')) {
                $table->unsignedBigInteger('arqueo_caja_id')->nullable()->after('id_venta');
                $table->foreign('arqueo_caja_id')->references('id')->on('arqueos_caja')->onDelete('set null');
            }

            if (!Schema::hasColumn('pagos_ventas', 'referencia')) {
                $table->string('referencia', 100)->nullable()->after('metodo_pago');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pagos_ventas', function (Blueprint $table) {
            if (Schema::hasColumn('pagos_ventas', 'arqueo_caja_id')) {
                $table->dropForeign(['arqueo_caja_id']);
                $table->dropColumn('arqueo_caja_id');
            }
            if (Schema::hasColumn('pagos_ventas', 'referencia')) {
                $table->dropColumn('referencia');
            }
        });
    }
};
