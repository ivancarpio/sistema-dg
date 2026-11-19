<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'venta_origen_id')) {
                $table->integer('venta_origen_id')->nullable()->after('id');
                $table->foreign('venta_origen_id')->references('id')->on('ventas')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (Schema::hasColumn('ventas', 'venta_origen_id')) {
                $table->dropForeign(['venta_origen_id']);
                $table->dropColumn('venta_origen_id');
            }
        });
    }
};
