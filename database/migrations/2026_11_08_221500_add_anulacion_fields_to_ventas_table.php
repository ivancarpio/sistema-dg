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
        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'id_usuario_anulacion')) {
                $table->unsignedBigInteger('id_usuario_anulacion')->nullable()->after('estado');
            }
            if (!Schema::hasColumn('ventas', 'motivo_anulacion')) {
                $table->string('motivo_anulacion', 255)->nullable()->after('id_usuario_anulacion');
            }
            if (!Schema::hasColumn('ventas', 'fecha_anulacion')) {
                $table->dateTime('fecha_anulacion')->nullable()->after('motivo_anulacion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (Schema::hasColumn('ventas', 'fecha_anulacion')) {
                $table->dropColumn('fecha_anulacion');
            }
            if (Schema::hasColumn('ventas', 'motivo_anulacion')) {
                $table->dropColumn('motivo_anulacion');
            }
            if (Schema::hasColumn('ventas', 'id_usuario_anulacion')) {
                $table->dropColumn('id_usuario_anulacion');
            }
        });
    }
};
