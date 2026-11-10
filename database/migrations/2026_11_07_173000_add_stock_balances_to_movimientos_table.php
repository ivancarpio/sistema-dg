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
        Schema::table('movimientos', function (Blueprint $table) {
            if (!Schema::hasColumn('movimientos', 'stock_anterior')) {
                $table->integer('stock_anterior')->nullable()->after('cantidad');
            }
            if (!Schema::hasColumn('movimientos', 'stock_nuevo')) {
                $table->integer('stock_nuevo')->nullable()->after('stock_anterior');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            if (Schema::hasColumn('movimientos', 'stock_nuevo')) {
                $table->dropColumn('stock_nuevo');
            }
            if (Schema::hasColumn('movimientos', 'stock_anterior')) {
                $table->dropColumn('stock_anterior');
            }
        });
    }
};
