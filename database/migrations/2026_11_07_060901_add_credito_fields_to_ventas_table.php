<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'monto_pagado')) {
                $table->decimal('monto_pagado', 10, 2)->default(0.00)->after('total');
            }
            if (!Schema::hasColumn('ventas', 'saldo_pendiente')) {
                $table->decimal('saldo_pendiente', 10, 2)->default(0.00)->after('monto_pagado');
            }
            if (!Schema::hasColumn('ventas', 'metodo_adelanto')) {
                $table->string('metodo_adelanto', 50)->nullable()->after('metodo_pago');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['monto_pagado', 'saldo_pendiente', 'metodo_adelanto']);
        });
    }
};
