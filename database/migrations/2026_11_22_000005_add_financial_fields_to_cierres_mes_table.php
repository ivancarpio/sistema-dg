<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cierres_mes')) {
            Schema::table('cierres_mes', function (Blueprint $table) {
                if (!Schema::hasColumn('cierres_mes', 'total_ingresos_ventas')) {
                    $table->decimal('total_ingresos_ventas', 12, 2)->default(0)->after('porcentaje');
                }
                if (!Schema::hasColumn('cierres_mes', 'total_costo_ventas')) {
                    $table->decimal('total_costo_ventas', 12, 2)->default(0)->after('total_ingresos_ventas');
                }
                if (!Schema::hasColumn('cierres_mes', 'total_gastos_operativos')) {
                    $table->decimal('total_gastos_operativos', 12, 2)->default(0)->after('total_costo_ventas');
                }
                if (!Schema::hasColumn('cierres_mes', 'utilidad_neta')) {
                    $table->decimal('utilidad_neta', 12, 2)->default(0)->after('total_gastos_operativos');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cierres_mes')) {
            Schema::table('cierres_mes', function (Blueprint $table) {
                $columns = ['total_ingresos_ventas', 'total_costo_ventas', 'total_gastos_operativos', 'utilidad_neta'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('cierres_mes', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
