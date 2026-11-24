<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ampliar el ENUM para permitir egreso_retiro de forma no destructiva
        DB::statement("ALTER TABLE movimientos_caja MODIFY COLUMN tipo ENUM('ingreso_extra', 'egreso_gasto', 'egreso_devolucion', 'egreso_retiro') NOT NULL");

        // 2. Columna de enlace directo a gastos si no existe
        if (Schema::hasTable('movimientos_caja') && !Schema::hasColumn('movimientos_caja', 'gasto_id')) {
            Schema::table('movimientos_caja', function (Blueprint $table) {
                $table->unsignedBigInteger('gasto_id')->nullable()->after('id_venta');
                $table->index('gasto_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('movimientos_caja') && Schema::hasColumn('movimientos_caja', 'gasto_id')) {
            Schema::table('movimientos_caja', function (Blueprint $table) {
                $table->dropIndex(['gasto_id']);
                $table->dropColumn('gasto_id');
            });
        }
        DB::statement("ALTER TABLE movimientos_caja MODIFY COLUMN tipo ENUM('ingreso_extra', 'egreso_gasto', 'egreso_devolucion') NOT NULL");
    }
};
